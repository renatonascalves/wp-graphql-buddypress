<?php
/**
 * FriendshipHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;
use BP_Friends_Friendship;

/**
 * FriendshipHelper Class.
 */
class FriendshipHelper {

	/**
	 * Get friendship ID helper.
	 *
	 * @throws UserError User error for invalid friendship.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return Friendship
	 */
	public static function get_friendship_from_input( $input ): ?Friendship {
		$friendship_id = Factory::get_id( $input );
		$friendship    = Factory::resolve_friendship_object( absint( $friendship_id ) );

		// Only the friendship initiator and the friend, the one invited to the friendship can see it.
		if ( ! empty( $friendship )
			&& ! empty( $friendship->initiator )
			&& ! empty( $friendship->friend )
			&& ! in_array( bp_loggedin_user_id(), [ $friendship->initiator, $friendship->friend ], true ) ) {
			throw new UserError( esc_html__( 'Sorry, you don\'t have permission to see this friendship.', 'wp-graphql-buddypress' ) );
		}

		return $friendship;
	}

	/**
	 * Check if friendship exists.
	 *
	 * @param BP_Friends_Friendship|null $friendship Friendship object or nothing.
	 * @return bool
	 */
	public static function friendship_exists( $friendship ): bool {
		return ! empty( $friendship->id );
	}

	/**
	 * Check if user can manage friendship.
	 *
	 * Only the friendship initiator and the friend, the one invited to the friendship can see it.
	 *
	 * @param int $initiator_id Initiator ID.
	 * @param int $friend_id Friend ID.
	 * @return bool
	 */
	public static function can_update_or_delete_friendship( int $initiator_id, int $friend_id ): bool {
		return in_array( bp_loggedin_user_id(), [ $initiator_id, $friend_id ], true );
	}

	/**
	 * Check if user can create friendship.
	 *
	 * @param int $initiator_id Initiator ID.
	 * @param int $friend_id Friend ID.
	 * @return bool
	 */
	public static function can_create_friendship( int $initiator_id, int $friend_id ): bool {
		$is_moderator = bp_current_user_can( 'bp_moderate' );
		$logged_id    = bp_loggedin_user_id();

		/**
		 * - Only admins can create friendship requests for other people.
		 * - Admins can't create friendship requests to themselves from other people.
		 * - Users can't create friendship requests to themselves from other people.
		 */
		return (
			( $logged_id !== $initiator_id && ! $is_moderator )
			|| ( $logged_id === $friend_id && $is_moderator )
			|| ( ! in_array( $logged_id, [ $initiator_id, $friend_id ], true ) && ! $is_moderator )
		);
	}
}
