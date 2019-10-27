<?php
/**
 * Group Enums.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\WPEnum
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\WPEnum;

/**
 * GroupEnums Class
 */
class GroupEnums {

	/**
	 * Registers enum type.
	 */
	public static function register() {
		register_graphql_enum_type(
			'GroupObjectFieldFormatEnum',
			[
				'description' => __( 'The format of group description field.', 'wp-graphql-buddypress' ),
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
