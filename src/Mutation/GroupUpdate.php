<?php
/**
 * GroupUpdate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\GroupMutation;

/**
 * GroupUpdate Class.
 */
class GroupUpdate {

	/**
	 * Registers the GroupUpdate mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'updateGroup',
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
			'creatorId'      => [
				'type'        => 'Int',
				'description' => __( 'The userId to assign as the group creator.', 'wp-graphql-buddypress' ),
			],
			'parentId'      => [
				'type'        => 'Int',
				'description' => __( 'The ID of the parent group.', 'wp-graphql-buddypress' ),
			],
			'name'      => [
				'type'        => 'String',
				'description' => __( 'The name of the group.', 'wp-graphql-buddypress' ),
			],
			'description'      => [
				'type'        => 'String',
				'description' => __( 'The description of the group.', 'wp-graphql-buddypress' ),
			],
			'slug'      => [
				'type'        => 'String',
				'description' => __( 'The slug of the group.', 'wp-graphql-buddypress' ),
			],
			'status'      => [
				'type'        => 'GroupStatusEnum',
				'description' => __( 'The status of the group.', 'wp-graphql-buddypress' ),
			],
			'hasForum'      => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the group has a forum enabled.', 'wp-graphql-buddypress' ),
			],
			'date'        => [
				'type'        => 'String',
				'description' => __( 'The date of the group.', 'wp-graphql-buddypress' ),
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
			'group' => [
				'type'        => 'Group',
				'description' => __( 'The group that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function( $payload, array $args, AppContext $context ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_group_object( absint( $payload['id'] ), $context );
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

			/**
			 * Get group ID.
			 */
			$group_id = GroupMutation::get_group_id_from_input( $input );

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
			 * Stop now if a user isn't allowed to update a group.
			 */
			if ( false === GroupMutation::can_update_or_delete_group( $group->creator_id ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete this group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Update group.
			 */
			$group_id = groups_create_group(
				GroupMutation::prepare_group_args( $input, $group, 'update' )
			);

			/**
			 * Throw an exception if the group failed to be updated.
			 */
			if ( ! is_numeric( $group_id ) ) {
				throw new UserError( __( 'Cannot update existing group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a group is updated.
			 *
			 * @param \BP_Groups_Group $group   The updated group.
			 * @param array            $input   The input of the mutation.
			 * @param AppContext       $context The AppContext passed down the resolve tree.
			 * @param ResolveInfo      $info    The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_groups_update_mutation', $group, $input, $context, $info );

			/**
			 * Return the group ID.
			 */
			return [
				'id' => $group_id,
			];
		};
	}
}
