<?php
/**
 * AttachmentCoverDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Attachment
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Attachment;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\AttachmentHelper;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Attachment;

/**
 * AttachmentCoverDelete Class.
 */
class AttachmentCoverDelete {

	/**
	 * Registers the AttachmentCoverDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteAttachmentCover',
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
			'objectId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The globally unique identifier for the object.', 'wp-graphql-buddypress' ),
			],
			'object'   => [
				'type'        => [ 'non_null' => 'AttachmentCoverEnum' ],
				'description' => __( 'The object (members, groups, blogs, etc) the cover belongs to.', 'wp-graphql-buddypress' ),
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
			'deleted'    => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the attachment deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return $payload['deleted'];
				},
			],
			'attachment' => [
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
		return function ( array $input ) {

			$object    = $input['object'];
			$object_id = AttachmentHelper::check_object_id( $object, $input['objectId'] );

			// Stop now if a user isn't allowed to delete an attachment cover.
			if ( false === AttachmentHelper::can_update_or_delete_attachment( $object_id, $object, true ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get the attachment object before it is deleted.
			$previous_attachment = Factory::resolve_attachment_cover( $object_id, $object );

			// Check if object has a cover to delete first.
			if ( ! $previous_attachment instanceof Attachment ) {
				throw new UserError( esc_html__( 'Sorry, there are no uploaded covers to delete.', 'wp-graphql-buddypress' ) );
			}

			// Trying to delete the attachment cover.
			$deleted = bp_attachments_delete_file(
				[
					'item_id'    => $object_id,
					'object_dir' => $object,
					'type'       => 'cover-image',
				]
			);

			// Confirm deletion.
			if ( false === $deleted ) {
				throw new UserError( esc_html__( 'Could not delete the attachment cover.', 'wp-graphql-buddypress' ) );
			}

			// The deleted attachment cover status and the previous object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_attachment,
			];
		};
	}
}
