<?php
/**
 * Registers Members Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class MemberConnection.
 */
class MemberConnection {

	/**
	 * Register connections to Users.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery > User.
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'User',
				'fromFieldName'      => 'members',
				'connectionTypeName' => 'RootQueryToMembersConnection',
				'connectionArgs'     => [
					'type'            => [
						'type'        => 'MemberOrderByTypeEnum',
						'description' => __( 'Shorthand for certain orderby/order combinations.', 'wp-graphql-buddypress' ),
					],
					'userId'          => [
						'type'        => 'Int',
						'description' => __( 'Limit results to friends of a user with specific ID.', 'wp-graphql-buddypress' ),
					],
					'exclude'         => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'Ensure result set excludes Members with specific IDs.', 'wp-graphql-buddypress' ),
					],
					'include'         => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'Ensure result set includes Members with specific IDs.', 'wp-graphql-buddypress' ),
					],
					'memberType'      => [
						'type'        => [ 'list_of' => 'MemberTypesEnum' ],
						'description' => __( 'Limit result set to certain member type(s).', 'wp-graphql-buddypress' ),
					],
					'memberTypeNotIn' => [
						'type'        => [ 'list_of' => 'MemberTypesEnum' ],
						'description' => __( 'Limit result set excluding certain member type(s).', 'wp-graphql-buddypress' ),
					],
					'xprofile'        => [
						'type'        => 'String',
						'description' => __( 'Limit result set to a certain XProfile field.', 'wp-graphql-buddypress' ),
					],
					'search'          => [
						'type'        => 'String',
						'description' => __( 'Search term(s) to retrieve matching members.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'            => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_members_connection( $source, $args, $context, $info );
				},
			]
		);
	}
}
