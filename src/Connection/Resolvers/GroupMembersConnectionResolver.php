<?php
/**
 * GroupMembersConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupMembersConnectionResolver
 */
class GroupMembersConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Return the name of the loader to be used with the connection resolver.
	 *
	 * @return string
	 */
	public function get_loader_name(): string {
		return 'user';
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args(): array {
		$query_args = [
			'group_id'            => 0,
			'exclude'             => false,
			'search_terms'        => false,
			'type'                => 'last_joined',
			'group_role'          => [],
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
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

		// Assign group when trying to get members from a group.
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['group_id'] = $this->source->databaseId;
		}

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
			'graphql_group_members_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * Returns group members query.
	 *
	 * @return array
	 */
	public function get_query(): array {
		return (array) groups_get_group_members( $this->query_args );
	}

	/**
	 * Returns an array of group member ids.
	 *
	 * @return int[]
	 */
	public function get_ids(): array {
		$member_ids = wp_list_pluck( $this->query['members'], 'ID' );

		return array_values( array_filter( wp_parse_id_list( $member_ids ) ) );
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute(): bool {

		// Check if group object is there.
		if ( ! $this->source instanceof Group ) {
			return false;
		}

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'groups' ] ) ) {
			return false;
		}

		// It is okay for public groups.
		if ( 'public' === $this->source->status ) {
			return true;
		}

		// Moderators as well.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		// Current user is a member of the group.
		return (bool) groups_is_user_member( bp_loggedin_user_id(), $this->source->databaseId );
	}

	/**
	 * Determine whether or not the offset is valid.
	 *
	 * @param int $offset Offset ID.
	 * @return bool
	 */
	public function is_valid_offset( $offset ): bool {
		return ! empty( get_user_by( 'ID', absint( $offset ) ) );
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * groups_get_group_members() friendly keys.
	 *
	 * @throws UserError User error.
	 *
	 * @param array $args The array of query arguments.
	 * @return array
	 */
	public function sanitize_input_fields( array $args ): array {

		// Only admins can filter those.
		// @todo update so that mods are not blocked as well.
		if (
			(
				( ! empty( $args['groupMemberRoles'] ) && 'banned' === $args['groupMemberRoles'][0] )
				|| ! empty( $args['excludeBanned'] )
			)
			&& ! bp_current_user_can( 'bp_moderate' )
		) {
			throw new UserError( esc_html__( 'Sorry, you do not have the necessary permissions to filter with this param.', 'wp-graphql-buddypress' ) );
		}

		// Map and sanitize the input args.
		$query_args = Utils::map_input(
			$args,
			[
				'type'              => 'type',
				'exclude'           => 'exclude',
				'search'            => 'search_terms',
				'groupMemberRoles'  => 'group_role',
				'excludeAdminsMods' => 'exclude_admins_mods',
				'excludeBanned'     => 'exclude_banned',
			]
		);

		// This allows plugins/themes to hook in and alter what $args should be allowed.
		return (array) apply_filters(
			'graphql_map_input_fields_to_group_members_query',
			$query_args,
			$args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
