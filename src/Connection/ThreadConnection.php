<?php
/**
 * Register Thread Connections.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * Class ThreadConnection.
 */
class ThreadConnection {

	/**
	 * Register connections to Threads.
	 */
	public static function register_connections(): void {

		// Register connection from RootQuery to Thread.
		register_graphql_connection( self::get_thread_connection_config() );

		// Register connection from User to Thread.
		register_graphql_connection(
			self::get_thread_connection_config(
				[
					'fromType'      => 'User',
					'toType'        => 'Thread',
					'fromFieldName' => 'threads',
				]
			)
		);

		// Register connection from Thread to Message.
		register_graphql_connection( self::get_messages_connection_config() );

		// Register connection from Thread to Recipients.
		register_graphql_connection( self::get_thread_recipients_connection_config() );
	}

	/**
	 * This returns a RootQuery > thread connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_thread_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Thread',
				'fromFieldName'  => 'threads',
				'connectionArgs' => [
					'userId' => [
						'type'        => 'Int',
						'description' => __( 'Include threads created by a specific member.', 'wp-graphql-buddypress' ),
					],
					'search' => [
						'type'        => 'String',
						'description' => __( 'Search term(s) to retrieve matching thread for.', 'wp-graphql-buddypress' ),
					],
					'type'   => [
						'type'        => 'ThreadTypeEnum',
						'description' => __( 'Filter the results by thread status.', 'wp-graphql-buddypress' ),
					],
					'box'    => [
						'type'        => 'ThreadBoxEnum',
						'description' => __( 'Filter the results by box.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_thread_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns a Thread > messages connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_messages_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'Thread',
				'toType'         => 'Message',
				'fromFieldName'  => 'messages',
				'connectionArgs' => [
					'order' => [
						'type'        => 'OrderEnum',
						'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
					],
					'type'  => [
						'type'        => 'MessageTypeEnum',
						'description' => __( 'Filter the results by type.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_messages_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}

	/**
	 * This returns a Thread > recipients connection config.
	 *
	 * @param array $args Array of arguments.
	 * @return array
	 */
	public static function get_thread_recipients_connection_config( array $args = [] ): array {
		return array_merge(
			[
				'fromType'       => 'Thread',
				'toType'         => 'User',
				'fromFieldName'  => 'recipients',
				'connectionArgs' => [
					'order' => [
						'type'        => 'OrderEnum',
						'description' => __( 'Order sort attribute ascending or descending.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'        => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					return Factory::resolve_recipients_connection( $source, $args, $context, $info );
				},
			],
			$args
		);
	}
}
