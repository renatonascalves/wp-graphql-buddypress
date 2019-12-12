<?php
/**
 * Registers XProfile Group Connections
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Model\User;

/**
 * Class XProfileGroupConnection
 */
class XProfileGroupConnection {

	/**
	 * Register connections to XProfile Groups.
	 */
	public static function register_connections() {

		/**
		 * Register connection from RootQuery to XProfile Groups.
		 */
		register_graphql_connection( self::get_connection_config() );

		/**
		 * Register connection from User to XProfile groups (and fields).
		 */
		register_graphql_connection(
			self::get_connection_config(
				[
					'fromType' => 'User',
					'toType'   => 'XProfileGroup',
				]
			)
		);
	}

	/**
	 * This returns a RootQuery > XProfileGroup connection config.
	 *
	 * @todo There is a bug where if one uses both connections, the userId is overlapped to other connections.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return array
	 */
	public static function get_connection_config( $args = [] ) {
		$defaults = [
			'fromType'       => 'RootQuery',
			'toType'         => 'XProfileGroup',
			'fromFieldName'  => 'xprofileGroups',
			'connectionArgs' => self::get_connection_args(),
			'resolveNode'    => function ( $group_id, array $args, AppContext $context ) {
				return Factory::resolve_xprofile_group_object( $group_id, $context );
			},
			'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
				if ( $source instanceof User ) {
					$context->config['userId'] = $source->userId;
				}

				return Factory::resolve_xprofile_groups_connection( $source, $args, $context, $info );
			},
		];

		return array_merge( $defaults, $args );
	}

	/**
	 * This returns the connection args for the XProfile Groups connection.
	 *
	 * @return array
	 */
	public static function get_connection_args() {
		return [
			'profileGroupId'  => [
				'type'        => 'Int',
				'description' => __( 'Limit results to a single XProfile group.', 'wp-graphql-buddypress' ),
			],
			'hideEmptyGroups'  => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to remove XProfile groups that do not have fields.', 'wp-graphql-buddypress' ),
			],
			'excludeGroups'  => [
				'type'        => [
					'list_of' => 'Int',
				],
				'description' => __( 'Ensure result set excludes specific XProfile field groups.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
