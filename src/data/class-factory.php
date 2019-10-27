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
	 *
	 * @param int|null   $id      Group ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return \BP_Groups_Group
	 */
	public static function resolve_group_object( $id, AppContext $context ) {

		// Get group.
		$group = groups_get_group( absint( $id ) );

		if ( empty( $group ) || empty( $group->id ) ) {
			throw new UserError( __( 'No group was found.', 'wp-graphql-buddypress' ) );
		}

		return $group;
	}
}
