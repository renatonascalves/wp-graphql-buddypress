<?php
/**
 * AttachmentMutation Class.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

/**
 * AttachmentMutation Class.
 */
class AttachmentMutation {

	/**
	 * Check if user can manage an attachment.
	 *
	 * @param int    $object_id Object ID.
	 * @param string $object    Object.
	 * @param bool   $cover     Is it a cover check? Default: false.
	 *
	 * @return bool
	 */
	public static function can_update_or_delete_attachment( $object_id, $object, $cover = false ) {
		$args = [
			'item_id' => $object_id,
			'object'  => $object,
		];

		$action = $cover ? 'edit_cover_image' : 'edit_avatar';

		return ( is_user_logged_in() && bp_attachments_current_user_can( $action, $args ) );
	}
}
