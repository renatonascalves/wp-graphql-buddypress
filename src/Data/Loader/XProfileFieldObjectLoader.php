<?php
/**
 * XProfileFieldObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

/**
 * Class XProfileFieldObjectLoader
 */
class XProfileFieldObjectLoader extends AbstractDataLoader {

	/**
	 * Loaded XProfile field.
	 *
	 * @var array
	 */
	protected $loaded_fields = [];

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @throws UserError User error.
	 *
	 * @param array $keys Array of keys.
	 *
	 * @return array
	 */
	public function loadKeys( array $keys ) {

		if ( empty( $keys ) ) {
			return $keys;
		}

		/**
		 * Execute the query, and prune the cache.
		 */
		\BP_XProfile_Group::get_group_field_ids( $keys );

		/**
		 * Loop over the keys and return an array of loaded_fields, where the key is the ID and the value
		 * is the XProfile field object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			/**
			 * Get the XPofile field object.
			 */
			$xprofile_field_object = xprofile_get_field( absint( $key ) );

			if ( empty( $xprofile_field_object ) ) {
				throw new UserError(
					sprintf(
						// translators: XProfile Field ID.
						__( 'No XProfile field was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			/**
			 * Return the instance through the Model Layer to ensure we only return
			 * values the consumer has access to.
			 */
			$this->loaded_fields[ $key ] = new Deferred(
				function() use ( $xprofile_field_object ) {

					if ( ! $xprofile_field_object instanceof \BP_XProfile_Field ) {
						return null;
					}

					return new XProfileField( $xprofile_field_object );
				}
			);
		}

		return $this->loaded_fields;
	}
}
