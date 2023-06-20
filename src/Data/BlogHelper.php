<?php
/**
 * BlogHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use stdClass;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * BlogHelper Class.
 */
class BlogHelper {

	/**
	 * Get blog ID helper.
	 *
	 * @throws UserError User error for invalid blog.
	 *
	 * @param array|int $input Array of possible input fields or a single integer.
	 * @return object
	 */
	public static function get_blog_from_input( $input ): object {
		$blog_id = Factory::get_id( $input );

		// Get the blog object.
		$blogs       = bp_blogs_get_blogs( [ 'include_blog_ids' => absint( $blog_id ) ] );
		$blog_object = $blogs['blogs'][0] ?? 0;

		if ( empty( $blog_object ) || ! is_object( $blog_object ) ) {
			throw new UserError(
				sprintf(
					// translators: %d is the blog ID.
					__( 'No Blog was found with ID: %d', 'wp-graphql-buddypress' ),
					absint( $blog_id )
				)
			);
		}

		return $blog_object;
	}

	/**
	 * Get blog uri/permalink.
	 *
	 * @param mixed $object Object.
	 * @return string|null
	 */
	public static function get_blog_uri( $object ): ?string {

		// Bail early.
		if ( empty( $object->domain ) && empty( $object->path ) ) {
			return null;
		}

		if ( empty( $object->domain ) && ! empty( $object->path ) ) {
			return bp_get_root_domain() . $object->path;
		}

		$protocol  = is_ssl() ? 'https://' : 'http://';
		$permalink = $protocol . $object->domain . $object->path;

		return apply_filters( 'bp_get_blog_permalink', $permalink );
	}
}
