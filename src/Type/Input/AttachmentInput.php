<?php
/**
 * Registers BuddyPress Attachment Input(s).
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Input
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Input;

/**
 * AttachmentInput Class.
 */
class AttachmentInput {

	/**
	 * Register Upload input type.
	 */
	public static function register(): void {
		register_graphql_input_type(
			'Upload',
			[
				'description' => __( 'The `Upload` special type represents a file to be uploaded in the same HTTP request as specified by [graphql-multipart-request-spec](https://github.com/jaydenseric/graphql-multipart-request-spec).', 'wp-graphql-buddypress' ),
				'fields'      => [
					'fileName' => [
						'type'        => 'String',
						'description' => __( 'The file being uploaded.', 'wp-graphql-buddypress' ),
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
