<?php
/**
 * XProfileFieldObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;
use BP_XProfile_Field;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldMutation;

/**
 * Class XProfileFieldObjectLoader
 */
class XProfileFieldObjectLoader extends AbstractDataLoader {

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @throws UserError User error.
	 *
	 * @param array $keys Array of keys.
	 * @return array
	 */
	public function loadKeys( array $keys = [] ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_xprofile_fields = [];

		// Get the user ID if available.
		$user_id = $this->context->config['userId'] ?? null;

		/**
		 * Loop over the keys and return an array of loaded_xprofile_fields, where the key is the ID and the value
		 * is the XProfile field object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the XPofile field object.
			$xprofile_field_object = XProfileFieldMutation::get_xprofile_field_from_input( absint( $key ), $user_id );

			// Pass object to our model.
			$loaded_xprofile_fields[ $key ] = new XProfileField( $xprofile_field_object );
		}

		return $loaded_xprofile_fields;
	}
}
