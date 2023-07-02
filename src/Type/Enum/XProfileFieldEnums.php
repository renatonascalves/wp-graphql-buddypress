<?php
/**
 * XProfile Field Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

use WPGraphQL\Type\WPEnumType;

/**
 * XProfileFieldEnums Class
 */
class XProfileFieldEnums {

	/**
	 * Registers enums.
	 */
	public static function register(): void {

		// Field Types Enum.
		self::field_types_enum();

		// Visibility Levels Enum.
		self::visibility_levels_enum();
	}

	/**
	 * Visibility Levels Enum.
	 */
	public static function visibility_levels_enum(): void {
		$visibility_level_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No visibility level is available.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		$levels = bp_xprofile_get_visibility_levels();

		if ( ! empty( $levels ) && is_array( $levels ) ) {

			// Reset the array.
			$visibility_level_enum_values = [];

			foreach ( $levels as $level ) {

				if ( empty( $level['id'] ) ) {
					continue;
				}

				$visibility_level_enum_values[ WPEnumType::get_safe_name( $level['id'] ) ] = [
					'description' => sprintf(
						/* translators: %1$s: visibility level */
						__( 'Visibility Level: %1$s', 'wp-graphql-buddypress' ),
						$level['label']
					),
					'value'       => $level['id'],
				];
			}
		}

		// Visibility Levels.
		register_graphql_enum_type(
			'XProfileFieldVisibilityLevelEnum',
			[
				'description' => __( 'XProfile field visibility levels.', 'wp-graphql-buddypress' ),
				'values'      => $visibility_level_enum_values,
			]
		);
	}

	/**
	 * Field Types Enum.
	 */
	public static function field_types_enum(): void {
		$field_types_enum_values = [
			WPEnumType::get_safe_name( 'none' ) => [
				'description' => __( 'No field type is available.', 'wp-graphql-buddypress' ),
				'value'       => 'none',
			],
		];

		$field_types = (array) buddypress()->profile->field_types;

		if ( ! empty( $field_types ) && is_array( $field_types ) ) {

			// Reset the array.
			$field_types_enum_values = [];

			foreach ( $field_types as $type ) {
				$field_types_enum_values[ WPEnumType::get_safe_name( $type ) ] = [
					'description' => sprintf(
						/* translators: %1$s: field type */
						__( 'Field Type: %1$s', 'wp-graphql-buddypress' ),
						$type
					),
					'value'       => $type,
				];
			}
		}

		register_graphql_enum_type(
			'XProfileFieldTypesEnum',
			[
				'description' => __( 'XProfile field types.', 'wp-graphql-buddypress' ),
				'values'      => $field_types_enum_values,
			]
		);
	}
}
