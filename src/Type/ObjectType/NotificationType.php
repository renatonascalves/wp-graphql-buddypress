<?php
/**
 * Registers Notification type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\NotificationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Notification;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;

/**
 * Class NotificationType
 */
class NotificationType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Notification';

	/**
	 * Registers the notification type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress notification.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'eagerlyLoadType'   => true,
				'fields'            => [
					'user'            => [
						'type'        => 'User',
						'description' => __( 'The user the notification is addressed to.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Notification $notification, array $args, AppContext $context ) {
							return ! empty( $notification->userId )
								? $context->get_loader( 'user' )->load_deferred( $notification->userId )
								: null;
						},
					],
					'primaryItemId'   => [
						'type'        => 'Int',
						'description' => __( 'The ID of some other object primarily associated with this one.', 'wp-graphql-buddypress' ),
					],
					'secondaryItemId' => [
						'type'        => 'Int',
						'description' => __( 'The ID of some other object also associated with this one.', 'wp-graphql-buddypress' ),
					],
					'componentName'   => [
						'type'        => 'String',
						'description' => __( 'The name of the BuddyPress component the notification relates to.', 'wp-graphql-buddypress' ),
					],
					'componentAction' => [
						'type'        => 'String',
						'description' => __( 'The name of the component\'s action the notification is about.', 'wp-graphql-buddypress' ),
					],
					'object'          => [
						'type'        => 'NotificationObjectUnion',
						'description' => __( 'The object primarily associated with this one.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Notification $notification, array $args, AppContext $context ) {
							$object = null;

							if (
								bp_is_active( 'activity' )
								&& 'activity' === $notification->componentName
								&& ! empty( $notification->primaryItemId ) ) {
								$object = $context->get_loader( 'bp_activity' )->load_deferred( $notification->primaryItemId );
							}

							if (
								bp_is_active( 'groups' )
								&& 'groups' === $notification->componentName
								&& ! empty( $notification->primaryItemId ) ) {
								$object = $context->get_loader( 'bp_group' )->load_deferred( $notification->primaryItemId );
							}

							if (
								bp_is_active( 'blogs' )
								&& 'blogs' === $notification->componentName
								&& ! empty( $notification->primaryItemId ) ) {
								$object = $context->get_loader( 'bp_blog' )->load_deferred( $notification->primaryItemId );
							}

							if ( bp_is_active( 'members' ) && empty( $object ) ) {
								$object = $context->get_loader( 'user' )->load_deferred( $notification->userId );
							}

							return $object;
						},
					],
					'date'            => [
						'type'        => 'String',
						'description' => __( 'The date the notification was created, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'dateGmt'         => [
						'type'        => 'String',
						'description' => __( 'The date the notification was created, as GMT.', 'wp-graphql-buddypress' ),
					],
					'isNew'           => [
						'type'        => 'Boolean',
						'description' => __( 'Whether it\'s a new notification or not.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_notification_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Notification ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			strtolower( self::$type_name ),
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Notification object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$notification = NotificationHelper::get_notification_from_input( $args );

					if ( false === NotificationHelper::can_see( $notification->id ) ) {
						return null;
					}

					return Factory::resolve_notification_object( $notification->id, $context );
				},
			]
		);
	}
}
