<?php
/**
 * GroupMutation Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_Groups_Group;

/**
 * GroupMutation Class.
 */
class GroupMutation {

	/**
	 * Get group ID helper.
	 *
	 * @throws UserError User error for invalid group.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return BP_Groups_Group
	 */
	public static function get_group_from_input( $input ): BP_Groups_Group {
		$group_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$group_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['slug'] ) ) {
			$group_id = groups_get_id( esc_html( $input['slug'] ) );
		} elseif ( ! empty( $input['groupId'] ) ) {
			$group_id = absint( $input['groupId'] );
		} elseif ( ! empty( $input ) && is_numeric( $input ) ) {
			$group_id = absint( $input );
		}

		$group = groups_get_group( $group_id );

		// Confirm if group exists.
		if ( empty( $group->id ) || ! $group instanceof BP_Groups_Group ) {
			throw new UserError( __( 'This group does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $group;
	}

	/**
	 * Mapping group params.
	 *
	 * @param array                 $input  The input for the mutation.
	 * @param \BP_Groups_Group|null $group  Group object.
	 * @param string                $action Hook action.
	 *
	 * @return array
	 */
	public static function prepare_group_args( array $input, $group = null, string $action ): array {
		$output_args = [
			'name'        => empty( $input['name'] )
				? $group->name ?? ''
				: $input['name'],
			'description' => empty( $input['description'] )
				? $group->description ?? ''
				: $input['description'],
			'creator_id'  => empty( $input['creatorId'] )
				? $group->creator_id ?? bp_loggedin_user_id()
				: $input['creatorId'],
			'parent_id'   => empty( $input['parentId'] )
				? $group->parent_id ?? null
				: $input['parentId'],
			'slug'        => empty( $input['slug'] )
				? $group->slug ? groups_check_slug( sanitize_title( esc_attr( $group->slug ) ) ) : false
				: $input['slug'],
			'status'      => empty( $input['status'] )
				? $group->status ?? null
				: $input['status'],
			'enable_forum' => empty( $input['hasForum'] )
				? $group->enable_forum ?? null
				: $input['hasForum'],
		];

		// Setting the group ID.
		if ( ! empty( $group->id ) ) {
			$output_args['group_id'] = $group->id;
		}

		/**
		 * Allows changing output args.
		 *
		 * @param array                $output_args Mutation output args.
		 * @param array                $input       Mutation input args.
		 * @param BP_Groups_Group|null $group       Group object.
		 */
		return apply_filters( "bp_graphql_groups_{$action}_mutation_args", $output_args, $input, $group );
	}

	/**
	 * Check if user can delete groups.
	 *
	 * @param int $creator_id Creator ID.
	 * @return bool
	 */
	public static function can_update_or_delete_group( $creator_id ): bool {
		return ( bp_current_user_can( 'bp_moderate' ) || absint( bp_loggedin_user_id() ) === absint( $creator_id ) );
	}
}
