<?php
/**
 * Registers Blog Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class BlogConnection.
 */
class BlogConnection {

	/**
	 * Register connection from RootQuery -> Blog (type).
	 */
	public static function register_connections(): void {
		register_graphql_connection(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Blog',
				'fromFieldName'  => 'blogs',
				'connectionArgs' => [
					'type'    => [
						'type'        => 'BlogOrderTypeEnum',
						'description' => __( 'The order in which blogs should be returned.', 'wp-graphql-buddypress' ),
					],
					'search'  => [
						'type'        => 'String',
						'description' => __( 'Search term(s) to retrieve matching blogs for.', 'wp-graphql-buddypress' ),
					],
					'userId'  => [
						'type'        => 'Int',
						'description' => __( 'ID of the user whose blogs user can post to.', 'wp-graphql-buddypress' ),
					],
					'include' => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'Ensure result set includes Blogs with specific IDs.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_blogs_connection( $source, $args, $context, $info );
				},
			]
		);
	}
}
