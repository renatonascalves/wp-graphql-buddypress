<?php
/**
 * XProfileGroupUpdate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupMutation;

/**
 * XProfileGroupUpdate Class.
 */
class XProfileGroupUpdate {

	/**
	 * Registers the XProfileGroupUpdate mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'updateXProfileGroup',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input fields.
	 *
	 * @return array
	 */
	public static function get_input_fields() {
		return [
			'id'          => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'groupId'          => [
				'type'        => 'int',
				'description' => __( 'The id field that matches the BP_XProfile_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'name'      => [
				'type'        => 'String',
				'description' => __( 'The name of the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'description'      => [
				'type'        => 'String',
				'description' => __( 'The description of the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'canDelete'      => [
				'type'        => 'Boolean',
				'description' => __( 'Option to allow XProfile group to be deleted.', 'wp-graphql-buddypress' ),
			],
			'groupOrder'      => [
				'type'        => 'Int',
				'description' => __( 'Order of the group relative to other groups.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'group' => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The XProfile group that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_xprofile_group_object( absint( $payload['id'] ), $context );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( $input, AppContext $context, ResolveInfo $info ) {

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError(
					__( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' )
				);
			}

			/**
			 * Get XProfile group ID from the input.
			 */
			$xprofile_group_id = XProfileGroupMutation::get_xprofile_group_id_from_input( $input );

			/**
			 * Get the XProfile group.
			 */
			$xprofile_group_object = current( bp_xprofile_get_groups( [ 'profile_group_id' => $xprofile_group_id ] ) );

			/**
			 * Confirm if XProfile group exists.
			 */
			if ( empty( $xprofile_group_object->id ) || ! is_object( $xprofile_group_object ) ) {
				throw new UserError( __( 'This XProfile group does not exist.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Stop now if a user isn't allowed to update a XProfile group.
			 */
			if ( false === XProfileGroupMutation::can_manage_xprofile_group() ) {
				throw new UserError( __( 'Sorry, you are not allowed to update this XProfile group.', 'wp-graphql-buddypress' ) );
			}

			// Setting the XProfile group id for update.
			$input['field_group_id'] = $xprofile_group_id;

			/**
			 * Update XProfile group and return the ID.
			 */
			$xprofile_group = xprofile_insert_field_group(
				XProfileGroupMutation::prepare_xprofile_group_args( $input, $xprofile_group_object, 'update' )
			);

			/**
			 * Throw an exception if the XProfile group failed to be updated.
			 */
			if ( ! $xprofile_group ) {
				throw new UserError( __( 'Cannot update existing XProfile field group.', 'wp-graphql-buddypress' ) );
			}

			// Update the position if the group_order exists.
			if ( isset( $input['groupOrder'] ) ) {
				xprofile_update_field_group_position( $xprofile_group_id, absint( $input['groupOrder'] ) );
			}

			/**
			 * Fires after a XProfile group is updated.
			 *
			 * @param int         $xprofile_group_id The ID of the XProfile group being updated.
			 * @param array       $input            The input of the mutation.
			 * @param AppContext  $context          The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info             The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_field_groups_update_mutation', $xprofile_group_id, $input, $context, $info );

			/**
			 * Return the XProfile group ID.
			 */
			return [
				'id' => $xprofile_group_id,
			];
		};
	}
}
