<?php
/**
 * Invitation Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Invitation;

/**
 * Class Invitation - Models the data for the Invitation object type.
 *
 * @property string $id ID.
 * @property int    $databaseId Invitation ID.
 * @property int    $invitee User ID.
 * @property int    $inviter ID of the invited user.
 * @property int    $itemId Item ID.
 * @property string $type Type.
 * @property string $message Message.
 * @property bool   $accepted Accepted.
 * @property bool   $inviteSent Sent status.
 * @property string $dateModified Date.
 * @property string $dateModifiedGmt Date as GMT.
 */
class Invitation extends Model {

	/**
	 * Stores the BP_Invitation object for the incoming data.
	 *
	 * @var BP_Invitation
	 */
	protected $data;

	/**
	 * Invitation constructor.
	 *
	 * @param BP_Invitation $invitation The BP_Invitation object.
	 */
	public function __construct( BP_Invitation $invitation ) {
		$this->data = $invitation;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * Users that can see a specific invitation:
	 *
	 * - site admins.
	 * - group admin of the subject group
	 * - group moderator of the suject group
	 * - invite recipient (invitee)
	 * - inviter
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		if ( false === is_user_logged_in() ) {
			return true;
		}

		if ( true === bp_current_user_can( 'bp_moderate' ) ) {
			return false;
		}

		$user_id = $this->current_user->ID;

		if ( true === ( groups_is_user_admin( $user_id, $this->data->item_id ) || groups_is_user_mod( $user_id, $this->data->item_id ) ) ) {
			return false;
		}

		if ( true === in_array( $user_id, [ $this->data->user_id, $this->data->inviter_id ], true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the Invitation object fields.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'              => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'invitation', (string) $this->data->id )
						: null;
				},
				'databaseId'      => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'invitee'         => function () {
					return ! empty( $this->data->user_id ) ? $this->data->user_id : null;
				},
				'inviter'         => function () {
					return ! empty( $this->data->inviter_id ) ? $this->data->inviter_id : null;
				},
				'type'            => function () {
					return ! empty( $this->data->type ) ? $this->data->type : null;
				},
				'itemId'          => function () {
					return ! empty( $this->data->item_id ) ? $this->data->item_id : null;
				},
				'dateModified'    => function () {
					return Utils::prepare_date_response( $this->data->date_modified, get_date_from_gmt( $this->data->date_modified ) );
				},
				'dateModifiedGmt' => function () {
					return Utils::prepare_date_response( $this->data->date_modified );
				},
				'message'         => function () {
					return ! empty( $this->data->content ) ? $this->data->content : null;
				},
				'inviteSent'      => function () {
					return wp_validate_boolean( $this->data->invite_sent );
				},
				'accepted'        => function () {
					return wp_validate_boolean( $this->data->accepted );
				},
			];
		}
	}
}
