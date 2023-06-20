<?php
/**
 * GroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use BP_Groups_Group;

/**
 * Class GroupObjectLoader
 */
class GroupObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Group
	 */
	protected function get_model( $entry, $key ): ?Group {

		if ( empty( $entry ) || ! $entry instanceof BP_Groups_Group ) {
			return null;
		}

		return new Group( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return BP_Groups_Group[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		// Execute the query, and prune the cache.
		groups_get_groups(
			[
				'include'  => $keys,
				'per_page' => count( $keys ),
			]
		);

		$loaded_groups = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_groups[ $key ] = groups_get_group( absint( $key ) );
		}

		return $loaded_groups;
	}
}
