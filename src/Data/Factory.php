<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all the resolvers.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\GroupsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\GroupMembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\MembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\XProfileFieldsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\Connection\XProfileGroupsConnectionResolver;

/**
 * Class Factory
 */
class Factory {

	/**
	 * Returns a Group object.
	 *
	 * @param int|null   $id      Group ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return Deferred|null
	 */
	public static function resolve_group_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$group_id = absint( $id );
		$context->getLoader( 'group_object' )->buffer( [ $group_id ] );

		return new Deferred(
			function () use ( $group_id, $context ) {
				return $context->getLoader( 'group_object' )->load( $group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Group object.
	 *
	 * @param int|null   $id      XProfile group ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return Deferred|null
	 */
	public static function resolve_xprofile_group_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$xprofile_group_id = absint( $id );
		$context->getLoader( 'xprofile_group_object' )->buffer( [ $xprofile_group_id ] );

		return new Deferred(
			function () use ( $xprofile_group_id, $context ) {
				return $context->getLoader( 'xprofile_group_object' )->load( $xprofile_group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Field object.
	 *
	 * @param int|null   $id      XProfile field ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return Deferred|null
	 */
	public static function resolve_xprofile_field_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$xprofile_field_id = absint( $id );
		$context->getLoader( 'xprofile_field_object' )->buffer( [ $xprofile_field_id ] );

		return new Deferred(
			function () use ( $xprofile_field_id, $context ) {
				return $context->getLoader( 'xprofile_field_object' )->load( $xprofile_field_id );
			}
		);
	}

	/**
	 * Wrapper for the XProfileGroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_xprofile_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new XProfileGroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileFieldsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_xprofile_fields_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new XProfileFieldsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
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

	/**
	 * Wrapper for the GroupMembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_group_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new GroupMembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the MembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new MembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}
}
