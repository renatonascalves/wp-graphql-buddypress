<?php
/**
 * ActivityObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Activity;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use BP_Activity_Activity;

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

		if ( empty( $entry->id ) || ! $entry instanceof BP_Activity_Activity ) {
			return null;
		}

		return new Activity( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys/ids.
	 * @return BP_Activity_Activity[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_activities = [];

		// Get all objects.
		foreach ( $keys as $key ) {

			// This is cached.
			$loaded_activities[ $key ] = ActivityHelper::get_activity( absint( $key ) );
		}

		return $loaded_activities;
	}
}
