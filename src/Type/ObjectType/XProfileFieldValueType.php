<?php
/**
 * Registers BuddyPress Field Data Value Type object.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

/**
 * XProfileFieldValueType Class.
 */
class XProfileFieldValueType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'XProfileFieldValue';

	/**
	 * XProfile Field Value Registration.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description' => __( 'Info about a BuddyPress XProfile field value.', 'wp-graphql-buddypress' ),
				'fields'      => [
					'raw'            => [
						'type'        => 'String',
						'description' => __( 'Field value directly from the database.', 'wp-graphql-buddypress' ),
					],
					'rendered'       => [
						'type'        => 'String',
						'description' => __( 'Field value with WordPress field rendering.', 'wp-graphql-buddypress' ),
					],
					'unserialized'   => [
						'type'        => [ 'list_of' => 'String' ],
						'description' => __( 'Unserialized field value(s) with WordPress field rendering.', 'wp-graphql-buddypress' ),
					],
					'lastUpdated'    => [
						'type'        => 'String',
						'description' => __( 'The date the field value was last updated, in the site\'s timezone', 'wp-graphql-buddypress' ),
					],
					'lastUpdatedGmt' => [
						'type'        => 'String',
						'description' => __( 'The date the field value was last updated, as GMT.', 'wp-graphql-buddypress' ),
					],
				],
			]
		);
	}
}
