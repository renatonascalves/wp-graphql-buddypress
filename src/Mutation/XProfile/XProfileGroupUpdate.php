<?php
/**
 * XProfileGroupUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\XProfile
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\XProfile;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupHelper;

/**
 * XProfileGroupUpdate Class.
 */
class XProfileGroupUpdate {

	/**
	 * Registers the XProfileGroupUpdate mutation.
	 */
	public static function register_mutation(): void {
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
	public static function get_input_fields(): array {
		return [
			'id'          => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'databaseId'  => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_XProfile_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'name'        => [
				'type'        => 'String',
				'description' => __( 'The name of the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'description' => [
				'type'        => 'String',
				'description' => __( 'The description of the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'canDelete'   => [
				'type'        => 'Boolean',
				'description' => __( 'Option to allow XProfile group to be deleted.', 'wp-graphql-buddypress' ),
			],
			'groupOrder'  => [
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
	public static function get_output_fields(): array {
		return [
			'group' => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The XProfile group that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
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
		return function ( array $input ) {

			// Get the XProfile group.
			$xprofile_group_object = XProfileGroupHelper::get_xprofile_group_from_input( $input );

			// Stop now if a user isn't allowed to update a XProfile group.
			if ( false === XProfileGroupHelper::can_manage_xprofile_group() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Update XProfile group and return the ID.
			$xprofile_group = xprofile_insert_field_group(
				XProfileGroupHelper::prepare_xprofile_group_args( $input, 'update', $xprofile_group_object )
			);

			// Throw an exception if the XProfile group failed to be updated.
			if ( false === $xprofile_group ) {
				throw new UserError( esc_html__( 'Cannot update existing XProfile field group.', 'wp-graphql-buddypress' ) );
			}

			// Set group id.
			$xprofile_group_id = $xprofile_group_object->id;

			// Update the position if the group_order exists.
			if ( isset( $input['groupOrder'] ) ) {
				xprofile_update_field_group_position( $xprofile_group_id, absint( $input['groupOrder'] ) );
			}

			// Return the XProfile group ID.
			return [
				'id' => $xprofile_group_id,
			];
		};
	}
}
