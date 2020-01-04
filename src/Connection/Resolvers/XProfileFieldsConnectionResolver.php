<?php
/**
 * XProfileFieldsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Types;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;

/**
 * Class XProfileFieldsConnectionResolver
 */
class XProfileFieldsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args() {
		$query_args = [
			'profile_group_id' => false,
			'fetch_fields'     => true,
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
		 * Set the graphql_cursor_offset
		 */
		$query_args['graphql_cursor_offset']  = $this->get_offset();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		/**
		 * Pass the graphql $this->args.
		 */
		$query_args['graphql_args'] = $this->args;

		/**
		 * Setting profile group ID.
		 */
		if ( true === is_object( $this->source ) && $this->source instanceof XProfileGroup ) {
			$query_args['profile_group_id'] = $this->source->groupId;
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
			'graphql_xprofile_fields_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the XProfile groups query, with fields.
	 *
	 * @return array
	 */
	public function get_query() {
		return bp_xprofile_get_groups( $this->query_args );
	}

	/**
	 * Returns an array of XProfile fields IDs.
	 *
	 * @return array
	 */
	public function get_items() {
		$ids = [];
		foreach ( $this->query as $group ) {
			foreach ( $group->fields as $field ) {
				$ids[] = $field->id;
			}
		}
		return array_map( 'absint', $ids );
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
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_XProfile_Group::get() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 *
	 * @return array
	 */
	public function sanitize_input_fields( array $args ) {
		$arg_mapping = [
			'hideEmptyFields' => 'hide_empty_fields',
			'excludeFields'   => 'exclude_fields',
		];

		/**
		 * Map and sanitize the input args.
		 */
		$query_args = Types::map_input( $args, $arg_mapping );

		/**
		 * This allows plugins/themes to hook in and alter what $args should be allowed.
		 */
		$query_args = apply_filters(
			'graphql_map_input_fields_to_xprofile_fields_query',
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
