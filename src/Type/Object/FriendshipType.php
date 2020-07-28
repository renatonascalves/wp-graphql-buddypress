<?php
/**
 * Register the Friendship object type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;

/**
 * FriendshipType Class.
 */
class FriendshipType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Friendship';

	/**
	 * Register the friendship type and queries to the WPGraphQL schema.
	 */
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress Friendship.', 'wp-graphql-buddypress' ),
				'fields'            => [
					'id' => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the friendship.', 'wp-graphql-buddypress' ),
					],
					'friendshipId' => [
						'type'        => [ 'non_null' => 'Int' ],
						'description' => __( 'The id field that matches the BP_Friends_Friendship->id field.', 'wp-graphql-buddypress' ),
					],
					'initiator' => [
						'type'        => 'User',
						'description' => __( 'The initiator of the friendship.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Friendship $friendship, array $args, AppContext $context ) {
							return ! empty( $friendship->initiator )
								? $context->get_loader( 'user' )->load_deferred( $friendship->initiator )
								: null;
						},
					],
					'friend' => [
						'type'        => 'User',
						'description' => __( 'The friend, the one invited to the friendship.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Friendship $friendship, array $args, AppContext $context ) {
							return ! empty( $friendship->friend )
								? $context->get_loader( 'user' )->load_deferred( $friendship->friend )
								: null;
						},
					],
					'isConfirmed' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the friendship been confirmed or accepted.', 'wp-graphql-buddypress' ),
					],
					'dateCreated' => [
						'type'        => 'String',
						'description' => __( 'The date the friendship was created, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve_node'      => function( $node, $id, $type ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_friendship_object( $id );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof Friendship ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'friendshipBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Friendship object.', 'wp-graphql-buddypress' ),
				'args'        => [
					'id' => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'friendshipId' => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve' => function ( $source, array $args ) {

					// Require user to be logged in.
					if ( ! is_user_logged_in() ) {
						throw new UserError( __( 'Sorry, you need to be logged in to perform this action.', 'wp-graphql-buddypress' ) );
					}

					$friendship_id = 0;

					if ( ! empty( $args['id'] ) ) {
						$id_components = Relay::fromGlobalId( $args['id'] );

						if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
							throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
						}

						$friendship_id = $id_components['id'];
					} elseif ( ! empty( $args['friendshipId'] ) ) {
						$friendship_id = $args['friendshipId'];
					}

					$friendship = Factory::resolve_friendship_object( absint( $friendship_id ) );

					// Only the friendship initiator and the friend, the one invited to the friendship can see it.
					if ( ! empty( $friendship ) && ! in_array( bp_loggedin_user_id(), [ $friendship->initiator, $friendship->friend ], true ) ) {
						throw new UserError( __( 'Sorry, you don\'t have permission to see this friendship.', 'wp-graphql-buddypress' ) );
					}

					return $friendship;
				},
			]
		);
	}
}
