<?php
/**
 * Registers Members Connections
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Data\DataSource;

/**
 * Class MemberConnection
 */
class MemberConnection {

	/**
	 * Register connections to members
	 */
	public static function register_connections() {

		/**
		 * Register connection from RootQuery to members.
		 */
		register_graphql_connection( self::get_connection_config() );
	}

	/**
	 * Given an array of $args, this returns the connection config, merging the provided args
	 * with the defaults.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return array
	 */
	public static function get_connection_config( $args = [] ) {
		$defaults = [
			'fromType'           => 'RootQuery',
			'toType'             => 'User',
			'fromFieldName'      => 'members',
			'connectionTypeName' => 'RootQueryToMembersConnection',
			'connectionArgs' => self::get_connection_argsss(),
			'resolveNode'    => function ( $id, array $args, AppContext $context ) {
				return DataSource::resolve_user( $id, $context );
			},
			'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
				return Factory::resolve_members_connection( $source, $args, $context, $info );
			},
		];

		return array_merge( $defaults, $args );
	}

	/**
	 * This returns the connection args for the Members connection.
	 *
	 * @return array
	 */
	public static function get_connection_argsss() {
		return [
			'type'  => [
				'type'        => 'MemberOrderByTypeEnum',
				'description' => __( 'Shorthand for certain orderby/order combinations.', 'wp-graphql-buddypress' ),
			],
			'userId'      => [
				'type'        => 'Int',
				'description' => __( 'Limit results to friends of a user.', 'wp-graphql-buddypress' ),
			],
			'exclude'     => [
				'type'        => [
					'list_of' => 'Int',
				],
				'description' => __( 'Ensure result set excludes Members with specific IDs.', 'wp-graphql-buddypress' ),
			],
			'include'     => [
				'type'        => [
					'list_of' => 'Int',
				],
				'description' => __( 'Ensure result set includes Members with specific IDs.', 'wp-graphql-buddypress' ),
			],
			'memberType'     => [
				'type'        => [
					'list_of' => 'String',
				],
				'description' => __( 'Limit results set to certain type(s).', 'wp-graphql-buddypress' ),
			],
			'memberTypeIn'     => [
				'type'        => [
					'list_of' => 'String',
				],
				'description' => __( 'Limit results set to include certain member types.', 'wp-graphql-buddypress' ),
			],
			'memberTypeNotIn'     => [
				'type'        => [
					'list_of' => 'String',
				],
				'description' => __( 'Limit results set to exclude certain member types.', 'wp-graphql-buddypress' ),
			],
			'xprofile'   => [
				'type'        => 'String',
				'description' => __( 'Limit results set to a certain XProfile field.', 'wp-graphql-buddypress' ),
			],
			'search'      => [
				'type'        => 'String',
				'description' => __( 'Search term(s) to retrieve matching members.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
