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
			'id' => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the friendship.', 'wp-graphql-buddypress' ),
			],
			'friendshipId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Friends_Friendship->id field.', 'wp-graphql-buddypress' ),
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

			// Get the friendship object.
			$friendship = FriendshipMutation::get_friendship_from_input( $input );

			// Confirm if friendship exists.
			if ( ! $friendship || 0 === $friendship->id ) {
				throw new UserError( __( 'This friendship does not exist.', 'wp-graphql-buddypress' ) );
			}

			// Stop now if a user isn't allowed to see this friendship.
			if ( false === FriendshipMutation::can_update_or_delete_friendship( $friendship ) ) {
				throw new UserError( __( 'Sorry, you don\'t have permission to see this friendship.', 'wp-graphql-buddypress' ) );
			}

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
				throw new UserError( __( 'Could not delete friendship.', 'wp-graphql-buddypress' ) );
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
