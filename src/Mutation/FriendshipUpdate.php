<?php
/**
 * FriendshipUpdate Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipMutation;

/**
 * FriendshipUpdate Class.
 */
class FriendshipUpdate {

	/**
	 * Registers the FriendshipUpdate mutation.
	 */
	public static function register_mutation() {
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
			'friendship' => [
				'type'        => 'Friendship',
				'description' => __( 'The friendship that was updated.', 'wp-graphql-buddypress' ),
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

			// Accept friendship.
			if ( ! friends_accept_friendship( $friendship->id ) ) {
				throw new UserError( __( 'Could not accept friendship.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after a friendship is updated/accepted.
			 *
			 * @param \BP_Friends_Friendship $friendship The updated friendship BuddyPress object.
			 * @param array                  $input      The input of the mutation.
			 * @param AppContext             $context    The AppContext passed down the resolve tree.
			 * @param ResolveInfo            $info       The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_friends_update_mutation', $friendship, $input, $context, $info );

			// Return the friendship id.
			return [
				'id' => $friendship->id,
			];
		};
	}
}
