<?php
/**
 * ThreadUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Thread
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Thread;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;
use BP_Messages_Message;

/**
 * ThreadUpdate Class.
 */
class ThreadUpdate {

	/**
	 * Registers the ThreadUpdate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'updateThread',
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
				'description' => __( 'The globally unique identifier for the thread.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Messages_Thread->thread_id field.', 'wp-graphql-buddypress' ),
			],
			'messageId'  => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Messages_Message->id field.', 'wp-graphql-buddypress' ),
			],
			'read'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to mark the thread as read.', 'wp-graphql-buddypress' ),
			],
			'unRead'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to mark the thread as unread.', 'wp-graphql-buddypress' ),
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
			'thread' => [
				'type'        => 'Thread',
				'description' => __( 'The thread object that was updated.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_thread_object( absint( $payload['id'] ), $context );
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
			$thread     = ThreadHelper::get_thread_from_input( $input );
			$user_id    = bp_loggedin_user_id();
			$message_id = $thread->last_message_id;

			// Check if user can perform this action.
			if ( false === ThreadHelper::can_update_or_delete_thread( $thread->thread_id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Is someone updating the thread status?
			$thread_status_update = ( (bool) $input['read'] || (bool) $input['unRead'] );

			// Mark thread as read.
			if ( ! empty( $input['read'] ) && true === (bool) $input['read'] ) {
				messages_mark_thread_read( $thread->thread_id, $user_id );
			}

			// Mark thread as unread.
			if ( ! empty( $input['unRead'] ) && true === (bool) $input['unRead'] ) {
				messages_mark_thread_unread( $thread->thread_id, $user_id );
			}

			if ( ! empty( $input['messageId'] ) ) {
				$message_id = $input['messageId'];
			}

			$message = ThreadHelper::get_message_from_input( $message_id );

			/**
			 * Filter here to allow more users to edit the message meta (eg: the recipients).
			 *
			 * @since 0.1.0
			 *
			 * @param bool               $value    Whether the user can edit the message meta.
			 *                                     By default: only the sender, for now.
			 * @param BP_Messages_Message $message The message object.
			 * @param array               $input   Input values.
			 */
			$can_edit_item_meta = (bool) apply_filters(
				'bp_graphql_messages_can_edit_item_meta',
				( $user_id === $message->sender_id ),
				$message,
				$input
			);

			// The message must exist in the thread, and the logged in user must be the sender.
			if (
				false === $thread_status_update
				&& (
					empty( $message->id )
					|| $message->thread_id !== $thread->thread_id
					|| ! $can_edit_item_meta
				)
			) {
				throw new UserError( esc_html__( 'There was an error trying to update the message.', 'wp-graphql-buddypress' ) );
			}

			return [
				'id' => $thread->thread_id,
			];
		};
	}
}
