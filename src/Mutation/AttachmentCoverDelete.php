<?php
/**
 * AttachmentCoverDelete Mutation.
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
 * AttachmentCoverDelete Class.
 */
class AttachmentCoverDelete {

	/**
	 * Registers the AttachmentCoverDelete mutation.
	 */
	public static function register_mutation() {
		register_graphql_mutation(
			'attachmentCoverDelete',
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
				'type'        => [ 'non_null' => 'int' ],
				'description' => __( 'The globally unique identifier for the object.', 'wp-graphql-buddypress' ),
			],
			'object'   => [
				'type'        => 'AttachmentCoverEnum',
				'description' => __( 'The object (members, groups, blogs, etc) the cover belongs to.', 'wp-graphql-buddypress' ),
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

			$object_id = $input['objectId'] ?? 0;
			$object    = $input['object'] ?? 'members';

			/**
			 * Get and save the attachment object before it is deleted.
			 */
			$previous_attachment = Factory::resolve_attachment_cover( $object_id, $object );

			/**
			 * Check if object has a cover to delete first.
			 */
			if ( empty( $previous_attachment ) ) {
				throw new UserError( __( 'Sorry, there are no uploaded covers to delete.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Stop now if a user isn't allowed to delete the attachment cover.
			 */
			if ( AttachmentMutation::can_update_or_delete_attachment( $object_id, $object, true ) ) {
				throw new UserError( __( 'Sorry, you are not allowed to delete the attachment cover.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Trying to delete the attachment cover.
			 */
			$deleted = bp_attachments_delete_file(
				[
					'item_id'    => $object_id,
					'object_dir' => $object,
					'type'       => 'cover-image',
				]
			);

			/**
			 * Confirm deletion.
			 */
			if ( ! $deleted ) {
				throw new UserError( __( 'Could not delete the attachment cover.', 'wp-graphql-buddypress' ) );
			}

			/**
			 * Fires after an attachment cover is deleted.
			 *
			 * @param Attachment  $previous_attachment The deleted attachment object.
			 * @param array       $input               The input of the mutation.
			 * @param AppContext  $context             The AppContext passed down the resolve tree.
			 * @param ResolveInfo $info                The ResolveInfo passed down the resolve tree.
			 */
			do_action( 'bp_graphql_attachment_cover_delete_mutation', $previous_attachment, $input, $context, $info );

			/**
			 * The deleted attachment cover status and the previous object.
			 */
			return [
				'deleted'        => true,
				'previousObject' => $previous_attachment,
			];
		};
	}
}
