<?php
/**
 * ActivityUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Activity
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Activity;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;

/**
 * ActivityUpdate Class.
 */
class ActivityUpdate {

	/**
	 * Registers the ActivityUpdate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'updateActivity',
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
			'id'              => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the activity.', 'wp-graphql-buddypress' ),
			],
			'databaseId'      => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Activity_Activity->id field.', 'wp-graphql-buddypress' ),
			],
			'type'            => [
				'type'        => [ 'non_null' => 'ActivityTypeEnum' ],
				'description' => __( 'The type of the activity.', 'wp-graphql-buddypress' ),
			],
			'content'         => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The content of the activity.', 'wp-graphql-buddypress' ),
			],
			'component'       => [
				'type'        => 'ActivityComponentEnum',
				'description' => __( 'The active BuddyPress component name the activity relates to.', 'wp-graphql-buddypress' ),
			],
			'primaryItemId'   => [
				'type'        => 'Int',
				'description' => __( 'The ID of some other object primarily associated with this one.', 'wp-graphql-buddypress' ),
			],
			'secondaryItemId' => [
				'type'        => 'Int',
				'description' => __( 'The ID of some other object also associated with this one.', 'wp-graphql-buddypress' ),
			],
			'hidden'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the activity was sitewide hidden from streams or not.', 'wp-graphql-buddypress' ),
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
			'activity' => [
				'type'        => 'Activity',
				'description' => __( 'The activity that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_activity_object( absint( $payload['id'] ), $context );
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

			// Check and get the activity.
			$activity = ActivityHelper::get_activity_from_input( $input );

			// Bail now if a user isn't allowed to update an activity.
			if ( false === bp_activity_user_can_delete( $activity ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Check empty activity content.
			if ( empty( $input['content'] ) ) {
				throw new UserError( esc_html__( 'Please, enter the content of the activity.', 'wp-graphql-buddypress' ) );
			}

			// Update activity.
			$activity_id = bp_activity_add(
				ActivityHelper::prepare_activity_args( $input, 'update', $activity )
			);

			// Throw an exception if the activity failed to be updated.
			if ( empty( $activity_id ) ) {
				throw new UserError( esc_html__( 'Could not update existing activity.', 'wp-graphql-buddypress' ) );
			}

			// Return the activity ID.
			return [
				'id' => $activity_id,
			];
		};
	}
}
