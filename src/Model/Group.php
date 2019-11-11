<?php
/**
 * Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;

/**
 * Class Group - Models the data for the Group object type
 *
 * @property string $id
 * @property string $groupId
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
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
	 * @param \BP_Groups_Group $group The incoming BP_Groups_Group object that needs modeling
	 */
	public function __construct( \BP_Groups_Group $group ) {
		$this->data = $group;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not
	 *
	 * @return bool
	 */
	protected function is_private() {

		if ( bp_current_user_can( 'bp_moderate' ) || 'public' === $this->data->status ) {
			return false;
		}

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
				'id'      => function() {
					return ! empty( $this->data->id ) ? Relay::toGlobalId( 'group', $this->data->id ) : null;
				},
				'groupId' => function() {
					return ! empty( $this->data->id ) ? $this->data->id : null;
				},
			];
		}
	}
}
