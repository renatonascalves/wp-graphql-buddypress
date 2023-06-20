<?php
/**
 * XProfileGroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;
use BP_XProfile_Group;
use stdClass;

/**
 * Class XProfileGroupObjectLoader
 */
class XProfileGroupObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|XProfileGroup
	 */
	protected function get_model( $entry, $key ): ?XProfileGroup {

		if ( empty( $entry ) || ! is_object( $entry ) ) {
			return null;
		}

		return new XProfileGroup( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys/ids.
	 * @return stdClass[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		// Execute the query, and prune the cache.
		BP_XProfile_Group::get_group_ids();

		$loaded_xprofile_groups = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_xprofile_groups[ $key ] = XProfileGroupHelper::get_xprofile_group_from_input( absint( $key ) );
		}

		return $loaded_xprofile_groups;
	}
}
