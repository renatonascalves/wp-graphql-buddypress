<?php
/**
 * XProfileGroupCreate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupMutation;

/**
 * XProfileGroupCreate Class.
 */
class XProfileGroupCreate {

	/**
	 * Registers the XProfileGroupCreate mutation.
	 */
	public static function register_mutation() {
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
	public static function get_input_fields() {
		return [
			'name'      => [
				'type'        => [ 'non_null' => 'String' ],
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
		];
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'group' => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The XProfile group that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function( array $payload, array $args, AppContext $context ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
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
		return function( $input, AppContext $context, ResolveInfo $info ) {

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Check if user can create a XProfile group.
			 */
			if ( false === XProfileGroupMutation::can_manage_xprofile_group() ) {
				throw new UserError( __( 'Sorry, you are not allowed to create XProfile groups.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Create XProfile group and return the ID.
			 */
			$xprofile_group_id = xprofile_insert_field_group(
				XProfileGroupMutation::prepare_xprofile_group_args( $input, null, 'create' )
			);

			/**
			 * Throw an exception if the XProfile group failed to be created.
			 */
			if ( ! is_numeric( $xprofile_group_id ) ) {
				throw new UserError( __( 'Cannot create XProfile field group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a XProfile group is created.
			 *
			 * @param int         $xprofile_group_id The ID of the XProfile group being created.
			 * @param array       $input            The input of the mutation.
			 * @param AppContext  $context          The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info             The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_groups_create_mutation', $xprofile_group_id, $input, $context, $info );

			/**
			 * Return the XProfile group ID.
			 */
			return [
				'id' => $xprofile_group_id,
			];
		};
	}
}
