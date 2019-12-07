<?php
/**
 * Member Enums.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * MemberEnums Class
 */
class MemberEnums {

	/**
	 * Registers enum type.
	 */
	public static function register() {

		// Member Order by Type.
		register_graphql_enum_type(
			'MemberOrderByTypeEnum',
			[
				'description' => __( 'Shorthand for certain members orderby/order combinations.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ACTIVE'      => [
						'name'        => 'ACTIVE',
						'description' => __( 'Used to order active members.', 'wp-graphql-buddypress' ),
						'value'       => 'active',
					],
					'NEWEST' => [
						'name'        => 'NEWEST',
						'description' => __( 'Used to order members by newest.', 'wp-graphql-buddypress' ),
						'value'       => 'newest',
					],
					'ALPHABETICAL' => [
						'name'        => 'ALPHABETICAL',
						'description' => __( 'Used to order members alphabetically.', 'wp-graphql-buddypress' ),
						'value'       => 'alphabetical',
					],
					'RANDOM' => [
						'name'        => 'RANDOM',
						'description' => __( 'Used to order members randomly.', 'wp-graphql-buddypress' ),
						'value'       => 'random',
					],
					'ONLINE' => [
						'name'        => 'ONLINE',
						'description' => __( 'Used to order online members.', 'wp-graphql-buddypress' ),
						'value'       => 'online',
					],
					'POPULAR' => [
						'name'        => 'POPULAR',
						'description' => __( 'Used to order popular members.', 'wp-graphql-buddypress' ),
						'value'       => 'popular',
					],
				],
			]
		);
	}
}
