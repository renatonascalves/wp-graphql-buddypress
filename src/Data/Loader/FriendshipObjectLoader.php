<?php
/**
 * FriendshipObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipMutation;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;
use BP_Friends_Friendship;

/**
 * Class FriendshipObjectLoader
 */
class FriendshipObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object.
	 * @return null|Friendship
	 */
	protected function get_model( $entry, $key ): ?Friendship {

		// Check if friendship exists.
		if ( false === FriendshipMutation::friendship_exists( $entry ) ) {
			return null;
		}

		return new Friendship( $entry );
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

		$loaded_friends = [];

		// Get all objects and add them to cache.
		foreach ( $keys as $key ) {
			$loaded_friends[ $key ] = new BP_Friends_Friendship( absint( $key ) ); // This is cached.
		}

		return $loaded_friends;
	}
}
