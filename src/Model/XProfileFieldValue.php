<?php
/**
 * XProfile Field Value Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use WPGraphQL\Model\Model;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;
/**
 * Class XProfile Field Value - Models the data for the XProfile Field Value object type.
 */
class XProfileFieldValue extends Model {

	/**
	 * Stores the object for the incoming data.
	 *
	 * @var XProfileField
	 */
	protected $data;

	/**
	 * XProfile field value constructor.
	 *
	 * @param XProfileField $xprofile_field The XProfile Field object.
	 */
	public function __construct( XProfileField $xprofile_field ) {
		$this->data = $xprofile_field->value;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// Empty data/value is not private.
		if ( empty( $this->data->data ) ) {
			return false;
		}

		$hidden_user_fields = bp_xprofile_get_hidden_fields_for_user( $this->data->data->user_id ?? 0, $this->current_user->ID ?? 0 );

		if ( in_array( $this->data->data->id, $hidden_user_fields, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the XProfile field value object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'raw'          => function() {
					return $this->data->data->value ?? null;
				},
				'unserialized' => function() {
					return $this->get_unserialized_value( $this->data->data->value );
				},
				'rendered'     => function() {
					return $this->get_rendered_value( $this->data->data->value, $this->data->data->field_id );
				},
				'lastUpdated'  => function() {
					if ( ! empty( $this->data->data->last_updated ) ) {
						return null;
					}

					return Utils::prepare_date_response( $this->data->data->last_updated );
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
