<?php
/**
 * Register the Friendship object type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;

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
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress Friendship.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'fields'            => [
					'initiator'      => [
						'type'        => 'User',
						'description' => __( 'The initiator of the friendship.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Friendship $friendship, array $args, AppContext $context ) {
							return ! empty( $friendship->initiator )
								? $context->get_loader( 'user' )->load_deferred( $friendship->initiator )
								: null;
						},
					],
					'friend'         => [
						'type'        => 'User',
						'description' => __( 'The friend, the one invited to the friendship.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Friendship $friendship, array $args, AppContext $context ) {
							return ! empty( $friendship->friend )
								? $context->get_loader( 'user' )->load_deferred( $friendship->friend )
								: null;
						},
					],
					'isConfirmed'    => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the friendship been confirmed or accepted.', 'wp-graphql-buddypress' ),
					],
					'dateCreated'    => [
						'type'        => 'String',
						'description' => __( 'The date the friendship was created, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'dateCreatedGmt' => [
						'type'        => 'String',
						'description' => __( 'The date the friendship was created, as GMT.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve_node'      => function ( $node, $id, string $type ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_friendship_object( $id );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Friendship ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			strtolower( self::$type_name ),
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Friendship object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args ) {

					// Require user to be logged in.
					if ( ! is_user_logged_in() ) {
						throw new UserError( esc_html__( 'Sorry, you need to be logged in to perform this action.', 'wp-graphql-buddypress' ) );
					}

					return FriendshipHelper::get_friendship_from_input( $args );
				},
			]
		);
	}
}
