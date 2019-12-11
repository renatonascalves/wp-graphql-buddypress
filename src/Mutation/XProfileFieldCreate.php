<?php
/**
 * XProfileFieldCreate Mutation.
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
 * XProfileFieldCreate Class.
 */
class XProfileFieldCreate {

	/**
	 * Registers the XProfileFieldCreate mutation.
	 */
	public static function register_mutation() {
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
	public static function get_input_fields() {
		return [
			'name'      => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The name of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'description'      => [
				'type'        => 'String',
				'description' => __( 'The description of the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'groupId'          => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The id of the group this field will be assigned to.', 'wp-graphql-buddypress' ),
			],
			'type'             => [
				'type'        => [ 'non_null' => 'String' ],
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
			'parentId'          => [
				'type'        => 'Int',
				'description' => __( 'The id of the parent field this field will be assigned to.', 'wp-graphql-buddypress' ),
			],
			'canDelete'      => [
				'type'        => 'Boolean',
				'description' => __( 'Option to allow XProfile field to be deleted.', 'wp-graphql-buddypress' ),
			],
			'isRequired'             => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the profile field must have a value.', 'wp-graphql-buddypress' ),
			],
			'isDefaultOption'             => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the option should be the default one for the XProfile field.', 'wp-graphql-buddypress' ),
			],
			'orderBy'             => [
				'type'        => 'OrderEnum',
				'description' => __( 'The way profile field\'s options are ordered.', 'wp-graphql-buddypress' ),
			],
			'optionOrder'             => [
				'type'        => 'Int',
				'description' => __( 'The order of the option into the profile field list of options.', 'wp-graphql-buddypress' ),
			],
			'fieldOrder'             => [
				'type'        => 'Int',
				'description' => __( 'The order of the XProfile field into the group of fields.', 'wp-graphql-buddypress' ),
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
				'description' => __( 'The XProfile field that was created.', 'wp-graphql-buddypress' ),
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
			 * Check if user can create a XProfile field.
			 */
			if ( false === XProfileFieldMutation::can_manage_xprofile_field() ) {
				throw new UserError( __( 'Sorry, you are not allowed to create XProfile fields.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Create XProfile field and return the ID.
			 */
			$xprofile_field_id = xprofile_insert_field(
				XProfileFieldMutation::prepare_xprofile_field_args( $input, null, 'create' )
			);

			/**
			 * Throw an exception if the XProfile field failed to be created.
			 */
			if ( ! is_numeric( $xprofile_field_id ) ) {
				throw new UserError( __( 'Cannot create XProfile field field.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Save additional information.
			 */
			XProfileFieldMutation::set_additional_fields( $xprofile_field_id, $input );

			/**
			 * Fires after a XProfile field is created.
			 *
			 * @param int         $xprofile_field_id The ID of the XProfile field being created.
			 * @param array       $input            The input of the mutation.
			 * @param AppContext  $context          The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info             The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_fields_create_mutation', $xprofile_field_id, $input, $context, $info );

			/**
			 * Return the XProfile field ID.
			 */
			return [
				'id' => $xprofile_field_id,
			];
		};
	}
}
