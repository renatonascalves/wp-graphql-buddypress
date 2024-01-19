<?php
/**
 * XProfileGroupDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\XProfile
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\XProfile;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;

/**
 * XProfileGroupDelete Class.
 */
class XProfileGroupDelete {

	/**
	 * Registers the XProfileGroupDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteXProfileGroup',
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
				'description' => __( 'The globally unique identifier for the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_XProfile_Group->id field.', 'wp-graphql-buddypress' ),
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
				'description' => __( 'The status of the XProfile group deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'group'   => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The deleted XProfile group object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
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

			// Get the XProfile group object.
			$xprofile_group_object = XProfileGroupHelper::get_xprofile_group_from_input( $input );

			// Stop now if a user isn't allowed to delete a XProfile group.
			if ( false === XProfileGroupHelper::can_manage_xprofile_group() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get the XProfile group object before it is deleted.
			$previous_xprofile_group = new XProfileGroup( $xprofile_group_object );

			// Trying to delete the XProfile group.
			if ( false === xprofile_delete_field_group( $xprofile_group_object->id ) ) {
				throw new UserError( esc_html__( 'Could not delete the XProfile group.', 'wp-graphql-buddypress' ) );
			}

			// The deleted XProfile group and the previous XProfile group object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_xprofile_group,
			];
		};
	}
}
