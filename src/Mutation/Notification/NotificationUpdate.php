<?php
/**
 * NotificationUpdate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Notification
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Notification;

use BP_Notifications_Notification;
use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\NotificationHelper;

/**
 * NotificationUpdate Class.
 */
class NotificationUpdate {

	/**
	 * Registers the NotificationUpdate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'updateNotification',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input fields.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'id'         => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the notification.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The database ID of the notification.', 'wp-graphql-buddypress' ),
			],
			'isNew'      => [
				'type'        => [ 'non_null' => 'Boolean' ],
				'description' => __( 'Update the notification status.', 'wp-graphql-buddypress' ),
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
			'notification' => [
				'type'        => 'Notification',
				'description' => __( 'The updated notification object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_notification_object( absint( $payload['id'] ), $context );
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

			// Check notification.
			$notification = NotificationHelper::get_notification_from_input( $input );

			// Bail now if a user isn't allowed to update the object.
			if ( false === NotificationHelper::can_see( $notification->id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			$is_new = (bool) $input['isNew'];

			if ( $is_new === $notification->is_new ) {
				throw new UserError( esc_html__( 'Notification is already with the status you are trying to update into.', 'wp-graphql-buddypress' ) );
			}

			$retval = BP_Notifications_Notification::update(
				[ 'is_new' => $is_new ],
				[ 'id' => $notification->id ]
			);

			if ( ! $retval ) {
				throw new UserError( esc_html__( 'Could not update this notification.', 'wp-graphql-buddypress' ) );
			}

			return [
				'id' => $notification->id,
			];
		};
	}
}
