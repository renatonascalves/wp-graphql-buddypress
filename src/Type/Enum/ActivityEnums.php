<?php
/**
 * Activity Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * ActivityEnums Class.
 */
class ActivityEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Activity Types Enum.
		self::activity_types();

		// Activity Components Enum.
		self::activity_components();

		// Activity Order Status Enum.
		register_graphql_enum_type(
			'ActivityOrderStatusEnum',
			[
				'description' => __( 'The status order of the activity.', 'wp-graphql-buddypress' ),
				'values'      => [
					'HAM_ONLY'  => [
						'name'        => 'HAM_ONLY',
						'description' => __( 'Used to get hammed activities.', 'wp-graphql-buddypress' ),
						'value'       => 'ham_only',
					],
					'SPAM_ONLY' => [
						'name'        => 'SPAM_ONLY',
						'description' => __( 'Used to get spammed activities.', 'wp-graphql-buddypress' ),
						'value'       => 'spam_only',
					],
					'ALL'       => [
						'name'        => 'ALL',
						'description' => __( 'Used to get all activities.', 'wp-graphql-buddypress' ),
						'value'       => 'all',
					],
				],
			]
		);

		// Activity Status Enum.
		register_graphql_enum_type(
			'ActivityStatusEnum',
			[
				'description' => __( 'The status of the activity.', 'wp-graphql-buddypress' ),
				'values'      => [
					'PUBLISHED' => [
						'name'        => 'PUBLISHED',
						'description' => __( 'Activity with the published status.', 'wp-graphql-buddypress' ),
						'value'       => 'published',
					],
					'SPAM'      => [
						'name'        => 'SPAM',
						'description' => __( 'Activity with the spam status.', 'wp-graphql-buddypress' ),
						'value'       => 'spam',
					],
				],
			]
		);

		// Activity Scope Enum.
		register_graphql_enum_type(
			'ActivityOrderScopeEnum',
			[
				'description' => __( 'The scope for an activity.', 'wp-graphql-buddypress' ),
				'values'      => [
					'JUST_ME'   => [
						'name'        => 'JUST_ME',
						'description' => __( 'Used to limit results by the just-me scope.', 'wp-graphql-buddypress' ),
						'value'       => 'just-me',
					],
					'FRIENDS'   => [
						'name'        => 'FRIENDS',
						'description' => __( 'Used to limit results by the friends scope.', 'wp-graphql-buddypress' ),
						'value'       => 'friends',
					],
					'GROUPS'    => [
						'name'        => 'GROUPS',
						'description' => __( 'Used to limit results by the groups scope.', 'wp-graphql-buddypress' ),
						'value'       => 'groups',
					],
					'FAVORITES' => [
						'name'        => 'FAVORITES',
						'description' => __( 'Used to limit results by the favorites scope.', 'wp-graphql-buddypress' ),
						'value'       => 'favorites',
					],
					'MENTIONS'  => [
						'name'        => 'MENTIONS',
						'description' => __( 'Used to limit results by the mentions scope.', 'wp-graphql-buddypress' ),
						'value'       => 'mentions',
					],
				],
			]
		);
	}

	/**
	 * Registers activity types enum.
	 */
	public static function activity_types(): void {
		$activity_types_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No activity type created yet.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		$activity_types = array_keys( bp_activity_get_types() );

		if ( ! empty( $activity_types ) && is_array( $activity_types ) ) {

			// Reset the array.
			$activity_types_enum_values = [];

			// Loop through the activity types.
			foreach ( $activity_types as $type ) {
				$activity_types_enum_values[ WPEnumType::get_safe_name( $type ) ] = [
					'value'       => $type,
					'description' => sprintf(
						/* translators: %1$s: activity type */
						__( 'Activity with the %1$s type', 'wp-graphql-buddypress' ),
						$type
					),
				];
			}
		}

		register_graphql_enum_type(
			'ActivityTypeEnum',
			[
				'description' => __( 'The type of the activity.', 'wp-graphql-buddypress' ),
				'values'      => $activity_types_enum_values,
			]
		);
	}

	/**
	 * Registers activity components enum.
	 */
	public static function activity_components(): void {
		$activity_components_enum_values = [];
		$activity_components             = array_keys( buddypress()->active_components );

		if ( ! empty( $activity_components ) ) {
			foreach ( $activity_components as $component ) {
				$activity_components_enum_values[ WPEnumType::get_safe_name( $component ) ] = [
					'value'       => $component,
					'description' => sprintf(
						/* translators: the %1$s: activity component */
						__( 'Activity with the %1$s component', 'wp-graphql-buddypress' ),
						$component
					),
				];
			}
		}

		register_graphql_enum_type(
			'ActivityComponentEnum',
			[
				'description' => __( 'The component of the activity.', 'wp-graphql-buddypress' ),
				'values'      => $activity_components_enum_values,
			]
		);
	}
}
