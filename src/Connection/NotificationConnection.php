<?php
/**
 * Register Notification Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class NotificationConnection.
 */
class NotificationConnection {

	/**
	 * Register connections to Notification type.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery > Notification.
		register_graphql_connection( self::get_connection_config() );

		// Register connection from Group > Notification.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'Group' ] ) );

		// Register connection from User > Notification.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'User' ] ) );

		// Register connection from Blog > Notification.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'Blog' ] ) );
	}

	/**
	 * This returns a RootQuery > Notification connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Notification',
				'fromFieldName'  => 'notifications',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_notification_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns the connection args for the Notification connection.
	 *
	 * @return array
	 */
	public static function get_connection_args(): array {
		return [
			'isNew'            => [
				'type'        => 'Boolean',
				'description' => __( 'Limit result set to new items.', 'wp-graphql-buddypress' ),
			],
			'componentName'    => [
				'type'        => [ 'list_of' => 'NotificationComponentNamesEnum' ],
				'description' => __( 'Limit result set to notifications associated with a list of specific components.', 'wp-graphql-buddypress' ),
			],
			'componentAction'  => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Limit result set to notifications associated with a list of specific component\'s action names.', 'wp-graphql-buddypress' ),
			],
			'itemIds'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to notifications associated with a list of specific item IDs.', 'wp-graphql-buddypress' ),
			],
			'secondaryItemIds' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to notifications associated with a list of specific secondary item IDs.', 'wp-graphql-buddypress' ),
			],
			'userIds'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to notifications addressed to a list of specific users.', 'wp-graphql-buddypress' ),
			],
			'order'            => [
				'type'        => 'OrderEnum',
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
			],
			'orderBy'          => [
				'type'        => 'NotificationOrderByEnum',
				'description' => __( 'Order notifications by attribute.', 'wp-graphql-buddypress' ),
			],
			'search'           => [
				'type'        => 'String',
				'description' => __( 'Search term(s) to match against component_name or component_action fields.', 'wp-graphql-buddypress' ),
			],
			'include'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Ensure result set includes Notifications with specific IDs.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
