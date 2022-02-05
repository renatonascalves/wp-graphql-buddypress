<?php
/**
 * ActivityHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_Activity_Activity;

/**
 * ActivityHelper Class.
 */
class ActivityHelper {

	/**
	 * Get activity helper.
	 *
	 * @throws UserError User error for invalid activity.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return BP_Activity_Activity
	 */
	public static function get_activity_from_input( $input ): BP_Activity_Activity {
		$activity_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$activity_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['activityId'] ) ) {
			$activity_id = absint( $input['activityId'] );
		} elseif ( ! empty( $input ) && is_numeric( $input ) ) {
			$activity_id = absint( $input );
		}

		$activity = self::get_activity( $activity_id );

		// Confirm if activity exists.
		if ( empty( $activity->id ) || ! $activity instanceof BP_Activity_Activity ) {
			throw new UserError( __( 'This activity does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $activity;
	}

	/**
	 * Get activity.
	 *
	 * @param int $activity_id Activity ID.
	 * @return BP_Activity_Activity
	 */
	public static function get_activity( int $activity_id ): BP_Activity_Activity {
		return new BP_Activity_Activity( $activity_id );
	}

	/**
	 * Check if Activity exists.
	 *
	 * @param int $activity_id Activity ID.
	 * @return bool
	 */
	public static function activity_exists( int $activity_id ): bool {
		$activity = self::get_activity( absint( $activity_id ) );
		return ( $activity instanceof BP_Activity_Activity && ! empty( $activity->id ) );
	}
}
