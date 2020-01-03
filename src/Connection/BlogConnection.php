<?php
/**
 * Registers Blog Connections.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.0.1-alpha
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
	 * Register connections to Blogs.
	 */
	public static function register_connections() {

		/**
		 * Register connection from RootQuery to blogs.
		 */
		register_graphql_connection( self::get_connection_config() );
	}

	/**
	 * This returns a RootQuery > blog connection config.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return array
	 */
	public static function get_connection_config( $args = [] ) {
		$defaults = [
			'fromType'       => 'RootQuery',
			'toType'         => 'Blog',
			'fromFieldName'  => 'blogs',
			'connectionArgs' => self::get_connection_args(),
			'resolveNode'    => function ( $id ) {
				return Factory::resolve_blog_object( $id );
			},
			'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
				return Factory::resolve_blogs_connection( $source, $args, $context, $info );
			},
		];

		return array_merge( $defaults, $args );
	}

	/**
	 * This returns the connection args for the Blogs connection.
	 *
	 * @return array
	 */
	public static function get_connection_args() {
		return [
			'type'        => [
				'type'        => 'BlogOrderTypeEnum',
				'description' => __( 'The order in which blogs should be returned.', 'wp-graphql-buddypress' ),
			],
			'search'      => [
				'type'        => 'String',
				'description' => __( 'Search term(s) to retrieve matching blogs for.', 'wp-graphql-buddypress' ),
			],
			'userId'      => [
				'type'        => 'Int',
				'description' => __( 'ID of the user whose blogs user can post to.', 'wp-graphql-buddypress' ),
			],
			'include'     => [
				'type'        => [
					'list_of' => 'Int',
				],
				'description' => __( 'Ensure result set includes Blogs with specific IDs.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
