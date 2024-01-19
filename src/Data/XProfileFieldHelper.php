<?php
/**
 * XProfileFieldHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_XProfile_Field;

/**
 * XProfileFieldHelper Class.
 */
class XProfileFieldHelper {

	/**
	 * Get XProfile field ID helper.
	 *
	 * @throws UserError  User error for invalid XProfile field.
	 *
	 * @param array|int $input   Array of possible input fields, or
	 *                           an integer from a specific XProfile field.
	 * @param int|null  $user_id User ID.
	 * @return BP_XProfile_Field
	 */
	public static function get_xprofile_field_from_input( $input, $user_id = null ): BP_XProfile_Field {
		$xprofile_field_id     = Factory::get_id( $input );
		$xprofile_field_object = xprofile_get_field( absint( $xprofile_field_id ), $user_id );

		if ( empty( $xprofile_field_object->id ) || ! $xprofile_field_object instanceof BP_XProfile_Field ) {
			throw new UserError( esc_html__( 'This XProfile field does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $xprofile_field_object;
	}

	/**
	 * Mapping XProfile field params.
	 *
	 * @param array                  $input          The input for the mutation.
	 * @param string                 $action         Hook action.
	 * @param BP_XProfile_Field|null $xprofile_field XProfile field object.
	 * @return array
	 */
	public static function prepare_xprofile_field_args( array $input, string $action, $xprofile_field = null ): array {
		$output_args = [
			'field_id'          => empty( $input['fieldId'] )
				? $xprofile_field->id ?? null
				: $input['fieldId'],
			'name'              => empty( $input['name'] )
				? $xprofile_field->name ?? ''
				: $input['name'],
			'description'       => empty( $input['description'] )
				? $xprofile_field->description ?? ''
				: $input['description'],
			'type'              => empty( $input['type'] )
				? $xprofile_field->type ?? ''
				: $input['type'],
			'field_group_id'    => empty( $input['groupId'] )
				? $xprofile_field->group_id ?? null
				: $input['groupId'],
			'parent_id'         => empty( $input['parentId'] )
				? $xprofile_field->parent_id ?? null
				: $input['parentId'],
			'can_delete'        => ! isset( $input['canDelete'] )
				? $xprofile_field->can_delete ?? true
				: (bool) $input['canDelete'],
			'is_required'       => ! isset( $input['isRequired'] )
				? $xprofile_field->is_required ?? false
				: (bool) $input['isRequired'],
			'is_default_option' => ! isset( $input['isDefaultOption'] )
				? $xprofile_field->is_default_option ?? false
				: (bool) $input['isDefaultOption'],
			'order_by'          => empty( $input['orderBy'] )
				? $xprofile_field->order_by ?? 'asc'
				: $input['orderBy'],
			'option_order'      => empty( $input['optionOrder'] )
				? $xprofile_field->option_order ?? null
				: $input['optionOrder'],
			'field_order'       => empty( $input['fieldOrder'] )
				? $xprofile_field->field_order ?? null
				: $input['fieldOrder'],
		];

		/**
		 * Allows changing output args.
		 *
		 * @param array                   $output_args    Mutation output args.
		 * @param array                   $input          Mutation input args.
		 * @param BP_XProfile_Field|null  $xprofile_field XProfile field object.
		 */
		return apply_filters( "bp_graphql_xprofile_fields_{$action}_mutation_args", $output_args, $input, $xprofile_field );
	}

	/**
	 * Set additional fields on update/creation.
	 *
	 * @param int   $xprofile_field_id Field ID.
	 * @param array $input             The input for the mutation.
	 */
	public static function set_additional_fields( int $xprofile_field_id, array $input ): void {

		// Setting/Updating member types if available.
		if ( ! empty( $input['memberTypes'] ) ) {

			// Append on update?!
			$append = isset( $input['fieldId'] ) || isset( $input['id'] );

			// Set types.
			( new BP_XProfile_Field( $xprofile_field_id ) )->set_member_types( stripslashes_deep( $input['memberTypes'] ), $append );
		}

		$default_visibility = $input['defaultVisibility'] ?? 'public';
		bp_xprofile_update_field_meta( $xprofile_field_id, 'default_visibility', $default_visibility );

		$allow_custom_visibility = ( isset( $input['allowCustomVisibility'] ) && true === $input['allowCustomVisibility'] ) ? 'allowed' : 'disabled';
		bp_xprofile_update_field_meta( $xprofile_field_id, 'allow_custom_visibility', $allow_custom_visibility );

		$do_autolink = ( isset( $input['doAutolink'] ) && true === $input['doAutolink'] ) ? 'on' : 'off';
		bp_xprofile_update_field_meta( $xprofile_field_id, 'do_autolink', $do_autolink );
	}

	/**
	 * Check if user can manage XProfile field.
	 *
	 * @return bool
	 */
	public static function can_manage_xprofile_field(): bool {
		return is_user_logged_in() && bp_current_user_can( 'bp_moderate' );
	}
}
