<?php
/**
 * Register Activity Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class ActivityConnection.
 */
class ActivityConnection {

	/**
	 * Register connections to Activity.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery to Activity.
		register_graphql_connection( self::get_connection_config() );

		// Register connection from User to Activity.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'User' ] ) );

		// Register connection from Blog/Site to Activity.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'Blog' ] ) );

		// Register connection from Group to Activity.
		register_graphql_connection( self::get_connection_config( [ 'fromType' => 'Group' ] ) );

		// Register connection from Activity to Activity.
		register_graphql_connection( self::get_activity_comments_connection_config() );
	}

	/**
	 * This returns a RootQuery > activity connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Activity',
				'fromFieldName'  => 'activities',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_activity_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns an Activity > Activity connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_activity_comments_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'Activity',
				'toType'         => 'Activity',
				'fromFieldName'  => 'comments',
				'connectionArgs' => [
					'order'  => [
						'type'        => 'OrderEnum',
						'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
					],
					'status' => [
						'type'        => 'ActivityOrderStatusEnum',
						'description' => __( 'Limit result set to items with a specific status.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_activity_comments_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns the connection args for the Activity connection.
	 *
	 * @return array
	 */
	public static function get_connection_args(): array {
		return [
			'search'          => [
				'type'        => 'String',
				'description' => __( 'Search term(s) to retrieve matching activities for.', 'wp-graphql-buddypress' ),
			],
			'displayComments' => [
				'type'        => 'Boolean',
				'description' => __( 'Display activity comments.', 'wp-graphql-buddypress' ),
			],
			'after'           => [
				'type'        => 'String',
				'description' => __( 'Limit result set to items published after a given ISO8601 compliant date.', 'wp-graphql-buddypress' ),
			],
			'exclude'         => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to items without specific IDs.', 'wp-graphql-buddypress' ),
			],
			'include'         => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Limit result set to items with specific IDs.', 'wp-graphql-buddypress' ),
			],
			'order'           => [
				'type'        => 'OrderEnum',
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
			],
			'status'          => [
				'type'        => 'ActivityOrderStatusEnum',
				'description' => __( 'Limit result set to items with a specific status.', 'wp-graphql-buddypress' ),
			],
			'type'            => [
				'type'        => [ 'list_of' => 'ActivityTypeEnum' ],
				'description' => __( 'Limit result set to items with one or more specific activity type.', 'wp-graphql-buddypress' ),
			],
			'component'       => [
				'type'        => 'ActivityComponentEnum',
				'description' => __( 'Limit result set to items with a specific active BuddyPress component.', 'wp-graphql-buddypress' ),
			],
			'scope'           => [
				'type'        => [ 'list_of' => 'ActivityOrderScopeEnum' ],
				'description' => __( 'Limit result set to items with one or more activity scope.', 'wp-graphql-buddypress' ),
			],
			'primaryId'       => [
				'type'        => 'Int',
				'description' => __( 'Limit result set to items with a specific prime association ID..', 'wp-graphql-buddypress' ),
			],
			'userId'          => [
				'type'        => 'Int',
				'description' => __( 'Limit result set to items created by a specific member (ID).', 'wp-graphql-buddypress' ),
			],
			'groupId'         => [
				'type'        => 'Int',
				'description' => __( 'Limit result set to items created within a specific group (ID).', 'wp-graphql-buddypress' ),
			],
			'siteId'          => [
				'type'        => 'Int',
				'description' => __( 'Limit result set to items attached to a specific site.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
