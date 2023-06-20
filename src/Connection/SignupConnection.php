<?php
/**
 * Register Signup Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class SignupConnection.
 */
class SignupConnection {

	/**
	 * Register connections to Signup.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery to Signup.
		register_graphql_connection( self::get_connection_config() );
	}

	/**
	 * This returns a RootQuery > Signup connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Signup',
				'fromFieldName'  => 'signups',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_signup_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns the connection args for the Signup connection.
	 *
	 * @return array
	 */
	public static function get_connection_args(): array {
		return [
			'search'        => [
				'type'        => 'String',
				'description' => __( 'Whether or not to search with a username.', 'wp-graphql-buddypress' ),
			],
			'activationKey' => [
				'type'        => 'String',
				'description' => __( 'Activation key to search for. If specified, all other parameters will be ignored.', 'wp-graphql-buddypress' ),
			],
			'active'        => [
				'type'        => 'Int',
				'description' => __( 'Limit result set to active items. Pass 0 for inactive signups, 1 for active (it is ignored, by default).', 'wp-graphql-buddypress' ),
			],
			'include'       => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to items with specific IDs.', 'wp-graphql-buddypress' ),
			],
			'order'         => [
				'type'        => 'OrderEnum',
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
			],
			'orderBy'       => [
				'type'        => 'SignupOrderByEnum',
				'description' => __( 'Order by a specific signup parameter.', 'wp-graphql-buddypress' ),
			],
			'userLogin'     => [
				'type'        => 'String',
				'description' => __( 'Limit result to a specific signup using user login.', 'wp-graphql-buddypress' ),
			],
			'userEmail'     => [
				'type'        => 'String',
				'description' => __( 'Limit result to a specific signup using user email.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
