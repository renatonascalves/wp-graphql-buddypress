<?php
/**
 * ActivitiesConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\User;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class ActivitiesConnectionResolver
 */
class ActivitiesConnectionResolver extends AbstractConnectionResolver {

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
			'search_terms'     => false,
			'sort'             => 'DESC',
			'scope'            => false,
			'exclude'          => false,
			'in'               => false,
			'display_comments' => false,
			'spam'             => 'ham_only',
			'fields'           => 'ids',
			'show_hidden'      => false,
			'filter'           => [],
		];

		// Prepare for later use.
		$first = $this->args['first'] ?? null;
		$last  = $this->args['last'] ?? null;

		// Collect the input_fields.
		$input_fields = $this->sanitize_input_fields( $this->args['where'] ?? [] );

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
		}

		$item_id = 0;

		// Groups.
		if ( ! empty( $query_args['group_id'] ) ) {
			$query_args['filter']['object']     = 'groups';
			$query_args['filter']['primary_id'] = $query_args['group_id'];

			if ( empty( $query_args['component'] ) || 'groups' !== $query_args['component'] ) {
				$query_args['component'] = 'groups';
			}

			$item_id = $query_args['group_id'];
		}

		// Site.
		if ( ! empty( $query_args['site_id'] ) ) {
			$query_args['filter']['object']     = 'blogs';
			$query_args['filter']['primary_id'] = $query_args['site_id'];
			$item_id                            = $query_args['site_id'];
		}

		if ( empty( $query_args['group_id'] ) && empty( $query_args['site_id'] ) ) {
			if ( ! empty( $query_args['component'] ) ) {
				$query_args['filter']['object'] = $query_args['component'];
			}

			if ( ! empty( $query_args['primary_id'] ) ) {
				$query_args['filter']['primary_id'] = $query_args['primary_id'];
				$item_id                            = $query_args['primary_id'];
			}
		}

		// User.
		if ( ! empty( $query_args['user_id'] ) ) {
			$query_args['filter']['user_id'] = $query_args['user_id'];
		}

		// Display comments.
		if ( true === boolval( $query_args['display_comments'] ) ) {
			$query_args['display_comments'] = 'stream';
		}

		// Set Type.
		if ( ! empty( $query_args['type'] ) ) {
			$query_args['filter']['action'] = $query_args['type'];
		}

		// Set order when using the last param.
		if ( ! empty( $last ) ) {
			$query_args['sort'] = 'DESC';
		}

		// Setting the user ID whose activities we wanna fetch.
		if ( true === is_object( $this->source ) && $this->source instanceof User ) {
			$query_args['filter']['user_id'] = $this->source->userId;
		}

		// Setting the site ID whose activities we wanna fetch.
		if ( true === is_object( $this->source ) && $this->source instanceof Blog ) {
			$query_args['filter']['object']     = 'blogs';
			$query_args['filter']['primary_id'] = $this->source->databaseId;
			$item_id                            = $this->source->databaseId;
		}

		// Setting the group ID whose activities we wanna fetch.
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['component']            = 'groups';
			$query_args['filter']['object']     = 'groups';
			$query_args['filter']['primary_id'] = $this->source->databaseId;
			$item_id                            = $this->source->databaseId;
		}

		// See if the user can see hidden activities.
		if ( true === ActivityHelper::show_hidden( $query_args['component'] ?? '', $item_id ) ) {
			$query_args['show_hidden'] = true;
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
			'graphql_activities_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the activities query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return bp_activity_get( $this->query_args );
	}

	/**
	 * Return an array of activity ids from the query.
	 *
	 * @return int[]
	 */
	public function get_ids(): array {
		$activities = $this->query['activities'] ?? [];

		return array_values( array_filter( wp_parse_id_list( $activities ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {
		return bp_current_user_can( 'bp_view', [ 'bp_component' => 'activity' ] );
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
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_Activity_Activity::get() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'type'            => 'type',
				'after'           => 'since',
				'search'          => 'search_terms',
				'order'           => 'sort',
				'slug'            => 'slug',
				'scope'           => 'scope',
				'status'          => 'spam',
				'component'       => 'component',
				'userId'          => 'user_id',
				'groupId'         => 'group_id',
				'siteId'          => 'site_id',
				'primaryId'       => 'primary_id',
				'include'         => 'in',
				'exclude'         => 'exclude',
				'displayComments' => 'display_comments',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_activities_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
