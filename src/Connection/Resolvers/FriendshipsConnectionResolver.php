<?php
/**
 * FriendshipsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Data\FriendshipMutation;
use WPGraphQL\Model\User;
use BP_Friends_Friendship;

/**
 * Class FriendshipsConnectionResolver
 */
class FriendshipsConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'bp_friend';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'user_id'      => 0,
			'is_confirmed' => null,
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
		if ( empty( $query_args['sort_order'] ) ) {
			$query_args['sort_order'] = ! empty( $last ) ? 'ASC' : 'DESC';
		}

		// Set the graphql_cursor_offset.
		$query_args['graphql_cursor_offset']  = $this->get_offset();
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		// Pass the graphql $this->args.
		$query_args['graphql_args'] = $this->args;

		// Setting the user ID whose friends we wanna fetch.
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
			'graphql_friendship_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Return the friendship query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return BP_Friends_Friendship::get_friendships( $this->source->userId ?? 0, $this->query_args );
	}

	/**
	 * Return an array of friend ids.
	 *
	 * @return array
	 */
	public function get_ids(): array {
		return array_map( 'absint', wp_list_pluck( $this->query, 'id' ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {

		// Moderators can see everything.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		// Logged in user is the same one from the current user object.
		return ( isset( $this->source->userId ) && bp_loggedin_user_id() === $this->source->userId );
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		return FriendshipMutation::friendship_exists(
			current( BP_Friends_Friendship::get_friendships_by_id( $offset ) )
		);
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * BP_Friends_Friendship::get_friendships friendly keys.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {
		$arg_mapping = [
			'order'       => 'sort_order',
			'isConfirmed' => 'is_confirmed',
		];

		// Map and sanitize the input args to the BP_Friends_Friendship::get_friendships compatible args.
		$query_args = Utils::map_input( $args, $arg_mapping );

		/**
		 * This allows plugins/themes to hook in and alter what $args should be allowed.
		 *
		 * @param array       $query_args An array of query_args being passed.
		 * @param array       $args       An array of arguments input in the field as part of the GraphQL query
		 * @param AppContext  $context    Context being passed.
		 * @param ResolveInfo $info       Info about the resolver.
		 */
		$query_args = apply_filters(
			'graphql_map_input_fields_to_friendship_query',
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
