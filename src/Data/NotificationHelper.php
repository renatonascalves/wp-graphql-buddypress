<?php
/**
 * NotificationHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_Notifications_Notification;

/**
 * NotificationHelper Class.
 */
class NotificationHelper {

	/**
	 * Get notification ID helper.
	 *
	 * @throws UserError User error for invalid notification.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return BP_Notifications_Notification
	 */
	public static function get_notification_from_input( $input ): BP_Notifications_Notification {
		$notification_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$notification_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['databaseId'] ) ) {
			$notification_id = absint( $input['databaseId'] );
		} elseif ( ! empty( $input['notificationId'] ) ) {
			$notification_id = absint( $input['notificationId'] );
		} elseif ( ! empty( $input ) && is_numeric( $input ) ) {
			$notification_id = absint( $input );
		}

		// This is not cached.
		$notification = bp_notifications_get_notification( absint( $notification_id ) );

		// Inexistent notification objects return the id being checked, so confirm another field is present.
		if ( empty( $notification->id ) || null === $notification->item_id ) {
			throw new UserError( __( 'This notification does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $notification;
	}

	/**
	 * Check if a notification exists.
	 *
	 * @param int $notification_id Notification ID.
	 * @return bool
	 */
	public static function notification_exists( int $notification_id ): bool {
		$notification = self::get_notification_from_input( absint( $notification_id ) );
		return ( $notification instanceof BP_Notifications_Notification && ! empty( $notification->id ) );
	}

	/**
	 * Can this user see the notification?
	 *
	 * @param int $notification_id Notification ID.
	 * @param int $user_id         User ID to check access.
	 * @return bool
	 */
	public static function can_see( $notification_id = 0, $user_id = 0 ): bool {

		if ( empty( $user_id ) ) {
			$user_id = bp_loggedin_user_id();
		}

		// Check notification access.
		if ( ! empty( $notification_id ) && (bool) BP_Notifications_Notification::check_access( $user_id, $notification_id ) ) {
			return true;
		}

		// Moderators as well.
		return bp_current_user_can( 'bp_moderate' );
	}
}
