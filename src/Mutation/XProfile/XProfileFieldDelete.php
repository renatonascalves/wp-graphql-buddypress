<?php
/**
 * XProfileFieldDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\XProfile
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\XProfile;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldHelper;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

/**
 * XProfileFieldDelete Class.
 */
class XProfileFieldDelete {

	/**
	 * Registers the XProfileFieldDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteXProfileField',
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
			'id'         => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_XProfile_Field->id field.', 'wp-graphql-buddypress' ),
			],
			'deleteData' => [
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
	public static function get_output_fields(): array {
		return [
			'deleted' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the XProfile field deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'field'   => [
				'type'        => 'XProfileField',
				'description' => __( 'The deleted XProfile field object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( $payload ) {
					return $payload['previousObject'] ?? null;
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

			// Get the XProfile field object.
			$xprofile_field_object = XProfileFieldHelper::get_xprofile_field_from_input( $input );

			// Stop now if a user isn't allowed to delete a XProfile field.
			if ( false === XProfileFieldHelper::can_manage_xprofile_field() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get a XProfile field object before it is deleted.
			$previous_xprofile_field = new XProfileField( $xprofile_field_object );

			// Trying to delete the XProfile field.
			if ( false === $xprofile_field_object->delete( $input['deleteData'] ?? false ) ) {
				throw new UserError( esc_html__( 'Could not delete the XProfile field.', 'wp-graphql-buddypress' ) );
			}

			// The deleted XProfile field and the previous XProfile field object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_xprofile_field,
			];
		};
	}
}
