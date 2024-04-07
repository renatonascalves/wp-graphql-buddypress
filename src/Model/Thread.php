<?php
/**
 * Thread Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use BP_Messages_Thread;

/**
 * Class Thread - Models the data for the Thread object type.
 *
 * @property string $id ID.
 * @property int    $databaseId Database ID.
 * @property int    $unreadCount Count of the unread messages.
 * @property array  $messages Thread messages.
 * @property int    $lastMessage Last message ID of the thread.
 * @property array  $recipients Thread recipients.
 * @property array  $senderIds Thread sender IDs.
 */
class Thread extends Model {

	/**
	 * Stores the Thread object for the incoming data.
	 *
	 * @var BP_Messages_Thread
	 */
	protected $data;

	/**
	 * Thread constructor.
	 *
	 * @param BP_Messages_Thread $thread The Thread object.
	 */
	public function __construct( BP_Messages_Thread $thread ) {
		$this->data = $thread;
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
	 * Initialize the Thread object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'          => function () {
					return ! empty( $this->data->thread_id )
						? Relay::toGlobalId( 'thread', (string) $this->data->thread_id )
						: null;
				},
				'databaseId'  => function () {
					return ! empty( $this->data->thread_id ) ? $this->data->thread_id : null;
				},
				'lastMessage' => function () {
					return ! empty( $this->data->last_message_id ) ? $this->data->last_message_id : null;
				},
				'senderIds'   => function () {
					return ! empty( $this->data->sender_ids ) ? $this->data->sender_ids : null;
				},
				'unreadCount' => function () {
					return ! empty( $this->data->unread_count ) ? $this->data->unread_count : null;
				},
				'recipients'  => function () {
					return ! empty( $this->data->recipients ) ? $this->data->recipients : null;
				},
				'messages'    => function () {
					return ! empty( $this->data->messages ) ? $this->data->messages : null;
				},
			];
		}
	}
}
