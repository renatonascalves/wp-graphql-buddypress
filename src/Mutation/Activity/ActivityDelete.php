<?php
/**
 * ActivityDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Activity
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Activity;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Activity;

/**
 * ActivityDelete Class.
 */
class ActivityDelete {

	/**
	 * Registers the ActivityDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteActivity',
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
				'description' => __( 'The globally unique identifier for the activity.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Activity_Activity->id field.', 'wp-graphql-buddypress' ),
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
			'deleted'  => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the activity deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'activity' => [
				'type'        => 'Activity',
				'description' => __( 'The deleted activity object.', 'wp-graphql-buddypress' ),
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

			// Check and get the activity.
			$activity = ActivityHelper::get_activity_from_input( $input );

			// Bail now if a user isn't allowed to delete an activity.
			if ( false === bp_activity_user_can_delete( $activity ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the Activity object before it is deleted.
			$previous_activity = new Activity( $activity );

			// Trying to delete the activity.
			if ( 'activity_comment' === $activity->type ) {
				$retval = bp_activity_delete_comment( $activity->item_id, $activity->id );
			} else {
				$retval = bp_activity_delete( [ 'id' => $activity->id ] );
			}

			if ( false === $retval ) {
				throw new UserError( esc_html__( 'Could not delete the activity.', 'wp-graphql-buddypress' ) );
			}

			// The deleted activity status and the previous activity object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_activity,
			];
		};
	}
}
