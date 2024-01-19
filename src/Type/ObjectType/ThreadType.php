<?php
/**
 * Registers Thread type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use GraphQL\Deferred;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Thread;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;

/**
 * Class ThreadType
 */
class ThreadType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Thread';

	/**
	 * Registers the thread type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress thread.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier', 'UniformResourceIdentifiable' ],
				'fields'            => [
					'unreadCount' => [
						'type'        => 'Int',
						'description' => __( 'Total count of unread messages for the thread.', 'wp-graphql-buddypress' ),
					],
					'lastMessage' => [
						'type'        => 'Message',
						'description' => __( 'The last message of the thread.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Thread $thread, array $args, AppContext $context ) {
							return ! empty( $thread->lastMessage )
								? $context->get_loader( 'bp_message' )->load_deferred( $thread->lastMessage )
								: null;
						},
					],
					'senders'     => [
						'type'        => [ 'list_of' => 'User' ],
						'description' => __( 'All users of all messages in the thread.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Thread $thread, array $args, AppContext $context ) {
							$user_ids = array_unique( $thread->senderIds ?? [] );

							if ( empty( $user_ids ) ) {
								return null;
							}

							$context->get_loader( 'user' )->buffer( $user_ids );
							return new Deferred(
								function () use ( $context, $user_ids ) {
									return $context->get_loader( 'user' )->load_many( $user_ids );
								}
							);
						},
					],
				],
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_thread_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Thread ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'thread',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Thread object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					return Factory::resolve_thread_object(
						ThreadHelper::get_thread_from_input( $args )->thread_id,
						$context
					);
				},
			]
		);
	}
}
