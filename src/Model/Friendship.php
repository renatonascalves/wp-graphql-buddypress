<?php
/**
 * Friendship Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WPGraphQL\Utils\Utils;
use BP_Friends_Friendship;

/**
 * Class Friendship - Models the data for the Friendship object type.
 *
 * @property string $id ID.
 * @property int $databaseId Friendship ID.
 * @property int $initiator ID of the user.
 * @property int $friend ID of the user.
 * @property bool $isConfirmed Friendship confirmation status.
 * @property string $dateCreated Date of the friendship.
 * @property string $dateCreatedGmt Date of the friendship, as GMT.
 */
class Friendship extends Model {

	/**
	 * Stores the Friendship object for the incoming data.
	 *
	 * @var BP_Friends_Friendship
	 */
	protected $data;

	/**
	 * Friendship constructor.
	 *
	 * @param BP_Friends_Friendship $friendship The BP_Friends_Friendship object.
	 */
	public function __construct( BP_Friends_Friendship $friendship ) {
		$this->data = $friendship;
		parent::__construct();
	}

	/**
	 * Initialize the Friendship object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'             => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'friendship', (string) $this->data->id )
						: null;
				},
				'databaseId'     => function () {
					return ! empty( $this->data->id ) ? $this->data->id : null;
				},
				'initiator'      => function () {
					return ! empty( $this->data->initiator_user_id ) ? $this->data->initiator_user_id : null;
				},
				'friend'         => function () {
					return ! empty( $this->data->friend_user_id ) ? $this->data->friend_user_id : null;
				},
				'isConfirmed'    => function () {
					return wp_validate_boolean( $this->data->is_confirmed );
				},
				'dateCreated'    => function () {
					return Utils::prepare_date_response( $this->data->date_created, get_date_from_gmt( $this->data->date_created ) );
				},
				'dateCreatedGmt' => function () {
					return Utils::prepare_date_response( $this->data->date_created );
				},
			];
		}
	}
}
