<?php
/**
 * Notification Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * NotificationEnums Class.
 */
class NotificationEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Notification Component Names Enum.
		self::component_names();

		// Notification Order By Enum.
		register_graphql_enum_type(
			'NotificationOrderByEnum',
			[
				'description' => __( 'Paremeters to order notifications by.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ID'                => [
						'name'        => 'ID',
						'description' => __( 'Used to order results by the id paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'id',
					],
					'DATE_NOTIFIED'     => [
						'name'        => 'DATE_NOTIFIED',
						'description' => __( 'Used to order results by the date_notified paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'date_notified',
					],
					'ITEM_ID'           => [
						'name'        => 'ITEM_ID',
						'description' => __( 'Used to order results by the item_id paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'item_id',
					],
					'SECONDARY_ITEM_ID' => [
						'name'        => 'SECONDARY_ITEM_ID',
						'description' => __( 'Used to order results by the secondary_item_id paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'secondary_item_id',
					],
					'COMPONENT_NAME'    => [
						'name'        => 'COMPONENT_NAME',
						'description' => __( 'Used to order results by the component_name paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'component_name',
					],
					'COMPONENT_ACTION'  => [
						'name'        => 'COMPONENT_ACTION',
						'description' => __( 'Used to order results by the component_action paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'component_action',
					],
				],
			]
		);
	}

	/**
	 * Notification Component Names Enum.
	 *
	 * Return an array of component names that are currently active and have
	 * registered Notifications callbacks.
	 */
	public static function component_names(): void {
		$component_names = [];

		foreach ( (array) bp_notifications_get_registered_components() as $component ) {
			$component_names[ WPEnumType::get_safe_name( $component ) ] = [
				'description' => sprintf(
					/* translators: component name */
					__( 'Component Name: %1$s', 'wp-graphql-buddypress' ),
					$component
				),
				'value'       => $component,
			];
		}

		if ( empty( $component_names ) ) {
			return;
		}

		register_graphql_enum_type(
			'NotificationComponentNamesEnum',
			[
				'description' => __( 'Component Names with support for notifications.', 'wp-graphql-buddypress' ),
				'values'      => $component_names,
			]
		);
	}
}
