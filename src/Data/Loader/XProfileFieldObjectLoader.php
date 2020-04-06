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
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldMutation;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

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

		/**
		 * Loop over the keys and return an array of loaded_xprofile_fields, where the key is the ID and the value
		 * is the XProfile field object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the XPofile field object.
			$xprofile_field_object = XProfileFieldMutation::get_xprofile_field_from_input( absint( $key ) );

			// Confirm if it is a valid object.
			if ( empty( $xprofile_field_object ) || ! is_object( $xprofile_field_object ) ) {
				throw new UserError(
					sprintf(
						// translators: %d is the XProfile Field ID.
						__( 'No XProfile field was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			/**
			 * Return the instance through the Model Layer to ensure we only return
			 * values the consumer has access to.
			 */
			$loaded_xprofile_fields[ $key ] = new XProfileField( $xprofile_field_object );
		}

		return $loaded_xprofile_fields;
	}
}
