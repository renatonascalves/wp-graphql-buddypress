<?php
/**
 * Register NotificationObjectUnion Union.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Union
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Union;

use WPGraphQL\Extensions\BuddyPress\Model\Activity;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Model\User;
use WPGraphQL\Registry\TypeRegistry;

/**
 * NotificationObjectUnion Class.
 */
class NotificationObjectUnion {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'NotificationObjectUnion';

	/**
	 * Register the NotificationObjectUnion Type
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry The Type Registry.
	 */
	public static function register( TypeRegistry $type_registry ): void {
		$type_names = [];

		if ( bp_is_active( 'activity' ) ) {
			$type_names[] = 'Activity';
		}

		if ( bp_is_active( 'groups' ) ) {
			$type_names[] = 'Group';
		}

		if ( bp_is_active( 'blogs' ) ) {
			$type_names[] = 'Blog';
		}

		if ( bp_is_active( 'members' ) ) {
			$type_names[] = 'User';
		}

		if ( ! empty( $type_names ) ) {
			register_graphql_union_type(
				self::$type_name,
				[
					'typeNames'   => $type_names,
					'description' => __( 'Union between the user, activity, group, blog types', 'wp-graphql-buddypress' ),
					'resolveType' => function ( $bp_object ) use ( $type_registry ) {
						$type = null;

						if ( $bp_object instanceof Activity ) {
							$type = $type_registry->get_type( 'Activity' );
						}

						if ( $bp_object instanceof Group ) {
							$type = $type_registry->get_type( 'Group' );
						}

						if ( $bp_object instanceof Blog ) {
							$type = $type_registry->get_type( 'Blog' );
						}

						if ( $bp_object instanceof User ) {
							$type = $type_registry->get_type( 'User' );
						}

						return $type;
					},
				]
			);
		}
	}
}
