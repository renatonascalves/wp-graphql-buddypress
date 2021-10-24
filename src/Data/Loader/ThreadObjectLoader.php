<?php
/**
 * ThreadObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Thread;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;
use BP_Messages_Thread;

/**
 * Class ThreadObjectLoader
 */
class ThreadObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Thread
	 */
	protected function get_model( $entry, $key ): ?Thread {

		if ( empty( $entry ) || ! $entry instanceof BP_Messages_Thread ) {
			return null;
		}

		return new Thread( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return array
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_threads = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_threads[ $key ] = ThreadHelper::get_thread_from_input( [ 'threadId' => absint( $key ) ] );
		}

		return $loaded_threads;
	}
}