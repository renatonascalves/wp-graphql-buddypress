<?php
/**
 * GroupCreate Mutation.
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
 * GroupCreate Class.
 */
class GroupCreate {

	/**
	 * Registers the GroupCreate mutation.
	 */
	public static function register_mutation() {
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
	public static function get_input_fields() {
		return [
			'creatorId'      => [
				'type'        => 'Int',
				'description' => __( 'The userId to assign as the group creator.', 'wp-graphql-buddypress' ),
			],
			'parentId'      => [
				'type'        => 'Int',
				'description' => __( 'The ID of the parent group.', 'wp-graphql-buddypress' ),
			],
			'name'      => [
				'type'        => [ 'non_null' => 'String' ],
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
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'group' => [
				'type'        => 'Group',
				'description' => __( 'The group that was created.', 'wp-graphql-buddypress' ),
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
		return function( $input, AppContext $context, ResolveInfo $info ) {

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Check if user can create a group.
			 */
			if ( false === ( is_user_logged_in() && bp_user_can_create_groups() ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to create groups.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Create group and return the ID.
			 */
			$group_id = groups_create_group(
				GroupMutation::prepare_group_args( $input, null, 'create' )
			);

			/**
			 * Throw an exception if the group failed to be created.
			 */
			if ( ! is_numeric( $group_id ) ) {
				throw new UserError( __( 'Could not create Group.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a group is created.
			 *
			 * @param int         $group_id      The ID of the group being created.
			 * @param array       $input         The input of the mutation.
			 * @param AppContext  $context       The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info          The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_groups_create_mutation', $group_id, $input, $context, $info );

			/**
			 * Return the group ID.
			 */
			return [
				'id' => $group_id,
			];
		};
	}
}
