<?php
/**
 * AttachmentAvatarUpload Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Attachment
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Attachment;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\AttachmentHelper;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * AttachmentAvatarUpload Class.
 */
class AttachmentAvatarUpload {

	/**
	 * Registers the AttachmentAvatarUpload mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'uploadAttachmentAvatar',
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
				'description' => __( 'The unique identifier (user_id, group_id, blog_id, etc) for the object the avatar will belong to.', 'wp-graphql-buddypress' ),
			],
			'object'   => [
				'type'        => [ 'non_null' => 'AttachmentAvatarEnum' ],
				'description' => __( 'The object (user, group, blog, etc) the avatar will belong to.', 'wp-graphql-buddypress' ),
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
				'description' => __( 'The uploaded avatar attachment object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return Factory::resolve_attachment( $payload['id'] ?? null, $payload['object'] ?? null );
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

			// Check if upload is enabled for member.
			if ( 'user' === $object && true === bp_disable_avatar_uploads() ) {
				throw new UserError( esc_html__( 'Sorry, member avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if upload is enabled for group.
			if ( 'group' === $object && true === bp_disable_group_avatar_uploads() ) {
				throw new UserError( esc_html__( 'Sorry, group avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if upload is enabled for blog.
			$bp = buddypress();
			if ( 'blog' === $object && isset( $bp->avatar->show_avatars ) && false === $bp->avatar->show_avatars ) {
				throw new UserError( esc_html__( 'Sorry, blog avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if user has access to upload it.
			if ( false === AttachmentHelper::can_update_or_delete_attachment( $object_id, $object ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Try to upload the avatar image file.
			AttachmentHelper::upload_avatar_from_file( $input, $object, $object_id );

			return [
				'id'     => $object_id,
				'object' => $object,
			];
		};
	}
}
