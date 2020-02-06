<?php
/**
 * XProfileGroupObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupMutation;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;
use BP_XProfile_Group;

/**
 * Class XProfileGroupObjectLoader
 */
class XProfileGroupObjectLoader extends AbstractDataLoader {

	/**
	 * Loaded XProfile groups.
	 *
	 * @var array
	 */
	protected $loaded_xprofile_groups = [];

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
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		// Execute the query, and prune the cache.
		BP_XProfile_Group::get_group_ids();

		/**
		 * Loop over the keys and return an array of loaded_xprofile_groups, where the key is the ID and the value
		 * is the XProfile group object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the XPofile group object.
			$xprofile_group_object = XProfileGroupMutation::get_xprofile_group_from_input( absint( $key ) );

			// Confirm if it is a valid object.
			if ( empty( $xprofile_group_object ) || ! is_object( $xprofile_group_object ) ) {
				throw new UserError(
					sprintf(
						// translators: %d is the XProfile Group ID.
						__( 'No XProfile group was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			/**
			 * Return the instance through the Model Layer to ensure we only return
			 * values the consumer has access to.
			 */
			$this->loaded_xprofile_groups[ $key ] = new Deferred(
				function() use ( $xprofile_group_object ) {
					return new XProfileGroup( $xprofile_group_object );
				}
			);
		}

		return $this->loaded_xprofile_groups;
	}
}
