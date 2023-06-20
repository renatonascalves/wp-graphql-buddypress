<?php
/**
 * MessageObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Message;
use BP_Messages_Message;

/**
 * Class MessageObjectLoader
 */
class MessageObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Message
	 */
	protected function get_model( $entry, $key ): ?Message {

		if ( ! $entry instanceof BP_Messages_Message || empty( $entry->id ) ) {
			return null;
		}

		return new Message( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return BP_Messages_Message[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_messages = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_messages[ $key ] = new BP_Messages_Message( absint( $key ) );
		}

		return $loaded_messages;
	}
}
