<?php
/**
 * Register Group Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use WPGraphQL\Data\Connection\TermObjectConnectionResolver;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupConnection.
 */
class GroupConnection {

	/**
	 * Register connections to Groups.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery > Group.
		register_graphql_connection( self::get_connection_config() );

		// Register connection from Group > Group.
		register_graphql_connection(
			self::get_connection_config(
				[
					'fromType'      => 'Group',
					'fromFieldName' => 'childGroups',
				]
			)
		);

		// Register connection from Group > User.
		register_graphql_connection(
			[
				'fromType'       => 'Group',
				'toType'         => 'User',
				'fromFieldName'  => 'members',
				'connectionArgs' => self::get_group_members_connection_args(),
				'resolve'        => function ( Group $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_group_members_connection( $source, $args, $context, $info );
				},
			]
		);

		// Register connection from Group > GroupInvitation (Request|Invite).
		register_graphql_connection( self::get_group_invitations_connection_config() );

		// Register connection from User > GroupInvitation (Request|Invite).
		register_graphql_connection( self::get_group_invitations_connection_config( [ 'fromType' => 'User' ] ) );

		// Register connection from Group > GroupTypeTerm.
		register_graphql_connection(
			[
				'fromType'      => 'Group',
				'toType'        => 'GroupTypeTerm',
				'fromFieldName' => 'types',
				'queryClass'    => 'WP_Term_Query',
				'resolve'       => function ( Group $source, array $args, AppContext $context, ResolveInfo $info ) {
					// bp_groups_get_group_type is cached and it is not always up to date.
					// So fetch the term objects directly.

					$taxonomy_name = bp_get_group_type_tax_name();
					$raw_types     = bp_get_object_terms( $source->databaseId, $taxonomy_name );

					if ( empty( $raw_types ) ) {
						return null;
					}

					// Get and parse IDs.
					$ids = wp_parse_id_list( wp_list_pluck( $raw_types, 'term_id' ) );

					if ( empty( $ids ) ) {
						return null;
					}

					$resolver = new TermObjectConnectionResolver( $source, $args, $context, $info, $taxonomy_name );
					$resolver->set_query_arg( 'include', $ids );

					return $resolver->get_connection();
				},
			]
		);
	}

	/**
	 * This returns a RootQuery > Group connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Group',
				'fromFieldName'  => 'groups',
				'connectionArgs' => self::get_connection_args(),
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_groups_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns a Group|User > GroupInvitation (Request|Invite) connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_group_invitations_connection_config( array $args = [] ): array {
		$fields = [
			'userId' => [
				'type'        => 'Int',
				'description' => __( 'Return invitations from a specific user.', 'wp-graphql-buddypress' ),
			],
			'type'   => [
				'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
				'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
			],
		];

		if ( ! empty( $args['fromType'] ) && 'User' === $args['fromType'] ) {
			$fields = [
				'itemId' => [
					'type'        => 'Int',
					'description' => __( 'ID of the group to limit results to.', 'wp-graphql-buddypress' ),
				],
				'type'   => [
					'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
					'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
				],
			];
		}

		return array_merge(
			[
				'fromType'       => 'Group',
				'toType'         => 'GroupInvitation',
				'fromFieldName'  => 'invitations',
				'connectionArgs' => $fields,
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_group_invitations_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns the connection args for the Groups connection.
	 *
	 * @return array
	 */
	public static function get_connection_args(): array {
		return [
			'showHidden' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether results should include hidden Groups.', 'wp-graphql-buddypress' ),
			],
			'hasForum'   => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the Group has a forum enabled or not.', 'wp-graphql-buddypress' ),
			],
			'type'       => [
				'type'        => 'GroupOrderTypeEnum',
				'description' => __( 'Shorthand for certain orderby/order combinations.', 'wp-graphql-buddypress' ),
			],
			'order'      => [
				'type'        => 'OrderEnum',
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
			],
			'orderBy'    => [
				'type'        => 'GroupOrderByEnum',
				'description' => __( 'Order groups by attribute.', 'wp-graphql-buddypress' ),
			],
			'parent'     => [
				'type'        => 'Int',
				'description' => __( 'Parent ID of group to retrieve children of.', 'wp-graphql-buddypress' ),
			],
			'search'     => [
				'type'        => 'String',
				'description' => __( 'Search term(s) to retrieve matching groups for.', 'wp-graphql-buddypress' ),
			],
			'slug'       => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Search group or groups by its/their slug(s).', 'wp-graphql-buddypress' ),
			],
			'status'     => [
				'type'        => [ 'list_of' => 'GroupStatusEnum' ],
				'description' => __( 'Group statuses to limit results to.', 'wp-graphql-buddypress' ),
			],
			'groupType'  => [
				'type'        => 'GroupTypeEnum',
				'description' => __( 'Include groups of a given type.', 'wp-graphql-buddypress' ),
			],
			'userId'     => [
				'type'        => 'Int',
				'description' => __( 'Include groups that this user is a member of.', 'wp-graphql-buddypress' ),
			],
			'exclude'    => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Ensure result set excludes Groups with specific IDs.', 'wp-graphql-buddypress' ),
			],
			'include'    => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Ensure result set includes Groups with specific IDs.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * This returns the connection args for the Group members connection.
	 *
	 * @return array
	 */
	public static function get_group_members_connection_args(): array {
		return [
			'type'              => [
				'type'        => 'GroupMembersStatusTypeEnum',
				'description' => __( 'Sort the order of results by the status of the group members.', 'wp-graphql-buddypress' ),
			],
			'exclude'           => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Ensure result set excludes specific member IDs.', 'wp-graphql-buddypress' ),
			],
			'excludeBanned'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether results should exclude banned group members.', 'wp-graphql-buddypress' ),
			],
			'excludeAdminsMods' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether results should exclude group admins and mods.', 'wp-graphql-buddypress' ),
			],
			'groupMemberRoles'  => [
				'type'        => [ 'list_of' => 'GroupMemberRolesEnum' ],
				'description' => __( 'Ensure result set includes specific Group member roles.', 'wp-graphql-buddypress' ),
			],
		];
	}
}
