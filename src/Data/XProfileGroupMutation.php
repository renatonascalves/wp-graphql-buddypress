<?php
/**
 * XProfileGroupMutation Class.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Data
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;

/**
 * XProfileGroupMutation Class.
 */
class XProfileGroupMutation {

	/**
	 * Get XProfile group ID helper.
	 *
	 * @throws UserError User error for invalid Relay ID.
	 *
	 * @param array $input Array of possible input fields.
	 *
	 * @return int
	 */
	public static function get_xprofile_group_id_from_input( $input ) {
		$xprofile_group_id = 0;

		/**
		 * Trying to get the XProfile group ID.
		 */
		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$xprofile_group_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['groupId'] ) ) {
			$xprofile_group_id = absint( $input['groupId'] );
		}

		return $xprofile_group_id;
	}

	/**
	 * Mapping XProfile group params.
	 *
	 * @param array       $input         The input for the mutation.
	 * @param object|null $xprofile_group XProfile Group object.
	 * @param string      $action        Hook action.
	 *
	 * @return array
	 */
	public static function prepare_xprofile_group_args( $input, $xprofile_group = null, $action ) {
		$output_args = [];

		// Setting the xprofile group description.
		if ( ! empty( $input['description'] ) ) {
			$output_args['description'] = $input['description'];
		} elseif ( ! empty( $xprofile_group->description ) ) {
			$output_args['description'] = $xprofile_group->description;
		} else {
			$output_args['description'] = null;
		}

		// Setting the xprofile group can_delete.
		if ( ! empty( $input['canDelete'] ) ) {
			$output_args['can_delete'] = $input['canDelete'];
		} elseif ( ! empty( $xprofile_group->can_delete ) ) {
			$output_args['can_delete'] = $xprofile_group->can_delete;
		} else {
			$output_args['can_delete'] = false;
		}

		// Setting the xprofile group name.
		if ( ! empty( $input['name'] ) ) {
			$output_args['name'] = $input['name'];
		} elseif ( ! empty( $xprofile_group->name ) ) {
			$output_args['name'] = $xprofile_group->name;
		} else {
			$output_args['name'] = '';
		}

		// Setting the xprofile group id.
		if ( ! empty( $input['field_group_id'] ) ) {
			$output_args['field_group_id'] = $input['field_group_id'];
		} elseif ( ! empty( $xprofile_group->id ) ) {
			$output_args['field_group_id'] = $xprofile_group->name;
		}

		/**
		 * Allows changing output args.
		 *
		 * @param array       $output_args    Mutation output args.
		 * @param array       $input          Mutation input args.
		 * @param object|null $xprofile_group  XProfile group object.
		 */
		return apply_filters( "bp_graphql_xprofile_field_groups_{$action}_mutation_args", $output_args, $input, $xprofile_group );
	}

	/**
	 * Check if user can manage XProfile group.
	 *
	 * @return bool
	 */
	public static function can_manage_xprofile_group() {
		return ( is_user_logged_in() && bp_current_user_can( 'bp_moderate' ) );
	}
}
