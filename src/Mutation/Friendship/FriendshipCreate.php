<?php
/**
 * FriendshipCreate Mutation.
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
 * FriendshipCreate Class.
 */
class FriendshipCreate {

	/**
	 * Registers the FriendshipCreate mutation.
	 */
	public static function register_mutation(): void {
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
			'friendId'    => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the `friend` - the one being invited to the friendship.', 'wp-graphql-buddypress' ),
			],
			'force'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to force the friendship agreement.', 'wp-graphql-buddypress' ),
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

			$initiator = get_user_by( 'id', $input['initiatorId'] ?? absint( bp_loggedin_user_id() ) );
			$friend    = get_user_by( 'id', $input['friendId'] );

			// Check if users are valid.
			if ( ! $initiator || ! $friend ) {
				throw new UserError( esc_html__( 'There was a problem confirming if user is valid.', 'wp-graphql-buddypress' ) );
			}

			// Check if user can create friendship.
			if ( FriendshipHelper::can_create_friendship( $initiator->ID, $friend->ID ) ) {
				throw new UserError( esc_html__( 'Sorry, you do not have permission to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Check friendship status.
			$friendship_status = BP_Friends_Friendship::check_is_friend( $initiator->ID, $friend->ID );

			// Already friends.
			if ( 'is_friend' === $friendship_status ) {
				throw new UserError( esc_html__( 'You are already friends with this user.', 'wp-graphql-buddypress' ) );
			}

			// Already with a friendship request to this user.
			if ( 'not_friends' !== $friendship_status ) {
				throw new UserError( esc_html__( 'You already have a pending friendship request with this user.', 'wp-graphql-buddypress' ) );
			}

			// Only admins can force a friendship request.
			$force = ( true === (bool) $input['force'] && bp_current_user_can( 'bp_moderate' ) );

			// Adding friendship.
			if ( false === friends_add_friend( $initiator->ID, $friend->ID, $force ) ) {
				throw new UserError( esc_html__( 'There was a problem requesting the friendship.', 'wp-graphql-buddypress' ) );
			}

			// Get friendship object.
			$friendship = new BP_Friends_Friendship(
				BP_Friends_Friendship::get_friendship_id( $initiator->ID, $friend->ID )
			);

			// Confirm if friendship exists after creation.
			if ( false === FriendshipHelper::friendship_exists( $friendship ) ) {
				throw new UserError( esc_html__( 'Friendship was not requested.', 'wp-graphql-buddypress' ) );
			}

			// Return the friendship id.
			return [
				'id' => $friendship->id,
			];
		};
	}
}
