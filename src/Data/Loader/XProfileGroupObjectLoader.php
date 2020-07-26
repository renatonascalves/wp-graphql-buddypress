<?php
/**
 * XProfileGroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupMutation;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;
use BP_XProfile_Group;

/**
 * Class XProfileGroupObjectLoader
 */
class XProfileGroupObjectLoader extends AbstractDataLoader {

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

		// Execute the query, and prune the cache.
		BP_XProfile_Group::get_group_ids();

		$loaded_xprofile_groups = [];

		/**
		 * Loop over the keys and return an array of loaded_xprofile_groups, where the key is the ID and the value
		 * is the XProfile group object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the XPofile group object.
			$xprofile_group_object = XProfileGroupMutation::get_xprofile_group_from_input( absint( $key ) );

			// Pass object to our model.
			$loaded_xprofile_groups[ $key ] = new XProfileGroup( $xprofile_group_object );
		}

		return $loaded_xprofile_groups;
	}
}
