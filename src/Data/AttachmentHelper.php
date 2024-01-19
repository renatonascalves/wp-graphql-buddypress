<?php
/**
 * AttachmentHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use stdClass;
use BP_Attachment_Avatar;
use BP_Attachment_Cover_Image;

/**
 * AttachmentHelper Class.
 */
class AttachmentHelper {

	/**
	 * Check object ID.
	 *
	 * @throws UserError User error for invalid user.
	 *
	 * @param string $bp_object BP Object.
	 * @param int    $object_id Object ID.
	 * @return int
	 */
	public static function check_object_id( string $bp_object, int $object_id ): int {
		switch ( $bp_object ) {
			// Get the group id.
			case 'group':
			case 'groups':
				$group = GroupHelper::get_group_from_input( $object_id );
				return $group->id;

			// Get the user id.
			case 'user':
			case 'members':
				$user = get_user_by( 'id', $object_id );

				// Check if user is valid.
				if ( ! $user ) {
					throw new UserError( esc_html__( 'There was a problem confirming if user is valid.', 'wp-graphql-buddypress' ) );
				}

				return $user->ID;

			// Get the blog id.
			case 'blog':
			case 'blogs':
				$blog = BlogHelper::get_blog_from_input( $object_id );
				return $blog->blog_id;

			default:
				return $object_id;
		}
	}

	/**
	 * Check if user can manage an attachment.
	 *
	 * @param int    $object_id Attachment Object ID.
	 * @param string $bp_object Attachment Object.
	 * @param bool   $cover     Is it a cover image? Default: false.
	 * @return bool
	 */
	public static function can_update_or_delete_attachment( int $object_id, string $bp_object, bool $cover = false ): bool {

		// Mapping object for verification.
		if ( $cover ) {
			switch ( $bp_object ) {
				case 'members':
					$bp_object = 'user';
					break;

				case 'groups':
					$bp_object = 'group';
					break;

				case 'blogs':
					$bp_object = 'blog';
					break;
			}
		}

		$args = [
			'item_id' => $object_id,
			'object'  => $bp_object,
		];

		$action = $cover ? 'edit_cover_image' : 'edit_avatar';

		return ( is_user_logged_in() && bp_attachments_current_user_can( $action, $args ) );
	}

	/**
	 * Cover upload from file.
	 *
	 * @throws UserError User error.
	 *
	 * @param array  $input   Mutation input fields.
	 * @param string $bp_object  Object (members, groups, blogs, etc).
	 * @param int    $item_id Item. (user_id, group_id, blog_id, etc).
	 */
	public static function upload_cover_from_file( array $input, string $bp_object, int $item_id ): void {

		// Set global variables.
		$bp = buddypress();
		switch ( $bp_object ) {
			case 'groups':
				$bp->groups->current_group = groups_get_group( absint( $item_id ) );
				break;

			case 'members':
			default:
				// @phpstan-ignore-next-line
				$bp->displayed_user     = new stdClass();
				$bp->displayed_user->id = (int) $item_id;
				break;
		}

		// Build expected file array for BuddyPress.
		$file_to_upload = [
			'file' => [
				'tmp_name' => $input['file']['fileName'],
				'name'     => basename( $input['file']['fileName'] ),
				'type'     => $input['file']['mimeType'],
				'error'    => 0,
				'size'     => filesize( $input['file']['fileName'] ),
			],
		];

		// Hacky solution to use correct values in the file upload.
		add_filter(
			'bp_after_cover_image_upload_dir_parse_args',
			function () use ( $bp_object, $item_id ) {
				return [
					'object_id'        => $item_id,
					'object_directory' => $bp_object,
				];
			}
		);

		// Force the post action for BuddyPress.
		$_POST['action'] = 'bp_cover_image_upload';

		// Try to upload cover image.
		$cover_instance = new BP_Attachment_Cover_Image();
		$uploaded_image = $cover_instance->upload( $file_to_upload );

		// Something went wrong? Bail with error.
		if ( ! empty( $uploaded_image['error'] ) ) {
			throw new UserError(
				sprintf(
					/* translators: %s is replaced with a cover error message */
					esc_html__( 'Upload failed! Error was: %s.', 'wp-graphql-buddypress' ),
					esc_html( $uploaded_image['error'] )
				)
			);
		}

		$bp_attachments_uploads_dir = bp_attachments_cover_image_upload_dir(
			[
				'object_directory' => $bp_object,
				'object_id'        => $item_id,
			]
		);

		// The BP Attachments Uploads Dir is not set, so stop here.
		if ( empty( $bp_attachments_uploads_dir ) ) {
			throw new UserError( esc_html__( 'The BuddyPress attachments uploads directory is not set.', 'wp-graphql-buddypress' ) );
		}

		$cover_subdir = $bp_attachments_uploads_dir['subdir'];
		$cover_dir    = $bp_attachments_uploads_dir['basedir'] . $cover_subdir;

		// If upload path doesn't exist, stop.
		if ( 0 !== validate_file( $cover_dir ) || ! is_dir( $cover_dir ) ) {
			throw new UserError( esc_html__( 'The cover image directory is not valid.', 'wp-graphql-buddypress' ) );
		}

		// Upload cover.
		$cover = bp_attachments_cover_image_generate_file(
			[
				'file'            => $uploaded_image['file'],
				'component'       => $bp_object,
				'cover_image_dir' => $cover_dir,
			]
		);

		// Bail if any error happened.
		if ( false === $cover ) {
			throw new UserError( esc_html__( 'There was a problem uploading the cover image.', 'wp-graphql-buddypress' ) );
		}

		// Bail with error if too small.
		if ( true === $cover['is_too_small'] ) {

			// Hacky way to get correct image dimentions.
			$bp_object = ( 'members' === $bp_object ) ? 'xprofile' : $bp_object;

			// Get cover image advised dimensions.
			$cover_dimensions = bp_attachments_get_cover_image_dimensions( $bp_object );

			throw new UserError(
				sprintf(
					/* translators: %$1s and %$2s is replaced with the correct sizes. */
					esc_html__( 'You have selected an image that is smaller than the recommended size. For better results, make sure to upload an image that is larger than %1$spx wide, and %2$spx tall.', 'wp-graphql-buddypress' ),
					(int) $cover_dimensions['width'],
					(int) $cover_dimensions['height']
				)
			);
		}
	}

