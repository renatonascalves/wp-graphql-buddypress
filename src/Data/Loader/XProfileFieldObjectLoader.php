<?php
/**
 * XProfileFieldObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldHelper;
use BP_XProfile_Field;

/**
 * Class XProfileFieldObjectLoader
 */
class XProfileFieldObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|XProfileField
	 */
	protected function get_model( $entry, $key ): ?XProfileField {

		if ( empty( $entry ) || ! $entry instanceof BP_XProfile_Field ) {
			return null;
		}

		return new XProfileField( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys/ids.
	 * @return BP_XProfile_Field[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_xprofile_fields = [];

		// Get the user ID if available.
		$user_id = $this->context->config['userId'] ?? null;

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_xprofile_fields[ $key ] = XProfileFieldHelper::get_xprofile_field_from_input( absint( $key ), $user_id );
		}

		return $loaded_xprofile_fields;
	}
}
