<?php
/**
 * Registers Thread type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Thread;

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
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress thread.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'fields'            => [
					'unreadCount' => [
						'type'        => 'Int',
						'description' => __( 'Total count of unread messages for the thread.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve_node'      => function( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_thread_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
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
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'threadId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
				],
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
