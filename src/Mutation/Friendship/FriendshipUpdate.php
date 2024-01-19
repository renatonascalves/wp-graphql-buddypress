<?php
/**
 * FriendshipUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Friendship
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Friendship;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipHelper;
use BP_Friends_Friendship;

/**
 * FriendshipUpdate Class.
 */
class FriendshipUpdate {

	/**
	 * Registers the FriendshipUpdate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'updateFriendship',
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
				'description' => __( 'The friendship that was updated/accepted.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					if ( empty( $payload['id'] ) ) {
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
			if ( false === in_array( $friendship_status, [ 'pending', 'awaiting_response' ], true ) ) {
				throw new UserError( esc_html__( 'There is no friendship request or users are already friends.', 'wp-graphql-buddypress' ) );
			}

			// Get friendship.
			$friendship = new BP_Friends_Friendship(
				BP_Friends_Friendship::get_friendship_id( $initiator->ID, $friend->ID )
			);

			// Confirm if friendship exists.
			if ( false === FriendshipHelper::friendship_exists( $friendship ) ) {
				throw new UserError( esc_html__( 'No Friendship requested was found.', 'wp-graphql-buddypress' ) );
			}

			// Accept friendship.
			if ( false === friends_accept_friendship( $friendship->id ) ) {
				throw new UserError( esc_html__( 'There was a problem accepting the friendship. Try again.', 'wp-graphql-buddypress' ) );
			}

			// Return the friendship id.
			return [
				'id' => $friendship->id,
			];
		};
	}
}
