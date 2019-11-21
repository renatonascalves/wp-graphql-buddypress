<?php
/**
 * GroupDelete Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * GroupDelete Class.
 */
class GroupDelete {

	/**
	 * Registers the GroupDelete mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'deleteGroup',
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
			'id'          => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
			],
			'groupId'          => [
				'type'        => 'int',
				'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'slug'         => [
				'type'        => 'String',
				'description' => __( 'Current group slug.', 'wp-graphql-buddypress' ),
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
				'description' => __( 'The status of the group deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'group'   => [
				'type'        => 'Group',
				'description' => __( 'The deleted group object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( $payload ) {
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
				throw new UserError(
					__( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' )
				);
			}

			$group_id = 0;

			/**
			 * Trying to get the group ID.
			 */
			if ( ! empty( $input['id'] ) ) {
				$id_components = Relay::fromGlobalId( $input['id'] );

				if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
					throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
				}

				$group_id = absint( $id_components['id'] );
			} elseif ( ! empty( $input['slug'] ) ) {
				$group_id = groups_get_id( esc_html( $input['slug'] ) );
			} elseif ( ! empty( $input['groupId'] ) ) {
				$group_id = absint( $input['groupId'] );
			}

			/**
			 * Get the group.
			 */
			$group = groups_get_group( absint( $group_id ) );

			/**
			 * Confirm if group exists.
			 */
			if ( empty( $group->id ) || ! $group instanceof \BP_Groups_Group ) {
				throw new UserError( __( 'This group does not exist.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Stop now if a user isn't allowed to delete a group.
			 */
			if ( false === self::can_delete_group( $group->creator_id ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete this group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Get the group before it is deleted.
			 */
			$previous_group = new Group( $group );

			/**
			 * Trying to delete the group.
			 */
			if ( ! groups_delete_group( $group_id ) ) {
				throw new UserError( __( 'Could not delete the group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a group is deleted.
			 *
			 * @param Group       $previous_group The deleted group.
			 * @param array       $input          The input of the mutation.
			 * @param AppContext  $context        The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info           The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_groups_delete_mutation', $previous_group, $input, $context, $info );

			/**
			 * The deleted group and the previous group object.
			 */
			return [
				'deleted'        => true,
				'previousObject' => $previous_group,
			];
		};
	}

	/**
	 * Check if user can delete group.
	 *
	 * @param int $creator_id Creator ID.
	 * @return boolean
	 */
	protected static function can_delete_group( $creator_id ) {

		// Required logged in user.
		if ( false === is_user_logged_in() ) {
			return false;
		}

		return ( bp_current_user_can( 'bp_moderate' ) || absint( bp_loggedin_user_id() ) === absint( $creator_id ) );
	}
}
