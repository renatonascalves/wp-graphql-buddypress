<?php
/**
 * GroupHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use BP_Groups_Group;

/**
 * GroupHelper Class.
 */
class GroupHelper {

	/**
	 * Get group ID helper.
	 *
	 * @throws UserError User error for invalid group.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return BP_Groups_Group
	 */
	public static function get_group_from_input( $input ): BP_Groups_Group {
		$group = groups_get_group( Factory::get_id( $input ) );

		// Confirm if group exists.
		if ( empty( $group->id ) || ! $group instanceof BP_Groups_Group ) {
			throw new UserError( esc_html__( 'This group does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $group;
	}

	/**
	 * Mapping group params.
	 *
	 * @param array                $input  The input for the mutation.
	 * @param string               $action Hook action.
	 * @param BP_Groups_Group|null $group  Group object.
	 * @return array
	 */
	public static function prepare_group_args( array $input, string $action, $group = null ): array {
		$mutation_args = [
			'name'         => empty( $input['name'] )
				? $group->name ?? ''
				: $input['name'],
			'description'  => empty( $input['description'] )
				? $group->description ?? ''
				: $input['description'],
			'creator_id'   => empty( $input['creatorId'] )
				? $group->creator_id ?? bp_loggedin_user_id()
				: $input['creatorId'],
			'parent_id'    => empty( $input['parentId'] )
				? $group->parent_id ?? null
				: $input['parentId'],
			'slug'         => empty( $input['slug'] )
				? $group->slug ?? null
				: groups_check_slug( sanitize_title( esc_attr( $input['slug'] ) ) ),
			'status'       => empty( $input['status'] )
				? $group->status ?? null
				: $input['status'],
			'enable_forum' => empty( $input['hasForum'] )
				? $group->enable_forum ?? null
				: $input['hasForum'],
		];

		// Setting the group ID.
		if ( ! empty( $group->id ) ) {
			$mutation_args['group_id'] = $group->id;
		}

		/**
		 * Allows updating mutation args.
		 *
		 * @param array                $mutation_args Mutation output args.
		 * @param array                $input         Mutation input args.
		 * @param BP_Groups_Group|null $group         Group object.
		 */
		return apply_filters( "bp_graphql_groups_{$action}_mutation_args", $mutation_args, $input, $group );
	}

	/**
	 * Check if user can update or delete groups.
	 *
	 * @param BP_Groups_Group $group Group object.
	 * @return bool
	 */
	public static function can_update_or_delete_group( BP_Groups_Group $group ): bool {
		return ( bp_current_user_can( 'bp_moderate' ) || groups_is_user_admin( bp_loggedin_user_id(), $group->id ) );
	}
}
