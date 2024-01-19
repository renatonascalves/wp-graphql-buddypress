<?php
/**
 * XProfileFieldCreate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\XProfile
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\XProfile;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldHelper;

/**
 * XProfileFieldCreate Class.
 */
class XProfileFieldCreate {

	/**
	 * Registers the XProfileFieldCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createXProfileField',
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
	public static function get_input_fields(): array {
		return [
			'name'                  => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The name of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'description'           => [
				'type'        => 'String',
				'description' => __( 'The description of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'groupId'               => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The id of the group this field will be assigned to.', 'wp-graphql-buddypress' ),
			],
			'type'                  => [
				'type'        => [ 'non_null' => 'XProfileFieldTypesEnum' ],
				'description' => __( 'The type of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'defaultVisibility'     => [
				'type'        => 'String',
				'description' => __( 'The default visibility for the profile field.', 'wp-graphql-buddypress' ),
			],
			'allowCustomVisibility' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to allow members to set the visibility for the profile field data or not.', 'wp-graphql-buddypress' ),
			],
			'doAutolink'            => [
				'type'        => 'Boolean',
				'description' => __( 'Autolink status for this profile field.', 'wp-graphql-buddypress' ),
			],
			'parentId'              => [
				'type'        => 'Int',
				'description' => __( 'The id of the parent field this field will be assigned to.', 'wp-graphql-buddypress' ),
			],
			'canDelete'             => [
				'type'        => 'Boolean',
				'description' => __( 'Option to allow XProfile field to be deleted.', 'wp-graphql-buddypress' ),
			],
			'isRequired'            => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the profile field must have a value.', 'wp-graphql-buddypress' ),
			],
			'isDefaultOption'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the option should be the default one for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'orderBy'               => [
				'type'        => 'OrderEnum',
				'description' => __( 'The way profile field\'s options are ordered.', 'wp-graphql-buddypress' ),
			],
			'optionOrder'           => [
				'type'        => 'Int',
				'description' => __( 'The order of the option into the profile field list of options.', 'wp-graphql-buddypress' ),
			],
			'fieldOrder'            => [
				'type'        => 'Int',
				'description' => __( 'The order of the XProfile field into the group of fields.', 'wp-graphql-buddypress' ),
			],
			'memberTypes'           => [
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
	public static function get_output_fields(): array {
		return [
			'field' => [
				'type'        => 'XProfileField',
				'description' => __( 'The XProfile field that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
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
		return function ( array $input ) {

			// Check if user can create a XProfile field.
			if ( false === XProfileFieldHelper::can_manage_xprofile_field() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Create XProfile field and return its ID.
			$xprofile_field_id = xprofile_insert_field(
				XProfileFieldHelper::prepare_xprofile_field_args( $input, 'create' )
			);

			// Throw an exception if the XProfile field failed to be created.
			if ( ! $xprofile_field_id ) {
				throw new UserError( esc_html__( 'Could not create XProfile field.', 'wp-graphql-buddypress' ) );
			}

			// Save additional information.
			XProfileFieldHelper::set_additional_fields( $xprofile_field_id, $input );

			// Return the XProfile field ID.
			return [
				'id' => $xprofile_field_id,
			];
		};
	}
}
