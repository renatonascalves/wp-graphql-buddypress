<?php
/**
 * Registers XProfile Field Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class XProfileFieldConnection.
 */
class XProfileFieldConnection {

	/**
	 * Register connection from RootQuery to XProfile Groups.
	 */
	public static function register_connections(): void {

		// Connection for the XProfile Field options.
		register_graphql_connection(
			[
				'fromType'      => 'XProfileField',
				'toType'        => 'XProfileField',
				'fromFieldName' => 'options',
				'resolve'       => function ( $source, array $args, AppContext $context ) {
					return Factory::resolve_xprofile_field_options_connection( $source, $args, $context );
				},
			]
		);

		// Connection for the XProfile Group fields.
		register_graphql_connection(
			[
				'fromType'       => 'XProfileGroup',
				'toType'         => 'XProfileField',
				'fromFieldName'  => 'fields',
				'connectionArgs' => [
					'memberType'      => [
						'type'        => [ 'list_of' => 'MemberTypesEnum' ],
						'description' => __( 'Limit results set to certain member type(s).', 'wp-graphql-buddypress' ),
					],
					'hideEmptyFields' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether to hide XProfile fields where the user has no provided data.', 'wp-graphql-buddypress' ),
					],
					'userId'          => [
						'type'        => 'Int',
						'description' => __( 'User ID to get XProfile fields data.', 'wp-graphql-buddypress' ),
					],
					'excludeFields'   => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'Ensure result set excludes specific fields IDs.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_xprofile_fields_connection( $source, $args, $context, $info );
				},
			]
		);
	}
}
