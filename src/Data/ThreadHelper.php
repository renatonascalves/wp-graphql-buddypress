<?php
/**
 * ThreadHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_Messages_Thread;
use BP_Messages_Message;

/**
 * ThreadHelper Class.
 */
class ThreadHelper {

	/**
	 * Get thread helper.
	 *
	 * @throws UserError User error for invalid thread.
	 *
	 * @param array $input Array of possible input fields.
	 * @return BP_Messages_Thread
	 */
	public static function get_thread_from_input( $input ): BP_Messages_Thread {
		$thread_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$thread_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['threadId'] ) ) {
			$thread_id = absint( $input['threadId'] );
		}

		$thread_object = new BP_Messages_Thread( $thread_id, 'ASC', [ 'user_id' => $input['userId'] ?? bp_loggedin_user_id() ] );

		// Confirm if thread exists.
		if ( false === (bool) $thread_object::is_valid( $thread_id ) ) {
			throw new UserError( __( 'This thread does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $thread_object;
	}

	/**
	 * Get message helper.
	 *
	 * @throws UserError User error for invalid message.
	 *
	 * @param array $input Array of possible input fields.
	 * @return BP_Messages_Message
	 */
	public static function get_message_from_input( $input ): BP_Messages_Message {
		$message_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$message_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input ) && is_numeric( $input ) ) {
			$message_id = absint( $input );
		}

		$message_object = new BP_Messages_Message( $message_id );

		// Confirm if message exists.
		if ( empty( $message_object->id ) ) {
			throw new UserError( __( 'This message does not exist.', 'wp-graphql-buddypress' ) );
		}

		return $message_object;
	}
}
