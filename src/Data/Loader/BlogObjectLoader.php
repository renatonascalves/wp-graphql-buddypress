<?php
/**
 * BlogObjectLoader Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Loader
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQL\Extensions\BuddyPress\Data\BlogHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use stdClass;

/**
 * Class BlogObjectLoader
 */
class BlogObjectLoader extends AbstractDataLoader {

	/**
	 * Get model.
	 *
	 * @param mixed $entry The object.
	 * @param mixed $key   The Key to identify the object by.
	 * @return null|Blog
	 */
	protected function get_model( $entry, $key ): ?Blog {

		// Check if friendship exists.
		if ( ! is_object( $entry ) ) {
			return null;
		}

		return new Blog( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
	 *
	 * @param array $keys Array of keys/ids.
	 * @return stdClass[]
	 */
	public function loadKeys( array $keys ): array {

		if ( empty( $keys ) ) {
			return $keys;
		}

		// Execute the query, and prune the cache.
		bp_blogs_get_blogs( [ 'include_blog_ids' => $keys ] );

		$loaded_blogs = [];

		// Get all objects.
		foreach ( $keys as $key ) {
			$loaded_blogs[ $key ] = BlogHelper::get_blog_from_input( absint( $key ) );
		}

		return $loaded_blogs;
	}
}
