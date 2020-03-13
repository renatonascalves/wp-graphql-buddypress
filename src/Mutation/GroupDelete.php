<?php
/**
 * GroupDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\GroupMutation;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use BP_Groups_Group;

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
	public static function get_input_fields(): array {
		return [
			'id'          => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
			],
			'groupId'          => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'slug'         => [
				'type'        => 'String',
				'description' => __( 'Slug of the group.', 'wp-graphql-buddypress' ),
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
				'description' => __( 'The status of the group deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'group'   => [
				'type'        => 'Group',
				'description' => __( 'The deleted group object.', 'wp-graphql-buddypress' ),
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
		return function ( $input ) {

			// Throw an exception if there's no input.
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			// Get the group object.
			$group = GroupMutation::get_group_from_input( $input );

			// Confirm if group exists.
			if ( empty( $group->id ) || ! $group instanceof BP_Groups_Group ) {
				throw new UserError( __( 'This group does not exist.', 'wp-graphql-buddypress' ) );
			}

			// Stop now if a user isn't allowed to delete a group.
			if ( false === GroupMutation::can_update_or_delete_group( $group->creator_id ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete this group.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the Group object before it is deleted.
			$previous_group = new Group( $group );

			// Trying to delete the group.
			if ( false === groups_delete_group( $group->id ) ) {
				throw new UserError( __( 'Could not delete the group.', 'wp-graphql-buddypress' ) );
			}

			// The deleted group status and the previous group object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_group,
			];
		};
	}
}
