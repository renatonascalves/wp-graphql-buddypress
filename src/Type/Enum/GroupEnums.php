<?php
/**
 * Group Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * GroupEnums Class.
 */
class GroupEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Group Order By.
		register_graphql_enum_type(
			'GroupOrderByEnum',
			[
				'description' => __( 'The attribute to order groups by.', 'wp-graphql-buddypress' ),
				'values'      => [
					'DATE_CREATED'       => [
						'name'        => 'DATE_CREATED',
						'description' => __( 'Used to order groups by the created date.', 'wp-graphql-buddypress' ),
						'value'       => 'date_created',
					],
					'LAST_ACTIVITY'      => [
						'name'        => 'LAST_ACTIVITY',
						'description' => __( 'Used to order groups by last activity.', 'wp-graphql-buddypress' ),
						'value'       => 'last_activity',
					],
					'TOTAL_MEMBER_COUNT' => [
						'name'        => 'TOTAL_MEMBER_COUNT',
						'description' => __( 'Used to order groups by totam member count.', 'wp-graphql-buddypress' ),
						'value'       => 'total_member_count',
					],
					'NAME'               => [
						'name'        => 'NAME',
						'description' => __( 'Used to order groups by name.', 'wp-graphql-buddypress' ),
						'value'       => 'name',
					],
					'RANDOM'             => [
						'name'        => 'RANDOM',
						'description' => __( 'Used to order groups randomly.', 'wp-graphql-buddypress' ),
						'value'       => 'random',
					],
				],
			]
		);

		// Group Order Types.
		register_graphql_enum_type(
			'GroupOrderTypeEnum',
			[
				'description' => __( 'Shorthand for certain orderby/order combinations.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ACTIVE'       => [
						'name'        => 'ACTIVE',
						'description' => __( 'Used to order groups by their status.', 'wp-graphql-buddypress' ),
						'value'       => 'active',
					],
					'NEWEST'       => [
						'name'        => 'NEWEST',
						'description' => __( 'Used to order groups by their date.', 'wp-graphql-buddypress' ),
						'value'       => 'newest',
					],
					'ALPHABETICAL' => [
						'name'        => 'ALPHABETICAL',
						'description' => __( 'Used to order groups by alphabetical order.', 'wp-graphql-buddypress' ),
						'value'       => 'alphabetical',
					],
					'RANDOM'       => [
						'name'        => 'RANDOM',
						'description' => __( 'Used to order groups randomly.', 'wp-graphql-buddypress' ),
						'value'       => 'random',
					],
					'POPULAR'      => [
						'name'        => 'POPULAR',
						'description' => __( 'Used to order groups by their populatity.', 'wp-graphql-buddypress' ),
						'value'       => 'popular',
					],
				],
			]
		);

		// Group Stati.
		self::group_stati();

		// Group Types.
		self::group_types();
	}

	/**
	 * Registers group stati enum.
	 */
	public static function group_stati(): void {
		$group_status_enum_values = [
			WPEnumType::get_safe_name( 'public' ) => [
				'description' => __( 'Group with the public status', 'wp-graphql-buddypress' ),
				'value'       => 'public',
			],
		];

		$group_stati = buddypress()->groups->valid_status;

		if ( ! empty( $group_stati ) && is_array( $group_stati ) ) {
			// Reset the array.
			$group_status_enum_values = [];

			// Loop through the group_stati.
			foreach ( $group_stati as $status ) {
				$group_status_enum_values[ WPEnumType::get_safe_name( $status ) ] = [
					'description' => sprintf(
						/* translators: 1: group status */
						__( 'Group with the %1$s status', 'wp-graphql-buddypress' ),
						$status
					),
					'value'       => $status,
				];
			}
		}

		register_graphql_enum_type(
			'GroupStatusEnum',
			[
				'description' => __( 'The status of the group.', 'wp-graphql-buddypress' ),
				'values'      => $group_status_enum_values,
			]
		);
	}

	/**
	 * Registers group types enum.
	 */
	public static function group_types(): void {
		$group_types_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No group type created yet.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		$group_types = bp_groups_get_group_types();

		if ( ! empty( $group_types ) && is_array( $group_types ) ) {

			// Reset the array.
			$group_types_enum_values = [];

			// Loop through the group types.
			foreach ( $group_types as $type ) {
				$group_types_enum_values[ WPEnumType::get_safe_name( $type ) ] = [
					'description' => sprintf(
						/* translators: %1$s: group type */
						__( 'Group with the %1$s type', 'wp-graphql-buddypress' ),
						$type
					),
					'value'       => $type,
				];
			}
		}

		register_graphql_enum_type(
			'GroupTypeEnum',
			[
				'description' => __( 'The type of the group.', 'wp-graphql-buddypress' ),
				'values'      => $group_types_enum_values,
			]
		);
	}
}
