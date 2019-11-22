<?php
/**
 * GroupMutation Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Data
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;

/**
 * GroupMutation Class.
 */
class GroupMutation {

	/**
	 * Get group ID helper.
	 *
	 * @throws UserError User error for invalid Relay ID.
	 *
	 * @param array $input Array of possible input fields.
	 * @return int
	 */
	public static function get_group_id_from_input( $input ) {
		$group_id = 0;

		/**
		 * Trying to get the group ID.
		 */
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
		}

		return $group_id;
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
	public static function prepare_group_args( $input, $group = null, $action ) {
		$output_args = [];

		// Setting the group ID.
		if ( ! empty( $group->id ) ) {
			$output_args['group_id'] = $group->id;
		}

		// Setting parent ID.
		if ( ! empty( $input['parentId'] ) ) {
			$output_args['parent_id'] = $input['parentId'];
		} elseif ( ! empty( $group->parent_id ) ) {
			$output_args['parent_id'] = $group->parent_id;
		} else {
			$output_args['parent_id'] = null;
		}

		// Setting the creator.
		if ( ! empty( $input['creatorId'] ) ) {
			$output_args['creator_id'] = (int) $input['creatorId'];
		} elseif ( ! empty( $group->creator_id ) ) {
			$output_args['creator_id'] = (int) $group->creator_id;
		} else {
			$output_args['creator_id'] = bp_loggedin_user_id();
		}

		// Setting if has a forum enabled.
		if ( ! empty( $input['hasForum'] ) ) {
			$output_args['enable_forum'] = $input['hasForum'];
		} elseif ( ! empty( $group->enable_forum ) ) {
			$output_args['enable_forum'] = $group->enable_forum;
		} else {
			$output_args['enable_forum'] = null;
		}

		// Setting the group name.
		if ( ! empty( $input['name'] ) ) {
			$output_args['name'] = $input['name'];
		} elseif ( ! empty( $group->name ) ) {
			$output_args['name'] = $group->name;
		} else {
			$output_args['name'] = '';
		}

		// Setting the group description.
		if ( ! empty( $input['description'] ) ) {
			$output_args['description'] = $input['description'];
		} elseif ( ! empty( $group->description ) ) {
			$output_args['description'] = $group->description;
		} else {
			$output_args['description'] = '';
		}

		// Setting the group slug.
		if ( ! empty( $input['slug'] ) ) {
			$output_args['slug'] = $input['slug'];
		} elseif ( ! empty( $group->slug ) ) {
			$output_args['slug'] = groups_check_slug( sanitize_title( esc_attr( $group->slug ) ) );
		} else {
			$output_args['slug'] = '';
		}

		// Setting the group status.
		if ( ! empty( $input['status'] ) ) {
			$output_args['status'] = $input['status'];
		} elseif ( ! empty( $group->status ) ) {
			$output_args['status'] = $group->status;
		} else {
			$output_args['status'] = null;
		}

		/**
		 * Allows changing output args.
		 *
		 * @param array                $output_args Mutation output args.
		 * @param array                $input       Mutation input args.
		 * @param null|BP_Groups_Group $name        Group object.
		 */
		return apply_filters( "bp_graphql_groups_{$action}_mutation_args", $output_args, $input, $group );
	}

	/**
	 * Check if user can delete group.
	 *
	 * @param int $creator_id Creator ID.
	 * @return bool
	 */
	public static function can_update_or_delete_group( $creator_id ) {

		// Required logged in user.
		if ( false === is_user_logged_in() ) {
			return false;
		}

		return ( bp_current_user_can( 'bp_moderate' ) || absint( bp_loggedin_user_id() ) === absint( $creator_id ) );
	}
}
