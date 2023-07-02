<?php
/**
 * Blog Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * BlogEnums Class.
 */
class BlogEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Site Language Enum.
		self::site_languages();

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

	/**
	 * Registers site languages enum.
	 */
	public static function site_languages(): void {
		$site_languages_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No language set yet.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		/** This filter is documented in wp-signup.php */
		$languages = (array) apply_filters( 'signup_get_available_languages', get_available_languages() );

		if ( ! empty( $languages ) && is_array( $languages ) ) {

			// Reset the array.
			$site_languages_enum_values = [];

			foreach ( $languages as $language ) {
				$site_languages_enum_values[ WPEnumType::get_safe_name( $language ) ] = [
					'value'       => $language,
					'description' => sprintf(
						/* translators: %1$s: site language */
						__( 'Language %1$s', 'wp-graphql-buddypress' ),
						$language
					),
				];
			}
		}

		register_graphql_enum_type(
			'SiteLanguagesEnum',
			[
				'description' => __( 'Available languages for the site.', 'wp-graphql-buddypress' ),
				'values'      => $site_languages_enum_values,
			]
		);
	}
}
