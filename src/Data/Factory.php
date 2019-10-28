<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all the resolvers of queries and mutations.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\GroupsConnectionResolver;

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

	/**
	 * Wrapper for the GroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new GroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}
}
