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
 */
class Attachment extends Model {

	/**
	 * Stores the object.
	 *
	 * @var stdClass
	 */
	protected $data;

	/**
	 * Attachment constructor.
	 *
	 * @param stdClass $attachment The Attachment object.
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
				'thumb' => function() {
					return $this->data->thumb ?? null;
				},
				'full' => function() {
					return $this->data->full ?? null;
				},
			];
		}
	}
}
