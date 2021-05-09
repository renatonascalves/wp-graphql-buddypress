<?php
/**
 * BlogHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use stdClass;

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
	 * @return stdClass
	 */
	public static function get_blog_from_input( $input ): stdClass {
		$blog_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$blog_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['blogId'] ) ) {
			$blog_id = absint( $input['blogId'] );
		} elseif ( ! empty( $input ) && is_numeric( $input ) ) {
			$blog_id = absint( $input );
		}

		// Get the blog object.
		$blogs       = current( bp_blogs_get_blogs( [ 'include_blog_ids' => absint( $blog_id ) ] ) );
		$blog_object = $blogs[0] ?? 0;

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
}
