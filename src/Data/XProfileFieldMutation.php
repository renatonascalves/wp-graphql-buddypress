<?php
/**
 * XProfileFieldMutation Class.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Data
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;

/**
 * XProfileFieldMutation Class.
 */
class XProfileFieldMutation {

	/**
	 * Get XProfile field ID helper.
	 *
	 * @throws UserError User error for invalid Relay ID.
	 *
	 * @param array $input Array of possible input fields.
	 *
	 * @return int
	 */
	public static function get_xprofile_field_id_from_input( $input ) {
		$xprofile_field_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$xprofile_field_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['fieldId'] ) ) {
			$xprofile_field_id = absint( $input['fieldId'] );
		}

		return $xprofile_field_id;
	}

	/**
	 * Mapping XProfile field params.
	 *
	 * @param array       $input          The input for the mutation.
	 * @param object|null $xprofile_field XProfile field object.
	 * @param string      $action         Hook action.
	 *
	 * @return array
	 */
	public static function prepare_xprofile_field_args( $input, $xprofile_field = null, $action ) {
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
			'can_delete'        => empty( $input['canDelete'] )
				? $xprofile_field->can_delete ?? true
				: $input['canDelete'],
			'is_required'       => empty( $input['isRequired'] )
				? $xprofile_field->is_required ?? false
				: $input['isRequired'],
			'is_default_option' => empty( $input['isDefaultOption'] )
				? $xprofile_field->is_default_option ?? false
				: $input['isDefaultOption'],
			'order_by'          => empty( $input['orderBy'] )
				? $xprofile_field->order_by ?? ''
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
		 * @param array       $output_args    Mutation output args.
		 * @param array       $input          Mutation input args.
		 * @param object|null $xprofile_field XProfile field object.
		 */
		return apply_filters( "bp_graphql_xprofile_fields_{$action}_mutation_args", $output_args, $input, $xprofile_field );
	}

	/**
	 * Check if user can manage XProfile field.
	 *
	 * @return bool
	 */
	public static function can_manage_xprofile_field() {
		return ( is_user_logged_in() && bp_current_user_can( 'bp_moderate' ) );
	}
}