<?php
/**
 * XProfile Field Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use BP_XProfile_Field;
use BP_XProfile_ProfileData;

/**
 * Class XProfile Field - Models the data for the XProfile Field object type.
 *
 * @property string $id ID.
 * @property int $databaseId XProfile Field ID.
 * @property int $groupId Group of the XProfile Field ID.
 * @property int $parent Parent XProfile Field ID.
 * @property BP_XProfile_ProfileData $options XProfile field options.
 */
class XProfileField extends Model {

	/**
	 * Stores the BP_XProfile_Field object for the incoming data.
	 *
	 * @var BP_XProfile_Field
	 */
	protected $data;

	/**
	 * XProfile field constructor.
	 *
	 * @param BP_XProfile_Field $xprofile_field The BP_XProfile_Field object.
	 */
	public function __construct( BP_XProfile_Field $xprofile_field ) {
		$this->data = $xprofile_field;
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
	 * Initialize the XProfile field object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'              => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'bp_xprofile_field', (string) $this->data->id )
						: null;
				},
				'databaseId'      => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'groupId'         => function () {
					return ! empty( $this->data->group_id ) ? absint( $this->data->group_id ) : null;
				},
				'parent'          => function () {
					return ! empty( $this->data->parent_id ) ? absint( $this->data->parent_id ) : null;
				},
				'name'            => function () {
					return ! empty( $this->data->name ) ? $this->data->name : null;
				},
				'type'            => function () {
					return ! empty( $this->data->type ) ? $this->data->type : null;
				},
				'canDelete'       => function () {
					return wp_validate_boolean( $this->data->can_delete );
				},
				'isRequired'      => function () {
					return wp_validate_boolean( $this->data->is_required );
				},
				'fieldOrder'      => function () {
					return $this->data->field_order;
				},
				'optionOrder'     => function () {
					return $this->data->option_order;
				},
				'orderBy'         => function () {
					return ! empty( $this->data->order_by ) ? $this->data->order_by : null;
				},
				'isDefaultOption' => function () {
					return $this->data->is_default_option;
				},
				'visibilityLevel' => function () {
					$visibility = $this->data->get_default_visibility();

					return ! empty( $visibility ) ? $visibility : null;
				},
				'doAutolink'      => function () {
					return bp_xprofile_get_meta( $this->data->id, 'field', 'do_autolink' );
				},
				'description'     => function () {
					return ! empty( $this->data->description ) ? $this->data->description : null;
				},
				'options'         => function () {
					return $this->data;
				},
				'value'           => function () {
					return $this->data->data;
				},
				'memberTypes'     => function () {
					$types = $this->data->get_member_types();

					return ! empty( $types ) ? $types : null;
				},
			];
		}
	}
}
