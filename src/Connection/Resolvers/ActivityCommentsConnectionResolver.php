<?php
/**
 * ActivityCommentsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use BP_Activity_Activity;

/**
 * Class ActivityCommentsConnectionResolver
 */
class ActivityCommentsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_activity';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'order'  => 'ASC',
			'status' => 'ham_only',
		];

		// Prepare for later use.
		$first = $this->args['first'] ?? null;
		$last  = $this->args['last'] ?? null;

		// Collect the input_fields.
		$input_fields = $this->sanitize_input_fields( $this->args['where'] ?? [] );

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		// Set order when using the last param.
		if ( ! empty( $last ) ) {
			$query_args['order'] = 'DESC';
		}

		// Set per_page the highest value of $first and $last, with a (filterable) max of 100.
		$query_args['per_page'] = min( max( absint( $first ), absint( $last ), 20 ), $this->get_query_amount() ) + 1;

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset_for_cursor();
		$query_args['graphql_cursor_compare'] = ! empty( $last ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		/**
		 * Filter the query_args that should be applied to the query. This filter is applied AFTER the input args from
		 * the GraphQL Query have been applied and has the potential to override the GraphQL Query Input Args.
		 *
		 * @param array       $query_args array of query_args being passed to the
		 * @param mixed       $source     Source passed down from the resolve tree
		 * @param array       $args       array of arguments input in the field as part of the GraphQL query
		 * @param AppContext  $context    object passed down zthe resolve tree
		 * @param ResolveInfo $info       info about fields passed down the resolve tree
		 */
		return (array) apply_filters(
			'graphql_activity_comments_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the activity comments query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return (array) BP_Activity_Activity::get_child_comments( $this->source->databaseId );
	}

	/**
	 * Return an array of activity comments ids from the query.
	 *
	 * @return int[]
	 */
	public function get_ids(): array {
		$comments = wp_list_pluck( $this->query ?? [], 'id' );

		if ( 'DESC' === $this->query_args['order'] ) {
			$comments = array_reverse( $comments );
		}

		return array_values( array_filter( wp_parse_id_list( $comments ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {
		return true;
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		return ActivityHelper::activity_exists( absint( $offset ) );
	}

	/**
	 * This sets up the "allowed" args.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'order'  => 'order',
				'status' => 'status',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_activity_comments_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
