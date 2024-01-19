<?php
/**
 * ActivityHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use BP_Activity_Activity;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * ActivityHelper Class.
 */
class ActivityHelper {

	/**
	 * Get activity helper.
	 *
	 * @throws UserError User error for invalid activity.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return BP_Activity_Activity
	 */
	public static function get_activity_from_input( $input ): BP_Activity_Activity {
		$activity = self::get_activity( Factory::get_id( $input ) );

		// Confirm if activity exists.
		if ( empty( $activity->id ) || ! $activity instanceof BP_Activity_Activity ) {
			throw new UserError( esc_html__( 'This activity does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $activity;
	}

	/**
	 * Mapping activity params.
	 *
	 * @param array                     $input    The input for the mutation.
	 * @param string                    $action   Hook action.
	 * @param BP_Activity_Activity|null $activity Activity object.
	 * @return array
	 */
	public static function prepare_activity_args( array $input, string $action, $activity = null ): array {
		$mutation_args = [
			'content'           => empty( $input['content'] )
				? $activity->content ?? ''
				: $input['content'],
			'user_id'           => empty( $input['userId'] )
				? get_current_user_id()
				: $input['userId'],
			'component'         => empty( $input['component'] )
				? buddypress()->activity->id ?? false
				: $input['component'],
			'type'              => empty( $input['type'] )
				? $activity->type ?? false
				: $input['type'],
			'secondary_item_id' => empty( $input['secondaryItemId'] )
				? $activity->secondary_item_id ?? false
				: $input['secondaryItemId'],
			'hide_sitewide'     => empty( $input['hidden'] )
				? $activity->hide_sitewide ?? false
				: $input['hidden'],
		];

		if ( ! empty( $activity->id ) && ! empty( $mutation_args['type'] && 'activity_comment' !== $mutation_args['type'] ) ) {
			$mutation_args['error_type'] = 'wp_error';
		}

		// Setting the activity ID.
		if ( ! empty( $activity->id ) ) {
			$mutation_args['id'] = $activity->id;
		}

		// Setting the Primary Item ID.
		if ( ! empty( $input['primaryItemId'] ) ) {
			$item_id = (int) $input['primaryItemId'];

			// Use a generic item ID.
			$mutation_args['item_id'] = $item_id;

			// Set the group ID, used in the `groups_post_update` helper function only.
			if (
				bp_is_active( 'groups' )
				&& ! empty( $mutation_args['component'] )
				&& buddypress()->groups->id === $mutation_args['component']
			) {
				$mutation_args['group_id'] = $item_id;
			}
		}

		/**
		 * Allows updating mutation args.
		 *
		 * @param array                     $mutation_args Mutation output args.
		 * @param array                     $input         Mutation input args.
		 * @param BP_Activity_Activity|null $activity      Activity object.
		 */
		return apply_filters( "bp_graphql_activity_{$action}_mutation_args", $mutation_args, $input, $activity );
	}

	/**
	 * Get activity.
	 *
	 * @param int $activity_id Activity ID.
	 * @return BP_Activity_Activity
	 */
	public static function get_activity( int $activity_id ): BP_Activity_Activity {
		return new BP_Activity_Activity( $activity_id );
	}

	/**
	 * Check if an activity exists.
	 *
	 * @param int $activity_id Activity ID.
	 * @return bool
	 */
	public static function activity_exists( int $activity_id ): bool {
		$activity = self::get_activity( absint( $activity_id ) );
		return ( $activity instanceof BP_Activity_Activity && ! empty( $activity->id ) );
	}

	/**
	 * Show hidden activity?
	 *
	 * @param  string $component The component the activity is from.
	 * @param  int    $item_id   The activity item ID.
	 * @return bool
	 */
	public static function show_hidden( string $component, int $item_id ): bool {
		$user_id = get_current_user_id();
		$retval  = false;

		if ( ! empty( $component ) ) {
			// If activity is from a group, do an extra cap check.
			if ( false === $retval && ! empty( $item_id ) && bp_is_active( $component ) && buddypress()->groups->id === $component ) {
				// Group admins and mods have access as well.
				if ( groups_is_user_admin( $user_id, $item_id ) || groups_is_user_mod( $user_id, $item_id ) ) {
					$retval = true;

					// User is a member of the group.
				} elseif ( (bool) groups_is_user_member( $user_id, $item_id ) ) {
					$retval = true;
				}
			}
		}

		// Moderators as well.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			$retval = true;
		}

		return $retval;
	}
}
