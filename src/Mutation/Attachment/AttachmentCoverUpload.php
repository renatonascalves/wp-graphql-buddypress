<?php
/**
 * AttachmentCoverUpload Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Attachment
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Attachment;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\AttachmentHelper;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * AttachmentCoverUpload Class.
 */
class AttachmentCoverUpload {

	/**
	 * Registers the AttachmentCoverUpload mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'uploadAttachmentCover',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input fields.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'file'     => [
				'type'        => [ 'non_null' => 'Upload' ],
				'description' => __( 'Upload a local file using multi-part.', 'wp-graphql-buddypress' ),
			],
			'objectId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The unique identifier (user_id, group_id, blog_id, etc) for the object the cover will belong to.', 'wp-graphql-buddypress' ),
			],
			'object'   => [
				'type'        => [ 'non_null' => 'AttachmentCoverEnum' ],
				'description' => __( 'The object (members, groups, blogs, etc) the cover will belong to.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'attachment' => [
				'type'        => 'Attachment',
				'description' => __( 'The uploaded cover attachment object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return Factory::resolve_attachment_cover( $payload['id'] ?? null, $payload['object'] ?? null );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			$object    = $input['object'];
			$object_id = AttachmentHelper::check_object_id( $object, $input['objectId'] );

			// Check if cover upload is enabled for members.
			if ( 'members' === $object && true === bp_disable_cover_image_uploads() ) {
				throw new UserError( esc_html__( 'Sorry, member cover upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if cover upload is enabled for groups.
			if ( 'groups' === $object && true === bp_disable_group_cover_image_uploads() ) {
				throw new UserError( esc_html__( 'Sorry, group cover upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if cover upload is enabled for blogs.
			$bp = buddypress();
			if ( 'blogs' === $object && isset( $bp->avatar->show_avatars ) && false === $bp->avatar->show_avatars ) {
				throw new UserError( esc_html__( 'Sorry, blog cover upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if user can to upload.
			if ( false === AttachmentHelper::can_update_or_delete_attachment( $object_id, $object, true ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Try to upload the cover image file.
			AttachmentHelper::upload_cover_from_file( $input, $object, $object_id );

			return [
				'id'     => $object_id,
				'object' => $object,
			];
		};
	}
}
