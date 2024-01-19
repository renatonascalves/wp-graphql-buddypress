<?php
/**
 * Notification Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Notifications_Notification;

/**
 * Class Notification - Models the data for the Notification object type.
 *
 * @property string $id ID.
 * @property int $databaseId Notification ID.
 * @property int $userId User ID.
 * @property int $primaryItemId Primary Item ID.
 * @property int $secondaryItemId Secondary Item ID.
 * @property string $componentName Component.
 * @property string $componentAction Action.
 * @property string $date Date.
 * @property string $dateGmt Date as GMT.
 * @property boolean $isNew New notification.
 * @property object $object Object.
 */
class Notification extends Model {

	/**
	 * Stores the BP_Notifications_Notification object for the incoming data.
	 *
	 * @var BP_Notifications_Notification
	 */
	protected $data;

	/**
	 * Notification constructor.
	 *
	 * @param BP_Notifications_Notification $notification The BP_Notifications_Notification object.
	 */
	public function __construct( BP_Notifications_Notification $notification ) {
		$this->data = $notification;
		parent::__construct();
	}

	/**
	 * Initialize the Notification object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'              => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'notification', (string) $this->data->id )
						: null;
				},
				'databaseId'      => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'userId'          => function () {
					return ! empty( $this->data->user_id ) ? absint( $this->data->user_id ) : null;
				},
				'primaryItemId'   => function () {
					return ! empty( $this->data->item_id ) ? absint( $this->data->item_id ) : null;
				},
				'secondaryItemId' => function () {
					return ! empty( $this->data->secondary_item_id ) ? absint( $this->data->secondary_item_id ) : null;
				},
				'componentName'   => function () {
					return ! empty( $this->data->component_name ) ? $this->data->component_name : null;
				},
				'componentAction' => function () {
					return ! empty( $this->data->component_action ) ? $this->data->component_action : null;
				},
				'date'            => function () {
					return Utils::prepare_date_response( $this->data->date_notified, get_date_from_gmt( $this->data->date_notified ) );
				},
				'dateGmt'         => function () {
					return Utils::prepare_date_response( $this->data->date_notified );
				},
				'isNew'           => function () {
					return wp_validate_boolean( $this->data->is_new );
				},
			];
		}
	}
}
