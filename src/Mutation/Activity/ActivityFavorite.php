<?php
/**
 * ActivityFavorite Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Activity
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Activity;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * ActivityFavorite Class.
 */
class ActivityFavorite {

	/**
	 * Registers the ActivityFavorite mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'favoriteActivity',
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
			'activity' => [
				'type'        => 'Activity',
				'description' => __( 'The activity object.', 'wp-graphql-buddypress' ),
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

			$user_id = get_current_user_id();

			// Check and get the activity.
			$activity = ActivityHelper::get_activity_from_input( $input );

			// Bail now if a user isn't allowed to favorite an activity.
			if ( false === is_user_logged_in() || false === bp_activity_can_favorite() || false === bp_activity_user_can_read( $activity, $user_id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			$result         = false;
			$user_favorites = array_values( array_filter( wp_parse_id_list( bp_activity_get_user_favorites( $user_id ) ) ) );

			if ( in_array( $activity->id, $user_favorites, true ) ) {
				$result = bp_activity_remove_user_favorite( $activity->id, $user_id );
				$error  = __( 'Sorry, you cannot remove the activity from your favorites.', 'wp-graphql-buddypress' );
			} else {
				$result = bp_activity_add_user_favorite( $activity->id, $user_id );
				$error  = __( 'Sorry, you cannot add the activity to your favorites.', 'wp-graphql-buddypress' );
			}

			if ( false === $result ) {
				throw new UserError( esc_html( $error ) );
			}

			// Return the activity ID.
			return [
				'id' => $activity->id,
			];
		};
	}
}
