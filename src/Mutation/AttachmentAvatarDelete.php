<?php
/**
 * AttachmentAvatarDelete Mutation.
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
 * AttachmentAvatarDelete Class.
 */
class AttachmentAvatarDelete {

	/**
	 * Registers the AttachmentAvatarDelete mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'deleteAttachmentAvatar',
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
	public static function get_input_fields() {
		return [
			'objectId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The globally unique identifier for the object.', 'wp-graphql-buddypress' ),
			],
			'object'   => [
				'type'        => [ 'non_null' => 'AttachmentAvatarEnum' ],
				'description' => __( 'The object (user, group, blog, etc) the avatar belongs to.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields() {
		return [
			'deleted' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the attachment deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return $payload['deleted'];
				},
			],
			'attachment'   => [
				'type'        => 'Attachment',
				'description' => __( 'The deleted attachment object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return $payload['previousObject'] ?? null;
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

			/**
			 * Throw an exception if there's no input.
			 */
			if ( empty( $input ) || ! is_array( $input ) ) {
				throw new UserError( __( 'Mutation not processed. There was no input for the mutation.', 'wp-graphql-buddypress' ) );
			}

			$object_id = $input['objectId'];
			$object    = $input['object'];

			/**
			 * Get and save the attachment object before it is deleted.
			 */
			$previous_attachment = Factory::resolve_attachment( $object_id, $object );

			/**
			 * Check if object has an avatar to delete first.
			 */
			if ( empty( $previous_attachment ) ) {
				throw new UserError( __( 'Sorry, there are no uploaded avatars to delete.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Stop now if a user isn't allowed to delete the attachment.
			 */
			if ( AttachmentMutation::can_update_or_delete_attachment( $object_id, $object ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete this attachment.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Trying to delete the attachment avatar.
			 */
			$deleted = bp_core_delete_existing_avatar(
				[
					'item_id' => $object_id,
					'object'  => $object,
				]
			);

			/**
			 * Confirm deletion.
			 */
			if ( ! $deleted ) {
				throw new UserError( __( 'Could not delete the attachment avatar.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after an attachment avatar is deleted.
			 *
			 * @param Attachment  $previous_attachment The deleted attachment object.
			 * @param array       $input               The input of the mutation.
			 * @param AppContext  $context             The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info                The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_attachment_avatar_delete_mutation', $previous_attachment, $input, $context, $info );

			/**
			 * The deleted attachment avatar status and the previous object.
			 */
			return [
				'deleted'        => true,
				'previousObject' => $previous_attachment,
			];
		};
	}
}
