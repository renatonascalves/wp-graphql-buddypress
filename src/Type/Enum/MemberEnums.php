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
		$types = [];

		foreach ( (array) bp_get_member_types() as $type ) {
			$types[ WPEnumType::get_safe_name( $type ) ] = [
				'description' => sprintf(
					/* translators: member type */
					__( 'Member Type: %1$s', 'wp-graphql-buddypress' ),
					$type
				),
				'value'       => $type,
			];
		}

		if ( empty( $types ) ) {
			return;
		}

		register_graphql_enum_type(
			'MemberTypesEnum',
			[
				'description' => __( 'Member types.', 'wp-graphql-buddypress' ),
				'values'      => $types,
			]
		);
	}
}
