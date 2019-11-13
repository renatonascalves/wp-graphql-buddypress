<?php
/**
 * Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WPGraphQL\Types;

/**
 * Class Group - Models the data for the Group object type
 *
 * @property string $id
 * @property string $groupId
 * @property string $parent
 * @property string $creatorId
 * @property string $name
 * @property string $slug
 * @property string $link
 * @property string $description
 * @property string $hasForum
 * @property string $totalMemberCount
 * @property string $lastActivity
 * @property string $dateCreated
 * @property string $status
 */
class Group extends Model {

	/**
	 * Stores the BP_Groups_Group object for the incoming data
	 *
	 * @var \BP_Groups_Group $data
	 */
	protected $data;

	/**
	 * Group constructor.
	 *
	 * @param \BP_Groups_Group $group The incoming BP_Groups_Group object that needs modeling.
	 */
	public function __construct( \BP_Groups_Group $group ) {
		$this->data = $group;

		$allowed_restricted_fields = [
			'id',
			'groupId',
			'parent',
			'creatorId',
			'name',
			'slug',
			'link',
			'description',
			'hasForum',
			'totalMemberCount',
			'lastActivity',
			'dateCreated',
			'status',
		];

		parent::__construct( 'bp_moderate', $allowed_restricted_fields, $group->creator_id );
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private() {

		// If it is not a hidden/private group, user can see it.
		if ( 'public' === $this->data->status ) {
			return false;
		}

		// Moderators.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return false;
		}

		// Group owners.
		if ( true === $this->owner_matches_current_user() ) {
			return false;
		}

		// User is a member of the group.
		if ( groups_is_user_member( bp_loggedin_user_id(), $this->data->id ) ) {
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
				'id'               => function() {
					return ! empty( $this->data->id ) ? Relay::toGlobalId( 'group', $this->data->id ) : null;
				},
				'groupId'          => function() {
					return ! empty( $this->data->id ) ? $this->data->id : 0;
				},
				'parent'           => function() {
					return ! empty( $this->data->parent_id ) ? $this->data->parent_id : 0;
				},
				'creatorId'        => function() {
					return ! empty( $this->data->creator_id ) ? $this->data->creator_id : 0;
				},
				'name'             => function() {
					return ! empty( $this->data->name ) ? $this->data->name : null;
				},
				'slug'             => function() {
					return ! empty( $this->data->slug ) ? $this->data->slug : null;
				},
				'description'      => function() {
					return ! empty( $this->data->description ) ? $this->data->description : null;
				},
				'link'             => function() {
					$link = bp_get_group_permalink( $this->data );
					return ! empty( $link ) ? $link : null;
				},
				'hasForum'         => function() {
					return bp_group_is_forum_enabled( $this->data );
				},
				'totalMemberCount' => function() {
					return groups_get_groupmeta( $this->data->id, 'total_member_count' );
				},
				'lastActivity'     => function() {
					return Types::prepare_date_response( groups_get_groupmeta( $this->data->id, 'last_activity' ) );
				},
				'dateCreated'      => function() {
					return Types::prepare_date_response( $this->data->date_created );
				},
				'status'           => function() {
					return bp_get_group_status( $this->data );
				},
			];
		}
	}
}
