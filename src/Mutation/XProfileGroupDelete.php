<?php
/**
 * XProfileGroupDelete Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupMutation;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;

/**
 * XProfileGroupDelete Class.
 */
class XProfileGroupDelete {

	/**
	 * Registers the XProfileGroupDelete mutation.
	 */
	public static function register_mutation() {
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
	public static function get_input_fields() {
		return [
			'id'      => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the XProfile group.', 'wp-graphql-buddypress' ),
			],
			'groupId' => [
				'type'        => 'int',
				'description' => __( 'The id field that matches the BP_XProfile_Group->id field.', 'wp-graphql-buddypress' ),
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
				'type'        => 'Boolean',
				'description' => __( 'The status of the XProfile group deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'group'   => [
				'type'        => 'XProfileGroup',
				'description' => __( 'The deleted XProfile group object.', 'wp-graphql-buddypress' ),
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
		return function ( $input, AppContext $context, ResolveInfo $info ) {

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Get the XProfile group object.
			 */
			$xprofile_group_object = XProfileGroupMutation::get_xprofile_group_from_input( $input );

			/**
			 * Confirm if XProfile group exists.
			 */
			if ( empty( $xprofile_group_object->id ) || ! is_object( $xprofile_group_object ) ) {
				throw new UserError( __( 'This XProfile group does not exist.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Stop now if a user isn't allowed to delete a XProfile group.
			 */
			if ( false === XProfileGroupMutation::can_manage_xprofile_group() ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete this XProfile group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Get the XProfile group object before it is deleted.
			 */
			$previous_xprofile_group = new XProfileGroup( $xprofile_group_object );

			/**
			 * Trying to delete the XProfile group.
			 */
			if ( ! xprofile_delete_field_group( $xprofile_group_object->id ) ) {
				throw new UserError( __( 'Could not delete the XProfile group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a XProfile group is deleted.
			 *
			 * @param XProfileGroup $previous_xprofile_group The deleted XProfile group model object.
			 * @param array          $input                  The input of the mutation.
			 * @param AppContext     $context                The AppContext passed down the resolve tree.
			 * @param ResolveInfo    $info                   The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_xprofile_groups_delete_mutation', $previous_xprofile_group, $input, $context, $info );

			/**
			 * The deleted XProfile group and the previous XProfile group object.
			 */
			return [
				'deleted'        => true,
				'previousObject' => $previous_xprofile_group,
			];
		};
	}
}
