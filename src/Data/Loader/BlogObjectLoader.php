<?php
/**
 * BlogObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use GraphQL\Error\UserError;
use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;

/**
 * Class BlogObjectLoader
 */
class BlogObjectLoader extends AbstractDataLoader {

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * Note that order of returned values must match exactly the order of keys.
	 * If some entry is not available for given key - it must include null for the missing key.
	 *
	 * For example:
	 * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
	 *
	 * @throws UserError User error.
	 *
	 * @param array $keys Array of keys.
	 * @return array
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		// Execute the query, and prune the cache.
		bp_blogs_get_blogs(
			[
				'include_blog_ids' => $keys,
			]
		);

		$loaded_blogs = [];

		/**
		 * Loop over the keys and return an array of loaded_blogs, where the key is the ID and the value
		 * is the Blog object, passed through the Model layer.
		 */
		foreach ( $keys as $key ) {

			// Get the blog object.
			$blogs       = current( bp_blogs_get_blogs( [ 'include_blog_ids' => absint( $key ) ] ) );
			$blog_object = $blogs[0] ?? 0;

			if ( empty( $blog_object ) || ! is_object( $blog_object ) ) {
				throw new UserError(
					sprintf(
						// translators: %d is the blog ID.
						__( 'No Blog was found with ID: %d', 'wp-graphql-buddypress' ),
						absint( $key )
					)
				);
			}

			/**
			 * Return the instance through the Model Layer to ensure we only return
			 * values the consumer has access to.
			 */
			$loaded_blogs[ $key ] = new Blog( $blog_object );
		}

		return $loaded_blogs;
	}
}
