<?php
/**
 * FriendshipDelete Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipMutation;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;

/**
 * FriendshipDelete Class.
 */
class FriendshipDelete {

	/**
	 * Registers the FriendshipDelete mutation.
	 */
	public static function register_mutation() {
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
	public static function get_input_fields() {
		return [
			'initiatorId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the friendship initiator.', 'wp-graphql-buddypress' ),
			],
			'friendId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the `friend` - the one invited to the friendship.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'deleted' => [
				'type' => 'Boolean',
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
		return function ( $input, AppContext $context, ResolveInfo $info ) {

			// Throw an exception if there's no input.
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			$initiator_id = get_user_by( 'id', $input['initiatorId'] );
			$friend_id    = get_user_by( 'id', $input['friendId'] );

			// Check if users are valid.
			if ( ! $initiator_id || ! $friend_id ) {
				throw new UserError( __( 'There was a problem confirming if user is valid.', 'wp-graphql-buddypress' ) );
			}

			// Stop now if a user isn't allowed to see this friendship.
			if ( false === FriendshipMutation::can_update_or_delete_friendship( $initiator_id->ID, $friend_id->ID ) ) {
				throw new UserError( __( 'Sorry, you do not have permission to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Check friendship status.
			$friendship_status = \BP_Friends_Friendship::check_is_friend( $initiator_id->ID, $friend_id->ID );

			// Confirm status.
			if ( 'not_friends' === $friendship_status ) {
				throw new UserError( __( 'Those users are not yet friends and not friendship request was found.', 'wp-graphql-buddypress' ) );
			}

			// Get friendship.
			$friendship = new \BP_Friends_Friendship(
				\BP_Friends_Friendship::get_friendship_id( $initiator_id->ID, $friend_id->ID )
			);

			// Get and save the friendship object before it is deleted.
			$previous_friendship = new Friendship( $friendship );

			/**
			 * If this change is being initiated by the initiator,
			 * use the `reject` function.
			 *
			 * This is the user who requested the friendship, and is doing the withdrawing.
			 */
			if ( bp_loggedin_user_id() === $friendship->initiator_user_id ) {
				$deleted = friends_withdraw_friendship( $friendship->initiator_user_id, $friendship->friend_user_id );
			} else {
				/**
				 * Otherwise, this change is being initiated by the user, friend,
				 * who received the friendship reject.
				 */
				$deleted = friends_reject_friendship( $friendship->id );
			}

			// Trying to delete the friendship.
			if ( ! $deleted ) {
				throw new UserError( __( 'Friendship could not be deleted.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a friendship is deleted.
			 *
			 * @param Friendship  $previous_friendship The deleted friendship model object.
			 * @param array       $input               The input of the mutation.
			 * @param AppContext  $context             The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info                The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_friends_delete_mutation', $previous_friendship, $input, $context, $info );

			// The deleted friendship status and the previous friendship object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_friendship,
			];
		};
	}
}
