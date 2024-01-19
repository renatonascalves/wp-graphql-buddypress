<?php
/**
 * GroupDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Group
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Group;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\GroupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * GroupDelete Class.
 */
class GroupDelete {

	/**
	 * Registers the GroupDelete mutation.
	 */
	public static function register_mutation(): void {
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
			'id'         => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'slug'       => [
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
		return function ( array $input ) {

			// Get the group object.
			$group = GroupHelper::get_group_from_input( $input );

			// Stop now if a user isn't allowed to delete a group.
			if ( false === GroupHelper::can_update_or_delete_group( $group ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the Group object before it is deleted.
			$previous_group = new Group( $group );

			// Trying to delete the group.
			if ( false === groups_delete_group( $group->id ) ) {
				throw new UserError( esc_html__( 'Could not delete the group.', 'wp-graphql-buddypress' ) );
			}

			// The deleted group status and the previous group object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_group,
			];
		};
	}
}
