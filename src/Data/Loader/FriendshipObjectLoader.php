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
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * Note that order of returned values must match exactly the order of keys.
	 * If some entry is not available for given key - it must include null for the missing key.
	 *
	 * For example:
	 * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
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

		$loaded_friends = [];

		/**
		 * Loop over the keys and return an array of loaded_friends, where the key is the ID and the value
		 * is the Friendship object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the friendship object.
			$friendship = new BP_Friends_Friendship( absint( $key ) ); // This is cached.

			// Check if friendship exists.
			if ( false === FriendshipMutation::friendship_exists( $friendship ) ) {
				throw new UserError(
					sprintf(
						// translators: %d is the friendship ID.
						__( 'No Friendship was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			$loaded_friends[ $key ] = new Friendship( $friendship );
		}

		return $loaded_friends;
	}
}
