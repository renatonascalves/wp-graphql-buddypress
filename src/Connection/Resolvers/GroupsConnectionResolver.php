<?php
/**
 * GroupsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupsConnectionResolver
 */
class GroupsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_group';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'fields'      => 'ids',
			'show_hidden' => false,
			'user_id'     => 0,
			'include'     => [],
			'exclude'     => [],
			'meta'        => [],
			'orderby'     => 'date_created',
			'type'        => 'active',
		];

		// Prepare for later use.
		$last = $this->args['last'] ?? null;

		// Collect the input_fields.
		$input_fields = [];
		if ( ! empty( $this->args['where'] ) ) {
			$input_fields = $this->sanitize_input_fields( $this->args['where'] );
		}

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		// If there's no orderby params in the inputArgs, set order based on the first/last argument.
		if ( empty( $query_args['order'] ) ) {
			$query_args['order'] = ! empty( $last ) ? 'ASC' : 'DESC';
		}

		if ( ! is_user_logged_in() && empty( $query_args['status'] ) ) {
			$query_args['status'] = 'public';
		}

		// Adding correct value for the parent_id.
		if ( empty( $query_args['parent_id'] ) ) {
			$query_args['parent_id'] = null;
		}

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		// Setting parent group.
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['parent_id'] = $this->source->groupId;
		}

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
		return apply_filters(
			'graphql_groups_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the groups query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return groups_get_groups( $this->query_args );
	}

	/**
	 * Return an array of group ids from the query.
	 *
	 * @return array
	 */
	public function get_ids(): array {
		return $this->query['groups'];
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
		return ! empty( groups_get_group( absint( $offset ) ) );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_Groups_Group::get() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {
		$arg_mapping = [
			'showHidden' => 'show_hidden',
			'type'       => 'type',
			'order'      => 'order',
			'orderBy'    => 'orderby',
			'parent'     => 'parent_id',
			'search'     => 'search_terms',
			'slug'       => 'slug',
			'status'     => 'status',
			'userId'     => 'user_id',
			'groupType'  => 'group_type',
			'include'    => 'include',
			'exclude'    => 'exclude',
		];

		// Map and sanitize the input args to the BP_Groups_Group compatible args.
		$query_args = Utils::map_input( $args, $arg_mapping );

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		$query_args = apply_filters(
			'graphql_map_input_fields_to_groups_query',
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
