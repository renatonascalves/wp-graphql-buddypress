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
	 * Registers enum type.
	 */
	public static function register() {

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
}
