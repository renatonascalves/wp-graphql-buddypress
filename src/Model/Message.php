<?php
/**
 * Message Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Messages_Message;

/**
 * Class Message - Models the data for the Message object type.
 *
 * @property string $id ID.
 * @property int    $databaseId Message/Database ID.
 * @property int    $threadId Thread ID.
 * @property int    $sender ID of the message sender.
 * @property string $subject Message subject.
 * @property string $message Message content.
 * @property string $excerpt Messate excerpt.
 * @property string $dateSent Date the message was sent.
 * @property string $dateSentGmt Date the message was sent, as GMT.
 */
class Message extends Model {

	/**
	 * Stores the Message object for the incoming data.
	 *
	 * @var BP_Messages_Message
	 */
	protected $data;

	/**
	 * Message constructor.
	 *
	 * @param BP_Messages_Message $message The message object.
	 */
	public function __construct( BP_Messages_Message $message ) {
		$this->data = $message;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// Moderators can see everything.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			return false;
		}

		// Check thread access.
		if ( messages_check_thread_access( $this->data->thread_id, $this->current_user->ID ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the Message object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'          => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'message', (string) $this->data->id )
						: null;
				},
				'databaseId'  => function () {
					return ! empty( $this->data->id ) ? $this->data->id : null;
				},
				'threadId'    => function () {
					return ! empty( $this->data->thread_id ) ? $this->data->thread_id : null;
				},
				'sender'      => function () {
					return ! empty( $this->data->sender_id ) ? $this->data->sender_id : null;
				},
				'subject'     => function () {
					return ! empty( $this->data->subject ) ? $this->data->subject : null;
				},
				'excerpt'     => function () {
					return ! empty( $this->data->message ) ? $this->data->message : null;
				},
				'message'     => function () {
					return ! empty( $this->data->message ) ? $this->data->message : null;
				},
				'dateSent'    => function () {
					return Utils::prepare_date_response( $this->data->date_sent, get_date_from_gmt( $this->data->date_sent ) );
				},
				'dateSentGmt' => function () {
					return Utils::prepare_date_response( $this->data->date_sent );
				},
			];
		}
	}
}
