<?php
/**
 * BlogsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Model\User;

/**
 * Class BlogsConnectionResolver
 */
class BlogsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_blog';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'include_blog_ids' => false,
			'user_id'          => false,
			'search_terms'     => false,
			'type'             => 'newest',
		];

		// Prepare for later use.
		$first = $this->args['first'] ?? null;
		$last  = $this->args['last'] ?? null;

		// Collect the input_fields.
		$input_fields = $this->sanitize_input_fields( $this->args['where'] ?? [] );

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		// Set per_page the highest value of $first and $last, with a (filterable) max of 100.
		$query_args['per_page'] = min( max( absint( $first ), absint( $last ), 20 ), $this->get_query_amount() ) + 1;

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset_for_cursor();
		$query_args['graphql_cursor_compare'] = ! empty( $last ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		// Setting the user ID whose blogs user can post to.
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
		return (array) apply_filters(
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
	public function get_query(): array {
		return bp_blogs_get_blogs( $this->query_args );
	}

	/**
	 * Returns an array of blog IDs.
	 *
	 * @return array
	 */
	public function get_ids(): array {
		$blog_ids = wp_list_pluck( $this->query['blogs'], 'blog_id' );

		if ( ! empty( $this->args['last'] ) ) {
			$blog_ids = array_reverse( $blog_ids );
		}

		return array_values( array_filter( wp_parse_id_list( $blog_ids ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {
		return bp_current_user_can( 'bp_view', [ 'bp_component' => 'blogs' ] );
	}

	/**
	 * Determine whether or not the the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		$blogs = current( bp_blogs_get_blogs( [ 'include_blog_ids' => absint( $offset ) ] ) );

		return ( ! empty( $blogs[0] ) && is_object( $blogs[0] ) );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * bp_blogs_get_blogs() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'userId'  => 'user_id',
				'include' => 'include_blog_ids',
				'search'  => 'search_terms',
				'type'    => 'type',
			]
		);

		/**
		 * This allows plugins/themes to hook in and alter what $args should be allowed.
		 *
		 * @param array              $query_args The mapped query arguments.
		 * @param array              $args       Query "where" args.
		 * @param mixed              $source     The query results for a query calling this.
		 * @param array              $all_args   All of the arguments for the query (not just the "where" args).
		 * @param AppContext         $context    The AppContext object.
		 * @param ResolveInfo        $info       The ResolveInfo object.
		 */
		return (array) apply_filters(
			'graphql_map_input_fields_to_blogs_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
