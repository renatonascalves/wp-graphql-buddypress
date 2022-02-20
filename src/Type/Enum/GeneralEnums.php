<?php
/**
 * General Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * GeneralEnums Class.
 */
class GeneralEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Site Language Enum.
		self::site_languages();

		// Content Field Format.
		register_graphql_enum_type(
			'ContentFieldFormatEnum',
			[
				'description' => __( 'The format of content field.', 'wp-graphql-buddypress' ),
				'values'      => [
					'RAW'      => [
						'name'        => 'RAW',
						'description' => __( 'Provide the field value directly from database', 'wp-graphql-buddypress' ),
						'value'       => 'raw',
					],
					'RENDERED' => [
						'name'        => 'RENDERED',
						'description' => __( 'Apply the default WordPress rendering', 'wp-graphql-buddypress' ),
						'value'       => 'rendered',
					],
				],
			]
		);
	}

	/**
	 * Registers site languages enum.
	 */
	public static function site_languages(): void {

		/** This filter is documented in wp-signup.php */
		$languages             = (array) apply_filters( 'signup_get_available_languages', get_available_languages() );
		$languages_enum_values = [];

		if ( ! empty( $languages ) ) {
			foreach ( $languages as $language ) {
				$languages_enum_values[ WPEnumType::get_safe_name( $language ) ] = [
					'value'       => $language,
					'description' => sprintf(
						/* translators: %1$s: available language */
						__( 'Language %1$s', 'wp-graphql-buddypress' ),
						$language
					),
				];
			}
		}

		register_graphql_enum_type(
			'SiteLanguagesEnum',
			[
				'description' => __( 'Available languages.', 'wp-graphql-buddypress' ),
				'values'      => $languages_enum_values,
			]
		);
	}
}
