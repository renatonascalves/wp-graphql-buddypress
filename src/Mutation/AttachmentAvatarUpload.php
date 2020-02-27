<?php
/**
 * AttachmentAvatarUpload Mutation.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Mutation
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\AttachmentMutation;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * AttachmentAvatarUpload Class.
 */
class AttachmentAvatarUpload {

	/**
	 * Registers the AttachmentAvatarUpload mutation.
	 */
	public static function register_mutation() {
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
			'file' => [
				'type'        => [ 'non_null' => 'Upload' ],
				'description' => __( 'Upload a local file using multi-part.', 'wp-graphql-buddypress' ),
			],
			'objectId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The unique identifier (user_id, group_id, etc) for the object the avatar will belong to.', 'wp-graphql-buddypress' ),
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
		return function ( $input, AppContext $context, ResolveInfo $info ) {

			$object_id    = $input['objectId'];
			$object       = $input['object'];
			$show_avatars = buddypress()->avatar->show_avatars;

			// Check if upload is enabled for member.
			if ( 'user' === $object && ( true === bp_disable_avatar_uploads() || false === $show_avatars ) ) {
				throw new UserError( __( 'Sorry, member avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if upload is enabled for group.
			if ( 'group' === $object && ( true === bp_disable_group_avatar_uploads() || false === $show_avatars ) ) {
				throw new UserError( __( 'Sorry, group avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if upload is enabled for blog.
			if ( 'blog' === $object && false === $show_avatars ) {
				throw new UserError( __( 'Sorry, blog avatar upload is disabled.', 'wp-graphql-buddypress' ) );
			}

			// Check if user has access to upload it.
			if ( false === AttachmentMutation::can_update_or_delete_attachment( $object_id, $object ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Try to upload the avatar image file.
			AttachmentMutation::upload_avatar_from_file( $input, $object, $object_id );

			/**
			 * Fires after an attachment avatar is uploaded.
			 *
			 * @param array       $input    The input of the mutation.
			 * @param AppContext  $context  The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info     The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_attachment_avatar_create_mutation', $input, $context, $info );

			return [
				'id'     => $object_id,
				'object' => $object,
			];
		};
	}
}
