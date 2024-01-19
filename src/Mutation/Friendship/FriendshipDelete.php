<?php
/**
 * FriendshipDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Friendship
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Friendship;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;
use BP_Friends_Friendship;

/**
 * FriendshipDelete Class.
 */
class FriendshipDelete {

	/**
	 * Registers the FriendshipDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteFriendship',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input fields.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'initiatorId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the friendship initiator.', 'wp-graphql-buddypress' ),
			],
			'friendId'    => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the `friend` - the one invited to the friendship.', 'wp-graphql-buddypress' ),
			],
			'force'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to force friendship removal.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'deleted'    => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the friendship deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'friendship' => [
				'type'        => 'Friendship',
				'description' => __( 'The deleted friendship object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return $payload['previousObject'] ?? null;
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			$initiator = get_user_by( 'id', $input['initiatorId'] );
			$friend    = get_user_by( 'id', $input['friendId'] );

			// Check if users are valid.
			if ( ! $initiator || ! $friend ) {
				throw new UserError( esc_html__( 'There was a problem confirming if user is valid.', 'wp-graphql-buddypress' ) );
			}

			// Stop now if a user isn't allowed to see this friendship.
			if ( false === FriendshipHelper::can_update_or_delete_friendship( $initiator->ID, $friend->ID ) ) {
				throw new UserError( esc_html__( 'Sorry, you do not have permission to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Check friendship status.
			$friendship_status = BP_Friends_Friendship::check_is_friend( $initiator->ID, $friend->ID );

			// Confirm status.
			if ( 'not_friends' === $friendship_status ) {
				throw new UserError( esc_html__( 'Those users are not yet friends and no friendship request was found.', 'wp-graphql-buddypress' ) );
			}

			// Get friendship.
			$friendship = new BP_Friends_Friendship(
				BP_Friends_Friendship::get_friendship_id( $initiator->ID, $friend->ID )
			);

			// Get and save the friendship object before it is deleted.
			$previous_friendship = new Friendship( $friendship );

			// Remove a friendship.
			if ( true === $input['force'] ) {
				$deleted = friends_remove_friend( $friendship->initiator_user_id, $friendship->friend_user_id );
			} elseif ( absint( bp_loggedin_user_id() ) === $friendship->initiator_user_id ) {
				/**
				 * If this change is being initiated by the initiator,
				 * use the `reject` function.
				 *
				 * This is the user who requested the friendship, and is doing the withdrawing.
				 */
				$deleted = friends_withdraw_friendship( $friendship->initiator_user_id, $friendship->friend_user_id );
			} else {
				/**
				 * Otherwise, this change is being initiated by the user, friend,
				 * who received the friendship reject.
				 */
				$deleted = friends_reject_friendship( $friendship->id );
			}

			// Trying to delete the friendship.
			if ( false === $deleted ) {
				throw new UserError( esc_html__( 'Friendship could not be deleted.', 'wp-graphql-buddypress' ) );
			}

			// The deleted friendship status and the previous friendship object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_friendship,
			];
		};
	}
}