	/**
	 * Avatar upload from file.
	 *
	 * @throws UserError User error.
	 *
	 * @param array  $input   Mutation input fields.
	 * @param string $bp_object  Object (user, group, blog, etc).
	 * @param int    $item_id Item. (user_id, group_id, blog_id, etc).
	 */
	public static function upload_avatar_from_file( $input, $bp_object, $item_id ): void {

		// Set global variables.
		$bp = buddypress();
		switch ( $bp_object ) {
			case 'group':
				$bp->groups->current_group = groups_get_group( $item_id );
				$upload_main_dir           = 'groups_avatar_upload_dir';
				break;

			case 'user':
			default:
				$upload_main_dir = 'bp_members_avatar_upload_dir';

				// @phpstan-ignore-next-line
				$bp->displayed_user     = new stdClass();
				$bp->displayed_user->id = (int) $item_id;
				break;
		}

		// Build expected file array for BuddyPress.
		$file_to_upload = [
			'file' => [
				'tmp_name' => $input['file']['fileName'],
				'name'     => basename( $input['file']['fileName'] ),
				'type'     => $input['file']['mimeType'],
				'error'    => 0,
				'size'     => filesize( $input['file']['fileName'] ),
			],
		];

		// Force the post action for BuddyPress.
		$_POST['action'] = 'bp_avatar_upload';

		$avatar_instance = new BP_Attachment_Avatar();
		$avatar_original = $avatar_instance->upload( $file_to_upload, $upload_main_dir );

		// Bail early in case of an error.
		if ( ! empty( $avatar_original['error'] ) ) {
			throw new UserError(
				sprintf(
					/* translators: %s is replaced with the error */
					esc_html__( 'Upload failed! Error was: %s.', 'wp-graphql-buddypress' ),
					esc_html( $avatar_original['error'] )
				)
			);
		}

		// Delete existing image if one already exists.
		self::delete_existing_image( $item_id, $bp_object );

		// Get image and bail early if there is an error.
		$image_file = self::resize( $avatar_original['file'], $avatar_instance );

		// Crop the profile photo accordingly.
		self::crop_image( $image_file, $avatar_instance, $bp_object, $item_id );
	}

