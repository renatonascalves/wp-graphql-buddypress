<?php
/**
 * BlogsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Types;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Model\User;

/**
 * Class BlogsConnectionResolver
 */
class BlogsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args() {
		$query_args = [
			'include_blog_ids' => [],
			'user_id'          => 0,
			'search_terms'     => false,
			'type'             => 'active',
		];

		/**
		 * Prepare for later use.
		 */
		$last = $this->args['last'] ?? null;

		/**
		 * Collect the input_fields.
		 */
		$input_fields = [];
		if ( ! empty( $this->args['where'] ) ) {
			$input_fields = $this->sanitize_input_fields( $this->args['where'] );
		}

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		/**
		 * If there's no orderby params in the inputArgs, set order based on the first/last argument
		 */
		if ( empty( $query_args['order'] ) ) {
			$query_args['order'] = ! empty( $last ) ? 'ASC' : 'DESC';
		}

		/**
		 * Set the graphql_cursor_offset
		 */
		$query_args['graphql_cursor_offset']  = $this->get_offset();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		/**
		 * Pass the graphql $this->args.
		 */
		$query_args['graphql_args'] = $this->args;

		/**
		 * Setting the user ID whose blogs user can post to.
		 */
		if ( true === is_object( $this->source ) && $this->source instanceof User ) {
			$query_args['user_id'] = $this->source->userId;
		}

		/**
		 * Filter the query_args that should be applied to the query. This filter is applied AFTER the input args from
		 * the GraphQL Query have been applied and has the potential to override the GraphQL Query Input Args.
		 *
		 * @param array       $query_args An array of query_args being passed.
		 * @param mixed       $source     Source passed down from the resolve tree.
		 * @param array       $args       An array of arguments input in the field as part of the GraphQL query
		 * @param AppContext  $context    Context passed down the resolve tree.
		 * @param ResolveInfo $info       Resolver info about fields passed down the resolve tree.
		 */
		return apply_filters(
			'graphql_blogs_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the blogs query.
	 *
	 * @return array
	 */
	public function get_query() {
		return bp_blogs_get_blogs( $this->query_args );
	}

	/**
	 * Returns an array of blogs.
	 *
	 * @return array
	 */
	public function get_items() {
		return wp_list_pluck( $this->query['blogs'], 'blog_id' );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute() {
		return true;
	}

	/**
	 * Determine whether or not the the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 *
	 * @return bool
	 */
	public function is_valid_offset( $offset ) {
		$blogs = current( bp_blogs_get_blogs( [ 'include_blog_ids' => absint( $offset ) ] ) );

		return ! empty( $blogs[0] );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * bp_blogs_get_blogs() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 *
	 * @return array
	 */
	public function sanitize_input_fields( array $args ) {
		$arg_mapping = [
			'userId'  => 'user_id',
			'include' => 'include_blog_ids',
			'search'  => 'search_terms',
			'type'    => 'type',
		];

		/**
		 * Map and sanitize the input args to the bp_blogs_get_blogs compatible args.
		 */
		$query_args = Types::map_input( $args, $arg_mapping );

		/**
		 * This allows plugins/themes to hook in and alter what $args should be allowed.
		 *
		 * @param array       $query_args An array of query_args being passed.
		 * @param array       $args       An array of arguments input in the field as part of the GraphQL query
		 * @param AppContext  $context    Context being passed.
		 * @param ResolveInfo $info       Info about the resolver.
		 */
		$query_args = apply_filters(
			'graphql_map_input_fields_to_blogs_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);

		if ( empty( $query_args ) || ! is_array( $query_args ) ) {
			return [];
		}

		return $query_args;
	}
}
