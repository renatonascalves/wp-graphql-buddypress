<?php
/**
 * XProfileFieldUpdate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldMutation;

/**
 * XProfileFieldUpdate Class.
 */
class XProfileFieldUpdate {

	/**
	 * Registers the XProfileFieldUpdate mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'updateXProfileField',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration.
	 *
	 * @return array
	 */
	public static function get_input_fields() {
		return [
			'id' => [
				'type' => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'fieldId'          => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_XProfile_Field->id field.', 'wp-graphql-buddypress' ),
			],
			'name'      => [
				'type'        => 'String',
				'description' => __( 'The name of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'description'      => [
				'type'        => 'String',
				'description' => __( 'The description of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'type'             => [
				'type'        => 'XProfileFieldTypesEnum',
				'description' => __( 'Type of XProfile field.', 'wp-graphql-buddypress' ),
			],
			'defaultVisibility'      => [
				'type'        => 'String',
				'description' => __( 'Default visibility for the profile field.', 'wp-graphql-buddypress' ),
			],
			'allowCustomVisibility'  => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to allow members to set the visibility for the profile field data or not.', 'wp-graphql-buddypress' ),
			],
			'doAutolink'             => [
				'type'        => 'Boolean',
				'description' => __( 'Autolink status for this profile field.', 'wp-graphql-buddypress' ),
			],
			'groupId'          => [
				'type'        => 'Int',
				'description' => __( 'The id of the group this field will assigned to.', 'wp-graphql-buddypress' ),
			],
			'parentId'          => [
				'type'        => 'Int',
				'description' => __( 'The id of the field this field will assigned to.', 'wp-graphql-buddypress' ),
			],
			'canDelete'      => [
				'type'        => 'Boolean',
				'description' => __( 'Option to allow XProfile field to be deleted.', 'wp-graphql-buddypress' ),
			],
			'isRequired'             => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the profile field must have a value.', 'wp-graphql-buddypress' ),
			],
			'isDefaultOption'    => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the option should be the default one for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'orderBy'            => [
				'type'        => 'OrderEnum',
				'description' => __( 'The way profile field\'s options are ordered.', 'wp-graphql-buddypress' ),
			],
			'optionOrder'        => [
				'type'        => 'Int',
				'description' => __( 'The order of the option into the profile field list of options.', 'wp-graphql-buddypress' ),
			],
			'fieldOrder'         => [
				'type'        => 'Int',
				'description' => __( 'The order of the XProfile field into the group of fields.', 'wp-graphql-buddypress' ),
			],
			'memberTypes'             => [
				'type'        => [
					'list_of' => 'MemberTypesEnum',
				],
				'description' => __( 'Sets the member types to which this field should be available.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'field' => [
				'type'        => 'XProfileField',
				'description' => __( 'The XProfile field that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_xprofile_field_object( absint( $payload['id'] ), $context );
				},
			],
		];
	}

	/**
	 * Defines the mutation closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function( $input, AppContext $context, ResolveInfo $info ) {

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError(
					__( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' )
				);
			}

			/**
			 * Get the XProfile field object.
			 */
			$xprofile_field_object = XProfileFieldMutation::get_xprofile_field_from_input( $input );

			/**
			 * Confirm if XProfile field exists.
			 */
			if ( empty( $xprofile_field_object->id ) ) {
				throw new UserError( __( 'This XProfile field does not exist.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Check if user can update a XProfile field.
			 */
			if ( false === XProfileFieldMutation::can_manage_xprofile_field() ) {
				throw new UserError( __( 'Sorry, you are not allowed to update this XProfile field.', 'wp-graphql-buddypress' ) );
			}

			// Specific check to make sure the Full Name xprofile field will remain undeletable.
			if ( bp_xprofile_fullname_field_id() === $xprofile_field_object->id ) {
				$input['canDelete'] = false;
			}

			/**
			 * Create XProfile field and return the ID.
			 */
			$xprofile_field_id = xprofile_insert_field(
				XProfileFieldMutation::prepare_xprofile_field_args( $input, $xprofile_field_object, 'update' )
			);

			/**
			 * Throw an exception if the XProfile field failed to be updated.
			 */
			if ( ! is_numeric( $xprofile_field_id ) ) {
				throw new UserError( __( 'Could not update XProfile field.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Save additional information.
			 */
			XProfileFieldMutation::set_additional_fields( $xprofile_field_id, $input );

			/**
			 * Fires after a XProfile field is updated.
			 *
			 * @param int         $xprofile_field_id The ID of the XProfile field being updated.
			 * @param array       $input             The input of the mutation.
			 * @param AppContext  $context           The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info              The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_fields_update_mutation', $xprofile_field_id, $input, $context, $info );

			/**
			 * Return the XProfile field ID.
			 */
			return [
				'id' => $xprofile_field_id,
			];
		};
	}
}
