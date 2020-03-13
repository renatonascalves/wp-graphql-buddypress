<?php
/**
 * FriendshipMutation Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use BP_Friends_Friendship;

/**
 * FriendshipMutation Class.
 */
class FriendshipMutation {

	/**
	 * Check if friendship exists.
	 *
	 * @param BP_Friends_Friendship $friendship Friendship object.
	 *
	 * @return bool
	 */
	public static function friendship_exists( $friendship ): bool {
		return ( $friendship instanceof BP_Friends_Friendship && ( 0 !== $friendship->id ?? 0 ) );
	}

	/**
	 * Check if user can manage friendship.
	 *
	 * Only the friendship initiator and the friend, the one invited to the friendship can see it.
	 *
	 * @param int $initiator Friendship initiator ID.
	 * @param int $friend    Friendship friend ID.
	 * @return bool
	 */
	public static function can_update_or_delete_friendship( $initiator, $friend ): bool {
		return in_array( bp_loggedin_user_id(), [ $initiator, $friend ], true );
	}
}
