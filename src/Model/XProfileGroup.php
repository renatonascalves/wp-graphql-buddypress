<?php
/**
 * XProfile Group Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;

/**
 * Class XProfile Group - Models the data for the XProfile Group object type.
 *
 * @property string $id
 * @property string $groupId
 * @property string $name
 * @property string $description
 * @property string $groupOrder
 * @property string $canDelete
 */
class XProfileGroup extends Model {

	/**
	 * Stores the object for the incoming data.
	 *
	 * @var object $data
	 */
	protected $data;

	/**
	 * XProfile group constructor.
	 *
	 * @param object $xprofile_group The incoming XProfile object that needs modeling.
	 */
	public function __construct( object $xprofile_group ) {
		$this->data = $xprofile_group;
		parent::__construct();
	}

	/**
	 * Initialize the Xprofile Group object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'          => function() {
					return ! empty( $this->data->id ) ? Relay::toGlobalId( 'xprofile_group_object', $this->data->id ) : null;
				},
				'groupId'     => function() {
					return ! empty( $this->data->id ) ? $this->data->id : null;
				},
				'name'        => function() {
					return ! empty( $this->data->name ) ? $this->data->name : null;
				},
				'description' => function() {
					return ! empty( $this->data->description ) ? $this->data->description : null;
				},
				'groupOrder'  => function() {
					return $this->data->group_order;
				},
				'canDelete'   => function() {
					return $this->data->can_delete;
				},
			];
		}
	}
}
