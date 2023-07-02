<?php
/**
 * Members' Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * MemberEnums Class
 */
class MemberEnums {

	/**
	 * Registers member enum types.
	 */
	public static function register(): void {

		// Member Order by Type Enum.
		register_graphql_enum_type(
			'MemberOrderByTypeEnum',
			[
				'description' => __( 'Shorthand for certain members orderby/order combinations.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ACTIVE'       => [
						'name'        => 'ACTIVE',
						'description' => __( 'Used to order active members.', 'wp-graphql-buddypress' ),
						'value'       => 'active',
					],
					'NEWEST'       => [
						'name'        => 'NEWEST',
						'description' => __( 'Used to order members by newest.', 'wp-graphql-buddypress' ),
						'value'       => 'newest',
					],
					'ALPHABETICAL' => [
						'name'        => 'ALPHABETICAL',
						'description' => __( 'Used to order members alphabetically.', 'wp-graphql-buddypress' ),
						'value'       => 'alphabetical',
					],
					'RANDOM'       => [
						'name'        => 'RANDOM',
						'description' => __( 'Used to order members randomly.', 'wp-graphql-buddypress' ),
						'value'       => 'random',
					],
					'ONLINE'       => [
						'name'        => 'ONLINE',
						'description' => __( 'Used to order online members.', 'wp-graphql-buddypress' ),
						'value'       => 'online',
					],
					'POPULAR'      => [
						'name'        => 'POPULAR',
						'description' => __( 'Used to order popular members.', 'wp-graphql-buddypress' ),
						'value'       => 'popular',
					],
				],
			]
		);

		// Member Types Enum.
		self::member_types_enum();
	}

	/**
	 * Member Types Enum.
	 */
	public static function member_types_enum(): void {
		$member_types_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No member type created yet.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		$member_types = (array) bp_get_member_types();

		if ( ! empty( $member_types ) && is_array( $member_types ) ) {

			// Reset the array.
			$member_types_enum_values = [];

			foreach ( $member_types as $member_type ) {
				$member_types_enum_values[ WPEnumType::get_safe_name( $member_type ) ] = [
					'description' => sprintf(
						/* translators: %1$s: member type */
						__( 'Member Type: %1$s', 'wp-graphql-buddypress' ),
						$member_type
					),
					'value'       => $member_type,
				];
			}
		}

		register_graphql_enum_type(
			'MemberTypesEnum',
			[
				'description' => __( 'Member types.', 'wp-graphql-buddypress' ),
				'values'      => $member_types_enum_values,
			]
		);
	}
}
