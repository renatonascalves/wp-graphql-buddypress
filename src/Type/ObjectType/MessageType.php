<?php
/**
 * Registers Message type and queries
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Message;

/**
 * Class MessageType
 */
class MessageType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Message';

	/**
	 * Registers the member type.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress thread message.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'fields'            => [
					'threadId'    => [
						'type'        => 'Int',
						'description' => __( 'The ID of the thread.', 'wp-graphql-buddypress' ),
					],
					'sender'      => [
						'type'        => 'User',
						'description' => __( 'The sender of the message.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Message $message, array $args, AppContext $context ) {
							return ! empty( $message->sender )
								? $context->get_loader( 'user' )->load_deferred( $message->sender )
								: null;
						},
					],
					'subject'     => [
						'type'        => 'String',
						'description' => __( 'The subject of the message.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( Message $message, array $args ) {
							if ( empty( $message->subject ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $message->subject;
							}

							return apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) );
						},
					],
					'excerpt'     => [
						'type'        => 'String',
						'description' => __( 'Summary of the message.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( Message $message, array $args ) {
							if ( empty( $message->excerpt ) ) {
								return null;
							}

							$excerpt = wp_strip_all_tags( bp_create_excerpt( $message->excerpt, 75 ) );

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $excerpt;
							}

							return apply_filters( 'bp_get_message_thread_excerpt', $excerpt );
						},
					],
					'message'     => [
						'type'        => 'String',
						'description' => __( 'The content of the message.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( Message $message, array $args ) {
							if ( empty( $message->message ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $message->message;
							}

							return apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) );
						},
					],
					'isStarred'   => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the message was starred.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Message $message ) {

							if ( false === bp_is_active( 'messages', 'star' ) ) {
								return null;
							}

							return bp_messages_is_message_starred( $message->databaseId, bp_loggedin_user_id() );
						},
					],
					'dateSent'    => [
						'type'        => 'String',
						'description' => __( 'The date the message was sent, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'dateSentGmt' => [
						'type'        => 'String',
						'description' => __( 'The date the message was sent, as GMT.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_message_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Message ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);
	}
}