	/**
	 * Resize image.
	 *
	 * @throws UserError User error.
	 *
	 * @param mixed                $file            Image to resize.
	 * @param BP_Attachment_Avatar $avatar_instance Avatar instance.
	 * @return string
	 */
	protected static function resize( $file, $avatar_instance ): string {
		$bp = buddypress();

		if ( ! isset( $bp->avatar_admin ) ) {
			// @phpstan-ignore-next-line
			$bp->avatar_admin = new stdClass();
		}

		// The Avatar UI available width.
		$ui_available_width = 0;

		// Try to set the ui_available_width using the avatar_admin global.
		if ( isset( $bp->avatar_admin->ui_available_width ) ) {
			$ui_available_width = $bp->avatar_admin->ui_available_width;
		}

		$resized = $avatar_instance->shrink( $file, $ui_available_width );

		// Check for WP_Error on what should be an image.
		if ( is_wp_error( $resized ) ) {
			throw new UserError(
				sprintf(
					/* translators: %s is replaced with the error. */
					esc_html__( 'Upload failed! Error was: %s.', 'wp-graphql-buddypress' ),
					esc_html( $resized->get_error_message() )
				)
			);
		}

		// We only want to handle one image after resize.
		if ( empty( $resized ) || empty( $resized['path'] ) ) {
			return $file;
		}

		return $resized['path'];
	}

	/**
	 * Crop image.
	 *
	 * @throws UserError User error.
	 *
	 * @param mixed                $image_file      Image to crop.
	 * @param BP_Attachment_Avatar $avatar_instance Avatar instance.
	 * @param string               $bp_object          Object.
	 * @param int                  $item_id         Item ID.
	 */
	protected static function crop_image( $image_file, $avatar_instance, $bp_object, $item_id ): void {
		$image          = getimagesize( $image_file );
		$avatar_to_crop = str_replace( bp_core_avatar_upload_path(), '', $image_file );

		// Get avatar full width and height.
		$full_height = bp_core_avatar_full_height();
		$full_width  = bp_core_avatar_full_width();

		// Use as much as possible of the image.
		$avatar_ratio = $full_width / $full_height;
		$image_ratio  = $image[0] / $image[1];

		if ( $image_ratio >= $avatar_ratio ) {
			// Uploaded image is wider than BP ratio, so we crop horizontally.
			$crop_y = 0;
			$crop_h = $image[1];

			// Get the target width by multiplying unmodified image height by target ratio.
			$crop_w    = $avatar_ratio * $image[1];
			$padding_w = round( ( $image[0] - $crop_w ) / 2 );
			$crop_x    = $padding_w;
		} else {
			// Uploaded image is narrower than BP ratio, so we crop vertically.
			$crop_x = 0;
			$crop_w = $image[0];

			// Get the target height by multiplying unmodified image width by target ratio.
			$crop_h    = $avatar_ratio * $image[0];
			$padding_h = round( ( $image[1] - $crop_h ) / 2 );
			$crop_y    = $padding_h;
		}

		add_filter( 'bp_attachments_current_user_can', '__return_true' );

		switch ( $bp_object ) {
			case 'group':
				$avatar_dir = 'group-avatars';
				break;

			case 'blog':
				$avatar_dir = 'blog-avatars';
				break;

			case 'user':
			default:
				$avatar_dir = 'avatars';
				break;
		}

		// Crop the image.
		$cropped = $avatar_instance->crop(
			[
				'object'        => $bp_object,
				'avatar_dir'    => $avatar_dir,
				'item_id'       => $item_id,
				'original_file' => $avatar_to_crop,
				'crop_w'        => $crop_w,
				'crop_h'        => $crop_h,
				'crop_x'        => $crop_x,
				'crop_y'        => $crop_y,
			]
		);

		remove_filter( 'bp_attachments_current_user_can', '__return_true' );

		// Check for errors.
		if ( empty( $cropped['full'] ) || empty( $cropped['thumb'] ) || is_wp_error( $cropped['full'] ) || is_wp_error( $cropped['thumb'] ) ) {
			throw new UserError(
				sprintf(
					/* translators: %s is replaced with object type. */
					esc_html__( 'There was a problem cropping your %s photo.', 'wp-graphql-buddypress' ),
					esc_html( $bp_object )
				)
			);
		}
	}

	/**
	 * Delete group/user's existing avatar if one exists.
	 *
	 * @param int    $item_id Item ID.
	 * @param string $bp_object  Object.
	 */
	protected static function delete_existing_image( $item_id, $bp_object ): void {
		// Get existing avatar.
		$existing_avatar = bp_core_fetch_avatar(
			[
				'object'  => $bp_object,
				'item_id' => $item_id,
				'html'    => false,
			]
		);

		// Check if the avatar exists before deleting it.
		if ( ! empty( $existing_avatar ) ) {
			bp_core_delete_existing_avatar(
				[
					'object'  => $bp_object,
					'item_id' => $item_id,
				]
			);
		}
	}
}
