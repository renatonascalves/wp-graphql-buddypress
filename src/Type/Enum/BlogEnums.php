<?php
/**
 * Blog Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * BlogEnums Class.
 */
class BlogEnums {

	/**
	 * Registers enum type.
	 */
	public static function register() {

		// Blog Order Types.
		register_graphql_enum_type(
			'BlogOrderTypeEnum',
			[
				'description' => __( 'Shorthand for certain orderby/order combinations.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ACTIVE'       => [
						'name'        => 'ACTIVE',
						'description' => __( 'Used to order blogs by their status.', 'wp-graphql-buddypress' ),
						'value'       => 'active',
					],
					'NEWEST'       => [
						'name'        => 'NEWEST',
						'description' => __( 'Used to order blogs by their date.', 'wp-graphql-buddypress' ),
						'value'       => 'newest',
					],
					'ALPHABETICAL' => [
						'name'        => 'ALPHABETICAL',
						'description' => __( 'Used to order blogs by alphabetical order.', 'wp-graphql-buddypress' ),
						'value'       => 'alphabetical',
					],
					'RANDOM'       => [
						'name'        => 'RANDOM',
						'description' => __( 'Used to order blogs randomly.', 'wp-graphql-buddypress' ),
						'value'       => 'random',
					],
				],
			]
		);
	}
}
