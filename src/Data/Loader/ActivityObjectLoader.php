<?php
/**
 * ActivityObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Activity;
use stdClass;

/**
 * Class ActivityObjectLoader
 */
class ActivityObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Activity
	 */
	protected function get_model( $entry, $key ): ?Activity {

		if ( empty( $entry ) || ! $entry instanceof stdClass ) {
			return null;
		}

		return new Activity( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return array
	 */
	public function loadKeys( array $keys ): array {
		global $wpdb;

		if ( empty( $keys ) ) {
			return $keys;
		}

		$bp                = buddypress();
		$loaded_activities = [];

		// Get all objects.
		foreach ( $keys as $key ) {

			/**
			 * Currently, BuddyPress offers no way to get an activity ID directly.
			 *
			 * @todo Update with a better, cached, option.
			 */
			$activity = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->prepare( "SELECT * FROM {$bp->activity->table_name} WHERE id = %d", $key ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			);

			if ( empty( $activity[0] ) ) {
				continue;
			}

			$loaded_activities[ $key ] = $activity[0];
		}

		return $loaded_activities;
	}
}
