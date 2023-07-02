<?php
/**
 * Group Members Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * GroupMembersEnums Class
 */
class GroupMembersEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {
		$values = [
			'LAST_JOINED'  => [
				'name'        => 'LAST_JOINED',
				'description' => __( 'Used to order group last joined members.', 'wp-graphql-buddypress' ),
				'value'       => 'last_joined',
			],
			'FIRST_JOINED' => [
				'name'        => 'FIRST_JOINED',
				'description' => __( 'Used to order group first joined members.', 'wp-graphql-buddypress' ),
				'value'       => 'first_joined',
			],
			'ALPHABETICAL' => [
				'name'        => 'ALPHABETICAL',
				'description' => __( 'Used to order group members alphabetically.', 'wp-graphql-buddypress' ),
				'value'       => 'alphabetical',
			],
		];

		if ( bp_is_active( 'activity' ) ) {
			$values['GROUP_ACTIVITY'] = [
				'name'        => 'GROUP_ACTIVITY',
				'description' => __( 'Used to order by group members activity.', 'wp-graphql-buddypress' ),
				'value'       => 'group_activity',
			];
		}

		// Group Members Status Type Enum.
		register_graphql_enum_type(
			'GroupMembersStatusTypeEnum',
			[
				'description' => __( 'Sort the order of results by the status of the group members.', 'wp-graphql-buddypress' ),
				'values'      => $values,
			]
		);

		// Group Roles.
		self::group_member_roles();
	}

	/**
	 * Group Member Roles.
	 */
	public static function group_member_roles(): void {
		$group_member_roles_enum_values = [];
		$roles                          = bp_groups_get_group_roles();

		foreach ( $roles as $role ) {
			$group_member_roles_enum_values[ WPEnumType::get_safe_name( $role->id ) ] = [
				'description' => sprintf(
					/* translators: %1$s: group member role */
					__( 'Group member role: %1$s', 'wp-graphql-buddypress' ),
					$role->name
				),
				'value'       => $role->id,
			];
		}

		// Group Roles.
		register_graphql_enum_type(
			'GroupMemberRolesEnum',
			[
				'description' => __( 'Ensure result set includes specific Group member roles.', 'wp-graphql-buddypress' ),
				'values'      => $group_member_roles_enum_values,
			]
		);
	}
}
