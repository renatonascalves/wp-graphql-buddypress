<?php
/**
 * Attachment Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use WPGraphQL\Model\Model;
use stdClass;

/**
 * Class Attachment - Models data.
 *
 * @property string $thumb Thumbnail size.
 * @property string $full Full size.
 */
class Attachment extends Model {

	/**
	 * Stores the object for the incoming data.
	 *
	 * @var stdClass
	 */
	protected $data;

	/**
	 * Attachment constructor.
	 *
	 * @param stdClass $attachment The Attachment object.
	 */
	public function __construct( stdClass $attachment ) {
		$this->data = $attachment;
		parent::__construct();
	}

	/**
	 * Initializes the Attachment object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'thumb' => function () {
					return $this->data->thumb ?? null;
				},
				'full'  => function () {
					return $this->data->full ?? null;
				},
			];
		}
	}
}
