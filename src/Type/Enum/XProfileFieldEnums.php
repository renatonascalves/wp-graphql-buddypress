<?php
/**
 * XProfile Field Enums.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.0.1-alpha
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
	public static function register() {

		// XProfile Field Value Format Enum.
		register_graphql_enum_type(
			'XProfileFieldValueFormatEnum',
			[
				'description' => __( 'The format of field value field.', 'wp-graphql-buddypress' ),
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
					'UNSERIALIZED' => [
						'name'        => 'UNSERIALIZED',
						'description' => __( 'Apply the default WordPress rendering', 'wp-graphql-buddypress' ),
						'value'       => 'unserialized',
					],
				],
			]
		);

		// Field Types Enum.
		self::field_types_enum();

		// Visibility Levels Enum.
		self::visibility_levels_enum();
	}

	/**
	 * Visibility Levels Enum.
	 */
	public static function visibility_levels_enum() {
		$levels = [];
		foreach ( bp_xprofile_get_visibility_levels() as $level ) {
			$levels[ WPEnumType::get_safe_name( $level['id'] ) ] = [
				'description' => sprintf(
					/* translators: visibility level */
					__( 'Visibility Level: %1$s', 'wp-graphql-buddypress' ),
					$level['label']
				),
				'value' => $level['id'],
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
	public static function field_types_enum() {
		$types = [];
		foreach ( (array) buddypress()->profile->field_types as $type ) {
			$types[ WPEnumType::get_safe_name( $type ) ] = [
				'description' => sprintf(
					/* translators: field type */
					__( 'Field Type: %1$s', 'wp-graphql-buddypress' ),
					$type
				),
				'value' => $type,
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
