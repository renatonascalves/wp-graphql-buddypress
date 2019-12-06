<?php
/**
 * GroupMembersConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data\Connection
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data\Connection;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Types;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupMembersConnectionResolver
 */
class GroupMembersConnectionResolver extends AbstractConnectionResolver {

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args() {
		$query_args = [
			'group_id'            => 0,
			'exclude'             => false,
			'search_terms'        => false,
			'type'                => 'last_joined',
			'group_role'          => [],
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
		];

		/**
		 * Prepare for later use
		 */
		$last = ! empty( $this->args['last'] ) ? $this->args['last'] : null;

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
		 * Assign group.
		 */
		if ( true === is_object( $this->source ) && $this->source instanceof Group ) {
			$query_args['group_id'] = $this->source->groupId;
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
	public function get_query() {
		return groups_get_group_members( $this->query_args );
	}

	/**
	 * Returns an array of group members.
	 *
	 * @return array
	 */
	public function get_items() {
		return wp_list_pluck(
			$this->query['members'],
			'ID'
		);
	}

	/**
	 * This can be used to determine whether the connection query should even execute.
	 *
	 * @return bool
	 */
	public function should_execute() {

		$group_id = $this->query_args['group_id'];

		// It is okay for public groups.
		$group = groups_get_group( $group_id );
		if ( 'public' === $group->status ) {
			return true;
		}

		// Moderators.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		// User is a member of the group.
		if ( groups_is_user_member( bp_loggedin_user_id(), $group_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
	 * groups_get_group_members() friendly keys.
	 *
	 * @throws UserError User error.
	 *
	 * @param array $args The array of query arguments.
	 *
	 * @return array
	 */
	public function sanitize_input_fields( array $args ) {

		/**
		 * Only admins and mods can filter those.
		 */
		if ( ! empty( $args['excludeBanned'] ) && ! bp_current_user_can( 'bp_moderate' ) ) {
			throw new UserError( __( 'Sorry, you do not have the necessary permissions to filter with this param.', 'wp-graphql-buddypress' ) );
		}

		$arg_mapping = [
			'type'              => 'type',
			'exclude'           => 'exclude',
			'search'            => 'search_terms',
			'groupMemberRoles'  => 'group_role',
			'excludeAdminsMods' => 'exclude_admins_mods',
			'excludeBanned'     => 'exclude_banned',
		];

		/**
		 * Map and sanitize the input args.
		 */
		$query_args = Types::map_input( $args, $arg_mapping );

		/**
		 * This allows plugins/themes to hook in and alter what $args should be allowed.
		 */
		$query_args = apply_filters(
			'graphql_map_input_fields_to_group_members_query',
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
