<?php
/**
 * XProfileGroupHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use stdClass;

/**
 * XProfileGroupHelper Class.
 */
class XProfileGroupHelper {

	/**
	 * Get XProfile group helper.
	 *
	 * @throws UserError User error for invalid XProfile group.
	 *
	 * @param array|int $input Array of possible input fields, or an integer from a specific XProfile group.
	 * @return stdClass
	 */
	public static function get_xprofile_group_from_input( $input ): stdClass {
		$xprofile_group_id = 0;

		if ( is_int( $input ) ) {
			$id_type = 'integer';
		} elseif ( isset( $input['groupId'] ) ) {
			$id_type = 'group_id';
		} else {
			$id_type = $input['idType'] ?? 'global_id';
		}

		switch ( $id_type ) {
			case 'group_id':
				$xprofile_group_id = absint( $input['groupId'] );
				break;
			case 'database_id':
				$xprofile_group_id = absint( $input['id'] );
				break;
			case 'integer':
				$xprofile_group_id = absint( $input );
				break;
			case 'global_id':
			default:
				$id_components = Relay::fromGlobalId( $input['id'] );

				if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
					throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
				}

				$xprofile_group_id = absint( $id_components['id'] );
				break;
		}

		// Get group object.
		$xprofile_group_object = current( bp_xprofile_get_groups( [ 'profile_group_id' => $xprofile_group_id ] ) );

		// Confirm if it is a valid object.
		if ( empty( $xprofile_group_object ) || ! is_object( $xprofile_group_object ) ) {
			throw new UserError( __( 'This XProfile group does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $xprofile_group_object;
	}

	/**
	 * Mapping XProfile group params.
	 *
	 * @param array       $input          The input for the mutation.
	 * @param string      $action         Hook action.
	 * @param object|null $xprofile_group XProfile Group object.
	 * @return array
	 */
	public static function prepare_xprofile_group_args( array $input, string $action, $xprofile_group = null ): array {
		$output_args = [
			'name'           => empty( $input['name'] )
				? $xprofile_group->name ?? ''
				: $input['name'],
			'description'    => empty( $input['description'] )
				? $xprofile_group->description ?? null
				: $input['description'],
			'can_delete'     => empty( $input['canDelete'] )
				? $xprofile_group->can_delete ?? false
				: (bool) $input['canDelete'],
			'field_group_id' => empty( $input['field_group_id'] )
				? $xprofile_group->id ?? null
				: $input['field_group_id'],
		];

		/**
		 * Allows changing output args.
		 *
		 * @param array       $output_args    Mutation output args.
		 * @param array       $input          Mutation input args.
		 * @param object|null $xprofile_group XProfile group object.
		 */
		return apply_filters( "bp_graphql_xprofile_groups_{$action}_mutation_args", $output_args, $input, $xprofile_group );
	}

	/**
	 * Check if user can manage XProfile group.
	 *
	 * @return bool
	 */
	public static function can_manage_xprofile_group(): bool {
		return is_user_logged_in() && bp_current_user_can( 'bp_moderate' );
	}
}
