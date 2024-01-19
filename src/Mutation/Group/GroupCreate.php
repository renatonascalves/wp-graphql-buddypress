<?php
/**
 * GroupCreate Mutation.
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
 * GroupCreate Class.
 */
class GroupCreate {

	/**
	 * Registers the GroupCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createGroup',
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
			'creatorId'   => [
				'type'        => 'Int',
				'description' => __( 'The userId to assign as the group creator.', 'wp-graphql-buddypress' ),
			],
			'parentId'    => [
				'type'        => 'Int',
				'description' => __( 'The ID of the parent group.', 'wp-graphql-buddypress' ),
			],
			'name'        => [
				'type'        => [ 'non_null' => 'String' ],
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
				'type'        => [
					'list_of' => 'GroupTypeEnum',
				],
				'description' => __( 'The type(s) of the group.', 'wp-graphql-buddypress' ),
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
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'group' => [
				'type'        => 'Group',
				'description' => __( 'The group object that was created.', 'wp-graphql-buddypress' ),
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

			// Check empty group name.
			if ( empty( $input['name'] ) ) {
				throw new UserError( esc_html__( 'Please, enter the name of the group.', 'wp-graphql-buddypress' ) );
			}

			// Check if user can create a group.
			if ( false === ( is_user_logged_in() && bp_user_can_create_groups() ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Create group and return its newly created ID.
			$group_id = (int) groups_create_group(
				GroupHelper::prepare_group_args( $input, 'create' )
			);

			// Throw an exception if the group failed to be created.
			if ( empty( $group_id ) ) {
				throw new UserError( esc_html__( 'Could not create Group.', 'wp-graphql-buddypress' ) );
			}

			// Set group type(s).
			if ( ! empty( $input['types'] ) ) {
				bp_groups_set_group_type( $group_id, $input['types'] );
			}

			// Return the group ID.
			return [
				'id' => $group_id,
			];
		};
	}
}
