<?php
/**
 * XProfileFieldDelete Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldMutation;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

/**
 * XProfileFieldDelete Class.
 */
class XProfileFieldDelete {

	/**
	 * Registers the XProfileFieldDelete mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'deleteXProfileField',
			[
				'inputFields' => self::get_input_fields(),
				'outputFields' => self::get_output_fields(),
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
			'id' => [
				'type' => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'fieldId'          => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_XProfile_Field->id field.', 'wp-graphql-buddypress' ),
			],
			'deleteData'      => [
				'type'        => 'Boolean',
				'description' => __( 'Required if you want to delete the data for the field.', 'wp-graphql-buddypress' ),
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
			'deleted' => [
				'type' => 'Boolean',
				'description' => __( 'The status of the XProfile field deletion.', 'wp-graphql-buddypress' ),
				'resolve' => function ( $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'field' => [
				'type' => 'XProfileField',
				'description' => __( 'The deleted XProfile field object.', 'wp-graphql-buddypress' ),
				'resolve' => function ( $payload ) {
					return $payload['previousObject'] ? $payload['previousObject'] : null;
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
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
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
			 * Stop now if a user isn't allowed to delete a XProfile field.
			 */
			if ( false === XProfileFieldMutation::can_manage_xprofile_field() ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete XProfile field.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Get a XProfile field object before it is deleted.
			 */
			$previous_xprofile_field = new XProfileField( $xprofile_field_object );

			/**
			 * Trying to delete the XProfile field.
			 */
			if ( ! $xprofile_field_object->delete( $input['deleteData'] ?? false ) ) {
				throw new UserError( __( 'Could not delete the XProfile field.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a XProfile field is deleted.
			 *
			 * @param XProfileField $previous_xprofile_field The deleted XProfile field model object.
			 * @param array         $input                   The input of the mutation.
			 * @param AppContext    $context                 The AppContext passed down the resolve tree.
			 * @param ResolveInfo   $info                    The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_fields_delete_mutation', $previous_xprofile_field, $input, $context, $info );

			/**
			 * The deleted XProfile field and the previous XProfile field object.
			 */
			return [
				'deleted'        => true,
				'previousObject' => $previous_xprofile_field,
			];
		};
	}
}
