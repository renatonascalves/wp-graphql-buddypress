<?php
/**
 * GroupUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Group
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Group;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\GroupHelper;

/**
 * GroupUpdate Class.
 */
class GroupUpdate {

	/**
	 * Registers the GroupUpdate mutation.
	 */
	public static function register_mutation(): void {
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
	public static function get_input_fields(): array {
		return [
			'id'          => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
			],
			'databaseId'  => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
			],
			'creatorId'   => [
				'type'        => 'Int',
				'description' => __( 'The userId to assign as the group creator.', 'wp-graphql-buddypress' ),
			],
			'parentId'    => [
				'type'        => 'Int',
				'description' => __( 'The ID of the parent group.', 'wp-graphql-buddypress' ),
			],
			'name'        => [
				'type'        => 'String',
				'description' => __( 'The name of the group.', 'wp-graphql-buddypress' ),
			],
			'description' => [
				'type'        => 'String',
				'description' => __( 'The description of the group.', 'wp-graphql-buddypress' ),
			],
			'slug'        => [
				'type'        => 'String',
				'description' => __( 'The slug of the group.', 'wp-graphql-buddypress' ),
			],
			'status'      => [
				'type'        => 'GroupStatusEnum',
				'description' => __( 'The status of the group.', 'wp-graphql-buddypress' ),
			],
			'types'       => [
				'type'        => [ 'list_of' => 'GroupTypeEnum' ],
				'description' => __( 'The type(s) of the group.', 'wp-graphql-buddypress' ),
			],
			'appendTypes' => [
				'type'        => [ 'list_of' => 'GroupTypeEnum' ],
				'description' => __( 'The type(s) of the group to append.', 'wp-graphql-buddypress' ),
			],
			'removeTypes' => [
				'type'        => [ 'list_of' => 'GroupTypeEnum' ],
				'description' => __( 'The type(s) of the group to remove.', 'wp-graphql-buddypress' ),
			],
			'hasForum'    => [
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
	public static function get_output_fields(): array {
		return [
			'group' => [
				'type'        => 'Group',
				'description' => __( 'The group that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
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
		return function ( array $input ) {

			// Get the group.
			$group = GroupHelper::get_group_from_input( $input );

			// Stop now if a user isn't allowed to update a group.
			if ( false === GroupHelper::can_update_or_delete_group( $group ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Update group.
			$group_id = groups_create_group(
				GroupHelper::prepare_group_args( $input, 'update', $group )
			);

			// Throw an exception if the group failed to be updated.
			if ( empty( $group_id ) ) {
				throw new UserError( esc_html__( 'Could not update existing group.', 'wp-graphql-buddypress' ) );
			}

			// Add group type(s).
			if ( ! empty( $input['types'] ) ) {
				bp_groups_set_group_type( $group_id, $input['types'] );
			}

			// Append group type(s).
			if ( ! empty( $input['appendTypes'] ) ) {
				bp_groups_set_group_type( $group_id, $input['appendTypes'], true );
			}

			// Remove group type(s).
			if ( ! empty( $input['removeTypes'] ) && ! empty( bp_groups_get_group_type( $group_id, false ) ) ) {
				array_map(
					function ( $type ) use ( $group_id ) {
						bp_groups_remove_group_type( $group_id, $type );
					},
					(array) $input['removeTypes']
				);
			}

			// Return the group ID.
			return [
				'id' => $group_id,
			];
		};
	}
}
