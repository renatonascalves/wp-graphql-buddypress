<?php
/**
 * Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Groups_Group;

/**
 * Class Group - Models the data for the Group object type.
 *
 * @property string $id ID.
 * @property int $databaseId Group ID.
 * @property int $parent Group parent ID.
 * @property int $creator Group creator ID.
 * @property string $name Group name.
 * @property string $slug Group slug.
 * @property string $description Group description.
 * @property string $link Group link.
 * @property bool $hasForum Group has forum.
 * @property int $totalMemberCount Total number of group member.
 * @property string $dateCreated Date group was created.
 * @property string $status Group status.
 * @property array $types Group types.
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

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'groups' ] ) ) {
			return true;
		}

		// Public groups are open.
		if ( 'public' === $this->data->status ) {
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
				'id'               => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'group', (string) $this->data->id )
						: null;
				},
				'databaseId'       => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'parent'           => function () {
					return ! empty( $this->data->parent_id ) ? absint( $this->data->parent_id ) : null;
				},
				'creator'          => function () {
					return ! empty( $this->data->creator_id ) ? $this->data->creator_id : null;
				},
				'name'             => function () {
					return ! empty( $this->data->name ) ? $this->data->name : null;
				},
				'slug'             => function () {
					return bp_get_group_slug( $this->data->slug );
				},
				'description'      => function () {
					return ! empty( $this->data->description ) ? $this->data->description : null;
				},
				'uri'              => function () {
					return bp_get_group_url( $this->data );
				},
				'hasForum'         => function () {
					return wp_validate_boolean( $this->data->enable_forum );
				},
				'totalMemberCount' => [
					'callback'   => function () {
						return groups_get_groupmeta( $this->data->id, 'total_member_count' );
					},
					'capability' => 'bp_moderate',
				],
				'lastActivity'     => [
					'callback'   => function () {
						return Utils::prepare_date_response( groups_get_groupmeta( $this->data->id, 'last_activity' ) );
					},
					'capability' => 'bp_moderate',
				],
				'dateCreated'      => function () {
					return Utils::prepare_date_response( $this->data->date_created );
				},
				'status'           => function () {
					return bp_get_group_status( $this->data );
				},
			];
		}
	}
}
