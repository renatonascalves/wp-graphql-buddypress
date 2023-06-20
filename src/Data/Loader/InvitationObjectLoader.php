<?php
/**
 * InvitationObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Invitation;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;
use BP_Invitation;

/**
 * Class InvitationObjectLoader
 */
class InvitationObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Invitation
	 */
	protected function get_model( $entry, $key ): ?Invitation {

		if ( empty( $entry->id ) || ! $entry instanceof BP_Invitation ) {
			return null;
		}

		return new Invitation( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys.
	 * @return BP_Invitation[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		$loaded_invitations = [];

		// Get all objects.
		foreach ( $keys as $key ) {

			// This is cached.
			$loaded_invitations[ $key ] = InvitationHelper::get_invitation( absint( $key ) );
		}

		return $loaded_invitations;
	}
}
