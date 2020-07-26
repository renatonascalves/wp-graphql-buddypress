<?php
/**
 * GroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use BP_Groups_Group;

/**
 * Class GroupObjectLoader
 */
class GroupObjectLoader extends AbstractDataLoader {

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @throws UserError User error.
	 *
	 * @param array $keys Array of keys.
	 * @return array
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

		/**
		 * Loop over the keys and return an array of loaded_groups, where the key is the ID and the value
		 * is the group object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the group object from cache.
			$group_object = groups_get_group( absint( $key ) );

			if ( empty( $group_object ) || ! $group_object instanceof BP_Groups_Group ) {
				throw new UserError(
					sprintf(
						// translators: %d is the Group ID.
						__( 'No group was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			$loaded_groups[ $key ] = new Group( $group_object );
		}

		return $loaded_groups;
	}
}
