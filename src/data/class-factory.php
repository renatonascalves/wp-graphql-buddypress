<?php
/**
 * Factory
 *
 * This class serves as a factory for all the resolvers of queries and mutations.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;

/**
 * Class Factory
 */
class Factory {

	/**
	 * Returns a Group object.
	 *
	 * @throws UserError Error Exception.
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 *
	 * @return BP_Groups_Group object
	 */
	public static function resolve_group_object( $id, AppContext $context ) {

		// Bail early.
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		if ( ! bp_is_active( 'groups' ) ) {
			throw new UserError( __( 'The Groups component is not active.', 'wp-graphql-buddypress' ) );
		}

		$group = groups_get_group( $id );

		if ( empty( $group ) || empty( $group->id ) ) {
			throw new UserError(
				sprintf(
					// translators: group ID.
					__( 'No group was found with the ID: %d', 'wp-graphql-buddypress' ),
					absint( $id )
				)
			);
		}

		return $group;
	}
}
