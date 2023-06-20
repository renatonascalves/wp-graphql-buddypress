<?php
/**
 * NotificationObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use BP_Notifications_Notification;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\NotificationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Notification;

/**
 * Class NotificationObjectLoader
 */
class NotificationObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Notification
	 */
	protected function get_model( $entry, $key ): ?Notification {

		if ( empty( $entry->id ) || ! $entry instanceof BP_Notifications_Notification ) {
			return null;
		}

		return new Notification( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return BP_Notifications_Notification[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_notifications = [];

		// Get all objects.
		foreach ( $keys as $key ) {

			// This is NOT cached.
			$loaded_notifications[ $key ] = NotificationHelper::get_notification_from_input( absint( $key ) );
		}

		return $loaded_notifications;
	}
}
