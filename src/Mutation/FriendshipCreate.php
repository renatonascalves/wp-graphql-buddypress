<?php
/**
 * FriendshipCreate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

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
	public static function get_input_fields() {
		return [
			'initiatorId' => [
				'type'        => 'Int',
				'description' => __( 'User ID of the friendship initiator.', 'wp-graphql-buddypress' ),
			],
			'friendId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'User ID of the `friend` - the one invited to the friendship.', 'wp-graphql-buddypress' ),
			],
			'force' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to force friendship acceptance (only admins can force it).', 'wp-graphql-buddypress' ),
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
			'friendship' => [
				'type'        => 'Friendship',
				'description' => __( 'The friendship that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( ! isset( $payload['id'] ) || ! absint( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_friendship_object( absint( $payload['id'] ), $context );
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

			// Check if user is logged in.
			if ( ! is_user_logged_in() ) {
				throw new UserError( __( 'Sorry, you need to be logged in to perform this action.', 'wp-graphql-buddypress' ) );
			}

			$initiator_id = get_user_by( 'id', $input['initiatorId'] ?? bp_loggedin_user_id() );
			$friend_id    = get_user_by( 'id', $input['friendId'] );

			// Check if users are valid.
			if ( ! $initiator_id || ! $friend_id ) {
				throw new UserError( __( 'There was a problem confirming if user is a valid one.', 'wp-graphql-buddypress' ) );
			}

			// Check if user can create friendship.
			if ( ! bp_current_user_can( 'bp_moderate' ) && ! in_array( bp_loggedin_user_id(), [ $initiator_id->ID, $friend_id->ID ], true ) ) {
				throw new UserError( __( 'There was a problem confirming if user is a valid one.', 'wp-graphql-buddypress' ) );
			}

			// Right now, only admins can force accept.
			if ( isset( $input['force'] ) && ! bp_current_user_can( 'bp_moderate' ) ) {
				throw new UserError( __( 'Only admins can force friendship acceptance.', 'wp-graphql-buddypress' ) );
			}

			// Adding friendship.
			if ( ! friends_add_friend( $initiator_id->ID, $friend_id->ID, $input['force'] ?? false ) ) {
				throw new UserError( __( 'There was a problem trying to create the friendship.', 'wp-graphql-buddypress' ) );
			}

			$friendship = new \BP_Friends_Friendship(
				\BP_Friends_Friendship::get_friendship_id( $initiator_id->ID, $friend_id->ID )
			);

			// Confirm if friendship exists after creation.
			if ( ! $friendship || 0 === $friendship->id ) {
				throw new UserError( __( 'This friendship does not exist.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a friendship is created.
			 *
			 * @param \BP_Friends_Friendship $friendship The created friendship BuddyPress object.
			 * @param array                  $input      The input of the mutation.
			 * @param AppContext             $context    The AppContext passed down the resolve tree.
			 * @param ResolveInfo            $info       The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_friends_create_mutation', $friendship, $input, $context, $info );

			// Return the friendship id.
			return [
				'id' => $friendship->id,
			];
		};
	}
}
