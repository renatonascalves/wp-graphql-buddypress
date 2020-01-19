<?php
/**
 * FriendshipMutation Class.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;

/**
 * FriendshipMutation Class.
 */
class FriendshipMutation {

	/**
	 * Get friendship object.
	 *
	 * @throws UserError User error for invalid Relay ID.
	 *
	 * @param array $input Array of possible input fields.
	 *
	 * @return \BP_Friends_Friendship
	 */
	public static function get_friendship_from_input( $input ) {
		$friendship_id = 0;

		/**
		 * Trying to get the friendship ID.
		 */
		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$friendship_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['friendshipId'] ) ) {
			$friendship_id = absint( $input['friendshipId'] );
		}

		return new \BP_Friends_Friendship( absint( $friendship_id ) );
	}

	/**
	 * Check if user can manage friendship.
	 *
	 * Only the friendship initiator and the friend, the one invited to the friendship can see it.
	 *
	 * @param \BP_Friends_Friendship $friendship Friendship object.
	 *
	 * @return bool
	 */
	public static function can_update_or_delete_friendship( \BP_Friends_Friendship $friendship ) {
		return in_array( bp_loggedin_user_id(), [ $friendship->initiator_user_id, $friendship->friend_user_id ], true );
	}
}
