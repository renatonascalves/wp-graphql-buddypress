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
use WPGraphQL\Extensions\BuddyPress\Data\BlogMutation;
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
		if ( ! $entry instanceof stdClass ) {
			return null;
		}

		return new Blog( $entry );
	}

	/**
	 * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
	 * values.
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
		bp_blogs_get_blogs( [ 'include_blog_ids' => $keys ] );

		$loaded_blogs = [];

		// Get all objects and add them to cache.
		foreach ( $keys as $key ) {
			$loaded_blogs[ $key ] = BlogMutation::get_blog_from_input( absint( $key ) );
		}

		return $loaded_blogs;
	}
}
