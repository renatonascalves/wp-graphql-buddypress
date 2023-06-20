<?php
/**
 * Registers BuddyPress Attachment Type object.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

/**
 * AttachmentType Class.
 */
class AttachmentType {

	/**
	 * Attachment registrations.
	 */
	public static function register(): void {
		register_graphql_object_type(
			'Attachment',
			[
				'description' => __( 'BuddyPress attachment object used in Avatar and Cover images.', 'wp-graphql-buddypress' ),
				'fields'      => [
					'thumb' => [
						'type'        => 'String',
						'description' => __( 'URL for the attachment with the thumb size.', 'wp-graphql-buddypress' ),
					],
					'full'  => [
						'type'        => 'String',
						'description' => __( 'URL for the attachment with the full size.', 'wp-graphql-buddypress' ),
					],
				],
			]
		);
	}
}
