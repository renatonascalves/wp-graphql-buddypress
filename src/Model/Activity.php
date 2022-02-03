<?php
/**
 * Activity Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use stdClass;

/**
 * Class Activity - Models the data for the Activity object type.
 *
 * @property string $id ID.
 * @property int $databaseId Activity ID.
 * @property int $userId User ID.
 * @property string $component Component.
 * @property string $type Type.
 * @property string $title Title.
 * @property string $date Date.
 * @property string $status Status.
 * @property string $link Link.
 * @property stdClass $data Activity object.
 */
class Activity extends Model {

	/**
	 * Stores the Activity object for the incoming data.
	 *
	 * @var stdClass
	 */
	protected $data;

	/**
	 * Activity constructor.
	 *
	 * @param stdClass $activity The stdClass object.
	 */
	public function __construct( stdClass $activity ) {
		$this->data = $activity;
		parent::__construct();
	}

	/**
	 * Initialize the Activity object.
	 */
	protected function init() : void {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'               => function() {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'activity', (string) $this->data->id )
						: null;
				},
				'databaseId'       => function() {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'parentId'         => function() {
					$id = 'activity_comment' === $this->data->type ? $this->data->item_id : 0;

					return ! empty( $id )
						? Relay::toGlobalId( 'activity', (string) $id )
						: null;
				},
				'parentDatabaseId' => function() {
					return 'activity_comment' === $this->data->type ? absint( $this->data->item_id ) : 0;
				},
				'primaryItemId'    => function() {
					return absint( $this->data->item_id ?? 0 );
				},
				'secondaryItemId'  => function() {
					return absint( $this->data->secondary_item_id ?? 0 );
				},
				'status'           => function() {
					return $this->data->is_spam ? 'spam' : 'published';
				},
				'title'            => function() {
					return $this->data->action ?? null;
				},
				'type'             => function() {
					return $this->data->type ?? null;
				},
				'hidden'           => function() {
					return $this->data->hide_sitewide ?? null;
				},
				'uri'              => function() {
					return bp_activity_get_permalink( $this->data->id ?? null );
				},
				'userId'           => function() {
					return $this->data->user_id ?? null;
				},
				'component'        => function() {
					return $this->data->component ?? null;
				},
				'data'             => function() {
					return $this->data ?? null;
				},
				'date'             => function() {
					return Utils::prepare_date_response( $this->data->date_recorded, get_date_from_gmt( $this->data->date_recorded ) );
				},
				'dateGmt'          => function() {
					return Utils::prepare_date_response( $this->data->date_recorded );
				},
			];
		}
	}
}
