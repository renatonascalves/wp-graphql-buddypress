<?php
/**
 * Register Friendship Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * FriendshipConnection Class.
 */
class FriendshipConnection {

	/**
	 * Register connection from User -> Friendship(s).
	 */
	public static function register_connections(): void {
		register_graphql_connection(
			[
				'fromType'       => 'User',
				'toType'         => 'Friendship',
				'fromFieldName'  => 'friends',
				'connectionArgs' => [
					'isConfirmed' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the friendship has been accepted.', 'wp-graphql-buddypress' ),
					],
					'order'       => [
						'type'        => 'OrderEnum',
						'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_friendship_connection( $source, $args, $context, $info );
				},
			]
		);
	}
}
