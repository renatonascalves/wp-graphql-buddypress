<?php
/**
 * XProfile Field Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
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
	 * Initialize the XProfile field object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'              => function() {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'bp_xprofile_field', (string) $this->data->id )
						: null;
				},
				'fieldId'         => function() {
					return $this->data->id ?? null;
				},
				'groupId'         => function() {
					return $this->data->group_id ?? null;
				},
				'parent'          => function() {
					return $this->data->parent_id ?? null;
				},
				'name'            => function() {
					return $this->data->name ?? null;
				},
				'type'            => function() {
					return $this->data->type ?? null;
				},
				'canDelete'       => function() {
					return $this->data->can_delete;
				},
				'isRequired'      => function() {
					return $this->data->is_required;
				},
				'fieldOrder'      => function() {
					return $this->data->field_order;
				},
				'optionOrder'     => function() {
					return $this->data->option_order;
				},
				'orderBy'         => function() {
					return $this->data->order_by ?? null;
				},
				'isDefaultOption' => function() {
					return $this->data->is_default_option;
				},
				'visibilityLevel' => function() {
					return $this->data->get_default_visibility() ?? null;
				},
				'doAutolink'      => function() {
					return bp_xprofile_get_meta( $this->data->id, 'field', 'do_autolink' );
				},
				'description'     => function() {
					return $this->data->description ?? null;
				},
				'options'         => function() {
					return $this->data ?? null;
				},
				'value'           => function() {
					return $this->data->data ?? null;
				},
				'memberTypes'     => function() {
					return $this->data->get_member_types() ?? null;
				},
			];
		}
	}
}
