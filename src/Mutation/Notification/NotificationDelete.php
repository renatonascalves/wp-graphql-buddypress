<?php
/**
 * NotificationDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Notification
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Notification;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\NotificationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Notification;
use BP_Notifications_Notification;

/**
 * NotificationDelete Class.
 */
class NotificationDelete {

	/**
	 * Registers the NotificationDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteNotification',
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
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'deleted'      => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the notification deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'notification' => [
				'type'        => 'Notification',
				'description' => __( 'The deleted notification object.', 'wp-graphql-buddypress' ),
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

			// Check and get the notification.
			$notification = NotificationHelper::get_notification_from_input( $input );

			// Bail now if a user isn't allowed to delete the object.
			if ( false === NotificationHelper::can_see( $notification->id ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the object before it is deleted.
			$previous_notification = new Notification( $notification );

			// Delete object.
			$retval = BP_Notifications_Notification::delete( [ 'id' => $notification->id ] );

			if ( ! $retval ) {
				throw new UserError( esc_html__( 'Could not delete the notification.', 'wp-graphql-buddypress' ) );
			}

			return [
				'deleted'        => true,
				'previousObject' => $previous_notification,
			];
		};
	}
}
