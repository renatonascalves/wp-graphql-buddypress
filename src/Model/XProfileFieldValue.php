<?php
/**
 * XProfile Field Value Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use WPGraphQL\Model\Model;
use WPGraphQL\Utils\Utils;
use BP_XProfile_ProfileData;

/**
 * Class XProfile Field Value - Models the data for the XProfile Field Value object type.
 */
class XProfileFieldValue extends Model {

	/**
	 * Stores the object for the incoming data.
	 *
	 * @var BP_XProfile_ProfileData
	 */
	protected $data;

	/**
	 * XProfile Profile Data constructor.
	 *
	 * @param BP_XProfile_ProfileData $xprofile_field_data The XProfile Profile Data object.
	 */
	public function __construct( BP_XProfile_ProfileData $xprofile_field_data ) {
		$this->data = $xprofile_field_data;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// Empty data/value is not private.
		if ( empty( $this->data->value ) ) {
			return false;
		}

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'xprofile' ] ) ) {
			return true;
		}

		$hidden_user_fields = (array) bp_xprofile_get_hidden_fields_for_user(
			$this->data->user_id,
			$this->current_user->ID
		);

		return in_array( $this->data->id, $hidden_user_fields, true );
	}

	/**
	 * Initialize the XProfile field value object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'raw'            => function () {
					return ! empty( $this->data->value ) ? $this->data->value : null;
				},
				'unserialized'   => function () {
					return $this->get_unserialized_value( $this->data->value );
				},
				'rendered'       => function () {
					return $this->get_rendered_value( $this->data->value, $this->data->field_id );
				},
				'lastUpdated'    => function () {
					return Utils::prepare_date_response( $this->data->last_updated, get_date_from_gmt( $this->data->last_updated ) );
				},
				'lastUpdatedGmt' => function () {
					return Utils::prepare_date_response( $this->data->last_updated );
				},
			];
		}
	}

	/**
	 * Retrieve the unserialized value of a profile field.
	 *
	 * @param string $value The raw value of the field.
	 * @return array
	 */
	protected function get_unserialized_value( $value = '' ): array {
		if ( empty( $value ) ) {
			return [];
		}

		$unserialized_value = maybe_unserialize( $value );
		if ( ! is_array( $unserialized_value ) ) {
			$unserialized_value = (array) $unserialized_value;
		}

		return $unserialized_value;
	}

	/**
	 * Retrieve the rendered value of a profile field.
	 *
	 * @param string   $value         The raw value of the field.
	 * @param int|null $profile_field The ID of the object for the field.
	 * @return string
	 */
	protected function get_rendered_value( $value = '', $profile_field = null ): string {
		if ( ! $value ) {
			return '';
		}

		$profile_field = xprofile_get_field( $profile_field );

		if ( ! isset( $profile_field->id ) ) {
			return '';
		}

		// Unserialize the BuddyPress way.
		$value = bp_unserialize_profile_field( $value );

		global $field;
		$reset_global = $field;

		// Set the $field global as the `xprofile_filter_link_profile_data` filter needs it.
		$field = $profile_field;

		/**
		 * Apply filters to sanitize XProfile field value.
		 *
		 * @param string $value Value for the profile field.
		 * @param string $type  Type for the profile field.
		 * @param int    $id    ID for the profile field.
		 */
		$value = apply_filters( 'bp_get_the_profile_field_value', $value, $field->type, $field->id );

		// Reset the global before returning the value.
		$field = $reset_global;

		return $value;
	}
}
