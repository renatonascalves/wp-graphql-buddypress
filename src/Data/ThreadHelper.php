<?php
/**
 * ThreadHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use BP_Messages_Thread;
use BP_Messages_Message;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * ThreadHelper Class.
 */
class ThreadHelper {

	/**
	 * Get thread helper.
	 *
	 * @throws UserError User error for invalid thread.
	 *
	 * @param array|int $input Array of possible input fields. Or thread ID.
	 * @return BP_Messages_Thread
	 */
	public static function get_thread_from_input( $input ): BP_Messages_Thread {
		$thread_id     = Factory::get_id( $input );
		$thread_object = new BP_Messages_Thread( $thread_id, 'ASC', [ 'user_id' => $input['userId'] ?? bp_loggedin_user_id() ] );

		// Confirm if thread exists.
		if ( false === (bool) $thread_object::is_valid( $thread_id ) ) {
			throw new UserError( esc_html__( 'This thread does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $thread_object;
	}

	/**
	 * Get message helper.
	 *
	 * @throws UserError User error for invalid message.
	 *
	 * @param array|int $input Array of possible input fields. Or message ID.
	 * @return BP_Messages_Message
	 */
	public static function get_message_from_input( $input ): BP_Messages_Message {
		$message_id     = Factory::get_id( $input );
		$message_object = new BP_Messages_Message( $message_id );

		// Confirm if message exists.
		if ( empty( $message_object->id ) ) {
			throw new UserError( esc_html__( 'This message does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $message_object;
	}

	/**
	 * Mapping thread params.
	 *
	 * @param array                   $input  The input for the mutation.
	 * @param string                  $action Hook action.
	 * @param BP_Messages_Thread|null $thread Optional. BuddyPress Thread object.
	 * @return array
	 */
	public static function prepare_thread_args( array $input, string $action, $thread = null ): array {
		$mutation_args = [
			'sender_id'  => empty( $input['senderId'] )
				? bp_loggedin_user_id()
				: $input['senderId'],
			'subject'    => empty( $input['subject'] )
				? $thread->last_message_subject ?? null
				: $input['subject'],
			'content'    => empty( $input['message'] )
				? $thread->last_message_content ?? null
				: $input['message'],
			'recipients' => empty( $input['recipients'] )
				? (
					$thread->recipients
						? array_values( array_filter( wp_parse_id_list( wp_list_pluck( $thread->recipients, 'user_id' ) ) ) )
						: null
				)
				: $input['recipients'],
		];

		// Setting the thread ID.
		if ( ! empty( $thread->thread_id ) ) {
			$mutation_args['thread_id'] = $thread->thread_id;
		}

		/**
		 * Allow updating mutation args.
		 *
		 * @param array                   $mutation_args Mutation output args.
		 * @param array                   $input         Mutation input args.
		 * @param BP_Messages_Thread|null $thread        BuddyPress Thread object.
		 */
		return (array) apply_filters( "bp_graphql_thread_{$action}_mutation_args", $mutation_args, $input, $thread );
	}

	/**
	 * Check if user can update or delete threads.
	 *
	 * @param int $thread_id Thread ID.
	 * @return bool
	 */
	public static function can_update_or_delete_thread( int $thread_id ): bool {
		return null !== messages_check_thread_access( $thread_id, bp_loggedin_user_id() );
	}
}
