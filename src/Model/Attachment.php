<?php
/**
 * Attachment Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use WPGraphQL\Model\Model;

/**
 * Class Attachment - Models data.
 *
 * @property string $thumb
 * @property string $full
 */
class Attachment extends Model {

	/**
	 * Stores the object.
	 *
	 * @var stClass $data
	 */
	protected $data;

	/**
	 * Attachment constructor.
	 *
	 * @param stClass $attachment The attachment object.
	 */
	public function __construct( $attachment ) {
		$this->data = $attachment;
		parent::__construct();
	}

	/**
	 * Initializes the Attachment object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'thumb'  => function() {
					return $this->data->thumb ?? null;
				},
				'full'   => function() {
					return $this->data->full ?? null;
				},
			];
		}
	}
}
