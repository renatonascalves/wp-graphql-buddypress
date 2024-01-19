<?php
/**
 * ThreadCreate Mutation.
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
 * ThreadCreate Class.
 */
class ThreadCreate {

	/**
	 * Registers the ThreadCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createThread',
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
			'recipients' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'The list of the recipients user IDs of the Message.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'ID of the Messages Thread. Required when replying to an existing Thread.', 'wp-graphql-buddypress' ),
			],
			'subject'    => [
				'type'        => 'String',
				'description' => __( 'Subject of the Message initializing the Thread.', 'wp-graphql-buddypress' ),
			],
			'message'    => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'Content of the Message to add to the Thread.', 'wp-graphql-buddypress' ),
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
			$thread = null;

			// Check if user can perform this action.
			if ( false === is_user_logged_in() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			if ( ! empty( $input['databaseId'] ) ) {
				$thread = ThreadHelper::get_thread_from_input( $input );

				// Check if user can perform this action.
				if ( false === ThreadHelper::can_update_or_delete_thread( $thread->thread_id ) ) {
					throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
				}
			}

			// Check empty message content.
			if ( empty( $input['message'] ) ) {
				throw new UserError( esc_html__( 'Please, enter the content of the thread message.', 'wp-graphql-buddypress' ) );
			}

			// Check empty recipients.
			if ( empty( $input['recipients'] ) ) {
				throw new UserError( esc_html__( 'Recipients is a required field.', 'wp-graphql-buddypress' ) );
			}

			// Create thread and return its ID.
			$thread_id = messages_new_message(
				ThreadHelper::prepare_thread_args( $input, 'create', $thread )
			);

			// Throw an exception if the thread failed to be created.
			if ( empty( $thread_id ) || is_wp_error( $thread_id ) ) {
				throw new UserError( esc_html__( 'There was an error trying to create a thread message.', 'wp-graphql-buddypress' ) );
			}

			return [
				'id' => $thread_id,
			];
		};
	}
}
