<?php
/**
 * XProfileFieldsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileFieldHelper;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;
use BP_XProfile_Field;

/**
 * Class XProfileFieldsConnectionResolver
 */
class XProfileFieldsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_xprofile_field';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'profile_group_id'  => false,
			'member_type'       => false,
			'user_id'           => 0,
			'fetch_field_data'  => true,
			'fetch_fields'      => true,
			'hide_empty_fields' => false,
		];

		// Prepare for later use.
		$last = $this->args['last'] ?? null;

		// Collect the input_fields.
		$input_fields = $this->sanitize_input_fields( $this->args['where'] ?? [] );

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset_for_cursor();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		// Setting profile group ID.
		if ( true === is_object( $this->source ) && $this->source instanceof XProfileGroup ) {
			$query_args['profile_group_id'] = $this->source->databaseId;
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
		return (array) apply_filters(
			'graphql_xprofile_fields_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the XProfile groups query, with its fields.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return bp_xprofile_get_groups( $this->query_args );
	}

	/**
	 * Returns an array of XProfile fields IDs.
	 *
	 * @return array
	 */
	public function get_ids(): array {
		$ids = [];
		foreach ( $this->query as $group ) {
			foreach ( $group->fields as $field ) {
				$ids[] = $field->id;
			}
		}

		return array_values( array_filter( wp_parse_id_list( $ids ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {
		return bp_current_user_can( 'bp_view', [ 'bp_component' => 'xprofile' ] );
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		$object = XProfileFieldHelper::get_xprofile_field_from_input( absint( $offset ) );

		return ( $object instanceof BP_XProfile_Field );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_XProfile_Group::get() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'hideEmptyFields' => 'hide_empty_fields',
				'excludeFields'   => 'exclude_fields',
				'memberType'      => 'member_type',
				'userId'          => 'user_id',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_xprofile_fields_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
