<?php
/**
 * Registers BuddyPress Attachment Type object.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

/**
 * AttachmentType Class.
 */
class AttachmentType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Attachment';

	/**
	 * Register the attachment type object.
	 */
	public static function register() {

		// Regiter Attachment object.
		register_graphql_object_type(
			self::$type_name,
			[
				'description' => __( 'BuddyPress attachment object used in Avatar and Cover images.', 'wp-graphql-buddypress' ),
				'fields'      => [
					'thumb'          => [
						'type'        => 'String',
						'description' => __( 'URL for the attachment with the thumb size.', 'wp-graphql-buddypress' ),
					],
					'full'          => [
						'type'        => 'String',
						'description' => __( 'URL for the attachment with the full size.', 'wp-graphql-buddypress' ),
					],
				],
			]
		);

		// Register Upload object.
		register_graphql_input_type(
			'Upload',
			[
				'description' => __( 'The `Upload` special type represents a file to be uploaded in the same HTTP request as specified by [graphql-multipart-request-spec](https://github.com/jaydenseric/graphql-multipart-request-spec).', 'wp-graphql-buddypress' ),
				'fields'      => [
					'fileName' => [
						'type'        => 'String',
						'description' => __( 'The name of the file being uploaded.', 'wp-graphql-buddypress' ),
					],
					'mimeType' => [
						'type'        => 'MimeTypeEnum',
						'description' => __( 'The mime-type of the file being uploaded.', 'wp-graphql-buddypress' ),
					],
				],
			]
		);
	}
}
