<?php
/**
 * NotificationConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use BP_Notifications_Notification;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\NotificationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Model\User;
use WPGraphQL\Utils\Utils;

/**
 * Class NotificationConnectionResolver
 */
class NotificationConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_notification';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'component_action'  => false,
			'component_name'    => false,
			'is_new'            => true,
			'item_id'           => 0,
			'order_by'          => 'id',
			'search_terms'      => false,
			'secondary_item_id' => 0,
			'sort_order'        => 'DESC',
			'user_id'           => bp_loggedin_user_id(),
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
			$query_args['sort_order'] = 'DESC';
		}

		// Set sort when using the order_by param.
		if ( ! empty( $this->args['where']['orderBy'] ) ) {
			$query_args['sort_order'] = 'ASC';
		}

		// Set number the highest value of $first and $last, with a (filterable) max of 100.
		$query_args['per_page'] = min( max( absint( $first ), absint( $last ), 20 ), $this->get_query_amount() ) + 1;

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset_for_cursor();
		$query_args['graphql_cursor_compare'] = ! empty( $last ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		// Set User.
		if ( true === is_object( $this->source ) && $this->source instanceof User ) {
			$query_args['user_id'] = $this->source->userId;
		}

		// Set Group.
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['item_id'] = $this->source->databaseId;
		}

		// Set Blog.
		if ( true === is_object( $this->source ) && $this->source instanceof Blog ) {
			$query_args['item_id'] = $this->source->databaseId;
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
			'graphql_notification_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns the notification query.
	 *
	 * @return BP_Notifications_Notification[]
	 */
	public function get_query(): array {
		return BP_Notifications_Notification::get( $this->query_args );
	}

	/**
	 * Return an array of notification ids from the query.
	 *
	 * @todo update BP_Notifications_Notification at BP core to turn IDs only.
	 *
	 * @return int[]
	 */
	public function get_ids(): array {
		$ids = wp_list_pluck( $this->query ?? [], 'id' );

		return array_values( array_filter( wp_parse_id_list( $ids ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {

		// Needs to be logged in.
		if ( false === is_user_logged_in() ) {
			return false;
		}

		// Moderators can do anything.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		// ID of the current logged in user.
		$user_id = bp_loggedin_user_id();

		// Logged in user is the same one from the current user object.
		if (
			$this->source instanceof User
			&& isset( $this->source->userId )
			&& $user_id === $this->source->userId
		) {
			return true;
		}

		// Logged in user is the same on from the params.
		if (
			! empty( $this->args['where']['userIds'] )
			&& false === in_array( $user_id, $this->args['where']['userIds'], true )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		return NotificationHelper::notification_exists( absint( $offset ) );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_Notifications_Notification::get() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'componentAction'  => 'component_action',
				'componentName'    => 'component_name',
				'include'          => 'id',
				'isNew'            => 'is_new',
				'itemIds'          => 'item_id',
				'order'            => 'sort_order',
				'orderBy'          => 'order_by',
				'search'           => 'search_terms',
				'secondaryItemIds' => 'secondary_item_id',
				'userIds'          => 'user_id',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_notification_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
