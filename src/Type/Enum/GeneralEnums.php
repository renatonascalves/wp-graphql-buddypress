<?php
/**
 * General Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * GeneralEnums Class.
 */
class GeneralEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

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
}
