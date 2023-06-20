<?php
/**
 * SignupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Signup;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use BP_Signup;

/**
 * Class SignupObjectLoader
 */
class SignupObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Signup
	 */
	protected function get_model( $entry, $key ): ?Signup {

		if ( empty( $entry->id ) || ! $entry instanceof BP_Signup ) {
			return null;
		}

		return new Signup( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys/ids.
	 * @return BP_Signup[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_signups = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_signups[ $key ] = SignupHelper::get_signup_from_input( absint( $key ) );
		}

		return $loaded_signups;
	}
}
