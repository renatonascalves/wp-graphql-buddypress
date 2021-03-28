<?php
/**
 * Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use WPGraphQL\Utils\Utils;
use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use BP_Groups_Group;

/**
 * Class Group - Models the data for the Group object type.
 */
class Group extends Model {

	/**
	 * Stores the BP_Groups_Group object for the incoming data.
	 *
	 * @var BP_Groups_Group
	 */
	protected $data;

	/**
	 * Group constructor.
	 *
	 * @param BP_Groups_Group $group The BP_Groups_Group object.
	 */
	public function __construct( BP_Groups_Group $group ) {
		$this->data = $group;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// If it is not a private group, user can see it.
		if ( in_array( $this->data->status, [ 'hidden', 'public' ], true ) ) {
			return false;
		}

		// If the group is not public, check if current user is a moderator/admin.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return false;
		}

		// Now check if the user is the group creator.
		if ( true === $this->owner_matches_current_user() ) {
			return false;
		}

		// Now check if the user is a member of the group.
		if ( groups_is_user_member( $this->current_user->ID, $this->data->id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the Group object
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id' => function() {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'group', (string) $this->data->id )
						: null;
				},
				'groupId' => function() {
					return $this->data->id ?? null;
				},
				'parent' => function() {
					return $this->data->parent_id ?? null;
				},
				'creator' => function() {
					return $this->data->creator_id ?? null;
				},
				'name' => function() {
					return $this->data->name ?? null;
				},
				'slug' => function() {
					return $this->data->slug ?? null;
				},
				'description' => function() {
					return $this->data->description ?? null;
				},
				'link' => function() {
					return bp_get_group_permalink( $this->data ) ?? null;
				},
				'hasForum' => function() {
					return $this->data->enable_forum ?? null;
				},
				'totalMemberCount' => [
					'callback' => function() {
						return groups_get_groupmeta( $this->data->id, 'total_member_count' );
					},
					'capability' => 'bp_moderate',
				],
				'lastActivity' => [
					'callback' => function() {
						return Utils::prepare_date_response( groups_get_groupmeta( $this->data->id, 'last_activity' ) );
					},
					'capability' => 'bp_moderate',
				],
				'dateCreated' => function() {
					return Utils::prepare_date_response( $this->data->date_created );
				},
				'status' => function() {
					return bp_get_group_status( $this->data );
				},
			];
		}
	}
}
