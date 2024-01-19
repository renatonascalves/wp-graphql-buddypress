<?php
/**
 * Register Signup object type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Signup;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;

/**
 * SignupType Class.
 */
class SignupType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Signup';

	/**
	 * Register the signup type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress Signup.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'eagerlyLoadType'   => true,
				'fields'            => [
					'userName'      => [
						'type'        => 'String',
						'description' => __( 'The new user\'s full name.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Signup $signup ) {

							// The user name is only available when the xProfile component is active.
							if ( false === bp_is_active( 'xprofile' ) ) {
								return null;
							}

							return $signup->userName ?? null;
						},
					],
					'userLogin'     => [
						'type'        => 'String',
						'description' => __( 'The username of the user the signup is for.', 'wp-graphql-buddypress' ),
					],
					'userEmail'     => [
						'type'        => 'String',
						'description' => __( 'The email for the user the signup is for.', 'wp-graphql-buddypress' ),
					],
					'registered'    => [
						'type'        => 'String',
						'description' => __( 'The registered date for the user, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'registeredGmt' => [
						'type'        => 'String',
						'description' => __( 'The registered date for the user, as GMT.', 'wp-graphql-buddypress' ),
					],
					'dateSent'      => [
						'type'        => 'String',
						'description' => __( 'The date the activation email was sent to the user, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'dateSentGmt'   => [
						'type'        => 'String',
						'description' => __( 'The date the activation email was sent to the user, as GMT.', 'wp-graphql-buddypress' ),
					],
					'active'        => [
						'type'        => 'Boolean',
						'description' => __( 'The status of the signup.', 'wp-graphql-buddypress' ),
					],
					'countSent'     => [
						'type'        => 'String',
						'description' => __( 'The number of times the activation email was sent to the user.', 'wp-graphql-buddypress' ),
					],
					'blog'          => [
						'type'        => 'Blog',
						'description' => __( 'Blog with some of the information.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Signup $signup ) {

							if ( empty( $signup->blog ) || false === is_multisite() ) {
								return null;
							}

							return new Blog( $signup->blog );
						},
					],
				],
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_signup_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Signup ) {
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
				'description' => __( 'Get a BuddyPress Signup object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {

					if ( false === SignupHelper::can_see() ) {
						return null;
					}

					$signup = SignupHelper::get_signup_from_input( $args );

					return Factory::resolve_signup_object( $signup->id, $context );
				},
			]
		);
	}
}
