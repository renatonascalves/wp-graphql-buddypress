<?php
/**
 * GroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupObjectLoader
 */
class GroupObjectLoader extends AbstractDataLoader {

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * Note that order of returned values must match exactly the order of keys.
	 * If some entry is not available for given key - it must include null for the missing key.
	 *
	 * For example:
	 * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
	 *
	 * @param array $keys Array of keys.
	 *
	 * @return array
	 */
	public function loadKeys( array $keys ) {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded = [];

		/**
		 * Execute the query, and prune the cache.
		 */
		groups_get_groups(
			[
				'include'  => $keys,
				'per_page' => count( $keys ),
				'fields'   => 'ids',
			]
		);

		/**
		 * Loop over the keys and return an array of loaded_groups, where the key is the ID and the value
		 * is the group object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			/**
			 * Get the group object.
			 */
			$group_object = groups_get_group( absint( $key ) );

			if ( empty( $group_object ) ) {
				throw new UserError(
					sprintf(
						// translators: Group ID.
						__( 'No item was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			/**
			 * Return the instance through the Model Layer to ensure we only return
			 * values the consumer has access to.
			 */
			$loaded[ $key ] = new Deferred(
				function() use ( $group_object ) {

					if ( ! $group_object instanceof \BP_Groups_Group ) {
						return null;
					}

					return new Group( $group_object );
				}
			);
		}

		return ! empty( $loaded ) ? $loaded : [];
	}
}
