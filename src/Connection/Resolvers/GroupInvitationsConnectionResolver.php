<?php
/**
 * GroupInvitationsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Model\User;

/**
 * Class GroupInvitationsConnectionResolver
 */
class GroupInvitationsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_invitation';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'fields'     => 'ids',
			'item_id'    => 0,
			'sort_order' => 'ASC',
			'order_by'   => 'id',
			'user_id'    => 0,
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

		// Set group.
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['item_id'] = $this->source->databaseId;
		}

		// Set user.
		if ( true === is_object( $this->source ) && $this->source instanceof User ) {
			$query_args['user_id'] = $this->source->databaseId;
		} else {
			$logged_user_id = bp_loggedin_user_id();

			// If the query is not restricted by user, limit it to the current user, if not an admin/group admin/group moderator.
			if (
				empty( $query_args['user_id'] )
				&& ! bp_current_user_can( 'bp_moderate' )
				&& ! groups_is_user_admin( $logged_user_id, $query_args['item_id'] )
				&& ! groups_is_user_mod( $logged_user_id, $query_args['item_id'] )
			) {
				$query_args['user_id'] = $logged_user_id;
			}
		}

		// Set per_page the highest value of $first and $last, with a (filterable) max of 100.
		$query_args['per_page'] = min( max( absint( $first ), absint( $last ), 20 ), $this->get_query_amount() ) + 1;

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset_for_cursor();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		/**
		 * Filter the query_args that should be applied to the query. This filter is applied AFTER the input args from
		 * the GraphQL Query have been applied and has the potential to override the GraphQL Query Input Args.
		 *
		 * @param array       $query_args An array of query_args being passed to the resolve tree
		 * @param mixed       $source     Source passed down from the resolve tree
		 * @param array       $args       An array of argument inputs in the field as part of the GraphQL query
		 * @param AppContext  $context    Object passed down the resolve tree
		 * @param ResolveInfo $info       Info about fields passed down the resolve tree
		 */
		return (array) apply_filters(
			'graphql_group_invitations_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns group invitations query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		$request_query = 'request' === $this->query_args['type'];

		// Remove type before querying.
		unset( $this->query_args['type'] );

		if ( true === $request_query ) {
			$query = groups_get_requests( $this->query_args );
		} else {
			$query = groups_get_invites( $this->query_args );
		}

		return (array) $query;
	}

	/**
	 * Returns an array of group invite ids.
	 *
	 * @return int[]
	 */
	public function get_ids(): array {
		return array_values( array_filter( wp_parse_id_list( $this->query ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {
		return bp_current_user_can( 'bp_view', [ 'bp_component' => 'groups' ] );
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		return InvitationHelper::invitation_exists( absint( $offset ) );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * groups_get_requests()|groups_get_invites() friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'userId' => 'user_id',
				'itemId' => 'item_id',
				'type'   => 'type',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_group_invitations_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
