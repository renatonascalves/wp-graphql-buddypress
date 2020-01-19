<?php
/**
 * Register Friendship Connections.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * FriendshipConnection Class.
 */
class FriendshipConnection {

	/**
	 * Register connections to User.
	 */
	public static function register_connections() {

		/**
		 * Register connection from RootQuery to User.
		 */
		register_graphql_connection( self::get_connection_config() );
	}

	/**
	 * This returns a RootQuery > User connection config.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return array
	 */
	public static function get_connection_config( $args = [] ) {
		$defaults = [
			'fromType'       => 'User',
			'toType'         => 'Friendship',
			'fromFieldName'  => 'friends',
			'connectionArgs' => self::get_connection_args(),
			'resolveNode'    => function ( $id ) {
				return Factory::resolve_friendship_object( $id );
			},
			'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
				return Factory::resolve_friendship_connection( $source, $args, $context, $info );
			},
		];

		return array_merge( $defaults, $args );
	}

	/**
	 * This returns the connection args for the user connection.
	 *
	 * @return array
	 */
	public static function get_connection_args() {
		return [
			'isConfirmed' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the friendship has been accepted.', 'wp-graphql-buddypress' ),
			],
			'order' => [
				'type'        => 'OrderEnum',
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
