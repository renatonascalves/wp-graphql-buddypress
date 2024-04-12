<?php
/**
 * XProfile Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use stdClass;

/**
 * Class XProfile Group - Models the data for the XProfile Group object type.
 *
 * @property string $id ID.
 * @property int    $databaseId XProfile group ID.
 * @property string $name XProfile group name.
 * @property string $description XProfile group description.
 * @property int    $groupOrder XProfile group order.
 * @property bool   $canDelete Can delete XProfile group.
 * @property int    $userId User ID.
 */
class XProfileGroup extends Model {

	/**
	 * Stores the object for the incoming data.
	 *
	 * @var stdClass
	 */
	protected $data;

	/**
	 * XProfile group constructor.
	 *
	 * @param stdClass $xprofile_group The XProfile object.
	 */
	public function __construct( stdClass $xprofile_group ) {
		$this->data = $xprofile_group;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'xprofile' ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the XProfile group object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'          => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'bp_xprofile_group', $this->data->id )
						: null;
				},
				'databaseId'  => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'name'        => function () {
					return $this->data->name ?? null;
				},
				'description' => function () {
					return $this->data->description ?? null;
				},
				'groupOrder'  => function () {
					return $this->data->group_order;
				},
				'canDelete'   => function () {
					return wp_validate_boolean( $this->data->can_delete );
				},
				'userId'      => function () {
					return $this->data->userId ?? null;
				},
			];
		}
	}
}
