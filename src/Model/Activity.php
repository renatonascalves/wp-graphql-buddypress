<?php
/**
 * Activity Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Activity_Activity;

/**
 * Class Activity - Models the data for the Activity object type.
 *
 * @property string $id ID.
 * @property int $databaseId Activity ID.
 * @property int $userId User ID.
 * @property int $parentDatabaseId Parent dabatase ID.
 * @property int $primaryItemId Primary Item ID.
 * @property int $secondaryItemId Secondary Item ID.
 * @property string $component Component.
 * @property string $type Type.
 * @property string $uri Permalink.
 * @property string $title Title.
 * @property string $date Date.
 * @property string $dateGmt Date as GMT.
 * @property string $status Status.
 * @property string $link Link.
 * @property BP_Activity_Activity $data Activity object.
 */
class Activity extends Model {

	/**
	 * Stores the Activity object for the incoming data.
	 *
	 * @var BP_Activity_Activity
	 */
	protected $data;

	/**
	 * Activity constructor.
	 *
	 * @param BP_Activity_Activity $activity The activity object.
	 */
	public function __construct( BP_Activity_Activity $activity ) {
		$this->data = $activity;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'activity' ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the Activity object.
	 */
	protected function init(): void {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'               => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'activity', (string) $this->data->id )
						: null;
				},
				'databaseId'       => function () {
					return ! empty( $this->data->id )
						? absint( $this->data->id )
						: null;
				},
				'parentId'         => function () {
					$id = 'activity_comment' === $this->data->type
						? $this->data->secondary_item_id
						: 0;

					return ! empty( $id )
						? Relay::toGlobalId( 'activity', (string) $id )
						: null;
				},
				'parentDatabaseId' => function () {
					return 'activity_comment' === $this->data->type
						? absint( $this->data->secondary_item_id )
						: 0;
				},
				'primaryItemId'    => function () {
					return absint( $this->data->item_id );
				},
				'secondaryItemId'  => function () {
					return absint( $this->data->secondary_item_id );
				},
				'status'           => function () {
					return $this->data->is_spam ? 'spam' : 'published';
				},
				'title'            => function () {
					return ! empty( $this->data->action ) ? $this->data->action : null;
				},
				'type'             => function () {
					return ! empty( $this->data->type ) ? $this->data->type : null;
				},
				'hidden'           => function () {
					return ! empty( $this->data->hide_sitewide ) ? $this->data->hide_sitewide : null;
				},
				'uri'              => function () {
					if ( empty( $this->data->id ) ) {
						return null;
					}

					return bp_activity_get_permalink( $this->data->id, $this->data );
				},
				'userId'           => function () {
					return ! empty( $this->data->user_id ) ? $this->data->user_id : null;
				},
				'component'        => function () {
					return ! empty( $this->data->component ) ? $this->data->component : null;
				},
				'data'             => function () {
					return $this->data;
				},
				'date'             => function () {
					return Utils::prepare_date_response( $this->data->date_recorded, get_date_from_gmt( $this->data->date_recorded ) );
				},
				'dateGmt'          => function () {
					return Utils::prepare_date_response( $this->data->date_recorded );
				},
			];
		}
	}
}
