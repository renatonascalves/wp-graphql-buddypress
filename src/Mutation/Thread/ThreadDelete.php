<?php
/**
 * ThreadDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Thread
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Thread;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Thread;

/**
 * ThreadDelete Class.
 */
class ThreadDelete {

	/**
	 * Registers the ThreadDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteThread',
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
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'deleted' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the thread deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'thread'  => [
				'type'        => 'Thread',
				'description' => __( 'The deleted thread object.', 'wp-graphql-buddypress' ),
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
		return function ( array $input ) {
			$thread  = ThreadHelper::get_thread_from_input( $input );
			$user_id = bp_loggedin_user_id();

			// Check if user can perform this action.
			if ( false === ThreadHelper::can_update_or_delete_thread( $thread->thread_id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the Thread object before it is deleted.
			$previous_thread = new Thread( $thread );

			// Delete a thread.
			if ( false === messages_delete_thread( $thread->thread_id, $user_id ) ) {
				throw new UserError( esc_html__( 'Could not delete the thread.', 'wp-graphql-buddypress' ) );
			}

			// The deleted thread status and the previous thread object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_thread,
			];
		};
	}
}
