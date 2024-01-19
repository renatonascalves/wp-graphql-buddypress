<?php
/**
 * StarMessage Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Thread
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Thread;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;

/**
 * StarMessage Class.
 */
class StarMessage {

	/**
	 * Registers the StarMessage mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'starMessage',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'id'         => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the message.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Messages_Message->id field.', 'wp-graphql-buddypress' ),
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
			'message' => [
				'type'        => 'Message',
				'description' => __( 'The message object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_message_object( absint( $payload['id'] ), $context );
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
			$message = ThreadHelper::get_message_from_input( $input );
			$user_id = bp_loggedin_user_id();

			// Check if user can perform this action.
			if ( false === ThreadHelper::can_update_or_delete_thread( $message->thread_id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			$result = false;
			$action = 'star';
			$info   = __( 'Sorry, you cannot add the message to your starred box.', 'wp-graphql-buddypress' );

			if ( bp_messages_is_message_starred( $message->id, $user_id ) ) {
				$action = 'unstar';
				$info   = __( 'Sorry, you cannot remove the message from your starred box.', 'wp-graphql-buddypress' );
			}

			$result = bp_messages_star_set_action(
				[
					'user_id'    => $user_id,
					'thread_id'  => $message->thread_id,
					'message_id' => $message->id,
					'action'     => $action,
				]
			);

			if ( false === $result ) {
				throw new UserError( esc_html( $info ) );
			}

			// Return the message ID.
			return [
				'id' => $message->id,
			];
		};
	}
}
