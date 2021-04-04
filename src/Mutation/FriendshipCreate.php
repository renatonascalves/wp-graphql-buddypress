<?php
/**
 * FriendshipCreate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipMutation;
use BP_Friends_Friendship;

/**
 * FriendshipCreate Class.
 */
class FriendshipCreate {

	/**
	 * Registers the FriendshipCreate mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'createFriendship',
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
				'type'        => 'Int',
				'description' => __( 'User ID of the friendship initiator. Defaults to the logged in user.', 'wp-graphql-buddypress' ),
			],
			'friendId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the `friend` - the one being invited to the friendship.', 'wp-graphql-buddypress' ),
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
			'friendship' => [
				'type'        => 'Friendship',
				'description' => __( 'The friendship that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_friendship_object( absint( $payload['id'] ) );
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
		return function ( $input ) {

			// Throw an exception if there's no input.
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			$logged_id    = bp_loggedin_user_id();
			$initiator_id = get_user_by( 'id', $input['initiatorId'] ?? $logged_id );
			$friend_id    = get_user_by( 'id', $input['friendId'] );

			// Check if users are valid.
			if ( ! $initiator_id || ! $friend_id ) {
				throw new UserError( __( 'There was a problem confirming if user is valid.', 'wp-graphql-buddypress' ) );
			}

			// Check if user can create friendship.
			if ( $logged_id !== $initiator_id->ID ) {
				throw new UserError( __( 'Sorry, you do not have permission to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Check friendship status.
			$friendship_status = BP_Friends_Friendship::check_is_friend( $initiator_id->ID, $friend_id->ID );

			// Already friends.
			if ( 'is_friend' === $friendship_status ) {
				throw new UserError( __( 'You are already friends with this user.', 'wp-graphql-buddypress' ) );
			}

			// Already with a friendship request to this user.
			if ( 'not_friends' !== $friendship_status ) {
				throw new UserError( __( 'You already have a pending friendship request with this user.', 'wp-graphql-buddypress' ) );
			}

			// Adding friendship.
			if ( false === friends_add_friend( $initiator_id->ID, $friend_id->ID ) ) {
				throw new UserError( __( 'There was a problem requesting the friendship.', 'wp-graphql-buddypress' ) );
			}

			// Get friendship object.
			$friendship = new BP_Friends_Friendship(
				BP_Friends_Friendship::get_friendship_id( $initiator_id->ID, $friend_id->ID )
			);

			// Confirm if friendship exists after creation.
			if ( false === FriendshipMutation::friendship_exists( $friendship ) ) {
				throw new UserError( __( 'Friendship was not requested.', 'wp-graphql-buddypress' ) );
			}

			// Return the friendship id.
			return [
				'id' => $friendship->id,
			];
		};
	}
}
