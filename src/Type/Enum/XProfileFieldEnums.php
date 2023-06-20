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
		$levels = [];
		foreach ( bp_xprofile_get_visibility_levels() as $level ) {
			$levels[ WPEnumType::get_safe_name( $level['id'] ) ] = [
				'value'       => $level['id'],
				'description' => sprintf(
					/* translators: 1: visibility level */
					__( 'Visibility Level: %1$s', 'wp-graphql-buddypress' ),
					$level['label']
				),
			];
		}

		// Visibility Levels.
		register_graphql_enum_type(
			'XProfileFieldVisibilityLevelEnum',
			[
				'description' => __( 'XProfile field visibility levels.', 'wp-graphql-buddypress' ),
				'values'      => $levels,
			]
		);
	}

	/**
	 * Field Types Enum.
	 */
	public static function field_types_enum(): void {
		$types = [];
		foreach ( (array) buddypress()->profile->field_types as $type ) {
			$types[ WPEnumType::get_safe_name( $type ) ] = [
				'description' => sprintf(
					/* translators: 1: field type */
					__( 'Field Type: %1$s', 'wp-graphql-buddypress' ),
					$type
				),
				'value'       => $type,
			];
		}

		register_graphql_enum_type(
			'XProfileFieldTypesEnum',
			[
				'description' => __( 'XProfile field types.', 'wp-graphql-buddypress' ),
				'values'      => $types,
			]
		);
	}
}
