<?php
/**
 * XProfileGroupCreate Mutation.
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
 * XProfileGroupCreate Class.
 */
class XProfileGroupCreate {

	/**
	 * Registers the XProfileGroupCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createXProfileGroup',
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
			'name'        => [
				'type'        => [ 'non_null' => 'String' ],
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
		];
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'group' => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The XProfile group that was created.', 'wp-graphql-buddypress' ),
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
	 * Defines the mutation closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			// Check if user can create a XProfile group.
			if ( false === XProfileGroupHelper::can_manage_xprofile_group() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Create XProfile group.
			$xprofile_group_id = xprofile_insert_field_group(
				XProfileGroupHelper::prepare_xprofile_group_args( $input, 'create' )
			);

			// Throw an exception if the XProfile group failed to be created.
			if ( false === $xprofile_group_id ) {
				throw new UserError( esc_html__( 'Cannot create XProfile field group.', 'wp-graphql-buddypress' ) );
			}

			// Return the XProfile group ID.
			return [
				'id' => $xprofile_group_id,
			];
		};
	}
}
