<?php
/**
 * Test_Messages_messages_Queries Class.
 *
 * @group threads
 * @group messages
 */
class Test_Messages_messages_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->global_id = $this->toRelayId( 'thread', $this->thread->thread_id );
	}

	public function test_get_thread_messages() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$m2 = $this->create_thread_object(
			array(
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			)
		);

		// Star the second message.
		$star = bp_messages_star_set_action(
			[
				'user_id'    => $this->admin,
				'message_id' => $m2->id,
			]
		);

		// assert that star is set.
		$this->assertTrue( $star );

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $this->admin,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $message->date_sent ),
					],
					1 => [
						'id'          => $this->toRelayId( 'message', $m2->id ),
						'threadId'    => $m2->thread_id,
						'databaseId'  => $m2->id,
						'sender'      => [
							'databaseId' => $this->random_user,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $m2->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $m2->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $m2->message ) ),
						'isStarred'   => true,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $m2->date_sent ),
					],
				]
			] );
	}

	/**
	 * @todo Pending.
	 */
	public function get_thread_messages_order_desc() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$m2 = $this->create_thread_object(
			array(
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			)
		);

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id,
			[ 'where' => [ 'order' => 'DESC' ] ]
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $m2->id ),
						'threadId'    => $m2->thread_id,
						'databaseId'  => $m2->id,
						'sender'      => [
							'databaseId' => $this->random_user,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $m2->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $m2->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $m2->message ) ),
						'isStarred'   => false,
					],
					1 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $this->admin,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
					],
				]
			] );
	}

	public function test_get_thread_messages_as_sender() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $this->admin,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $message->date_sent ),
					],
				]
			] );
	}

	public function test_get_thread_messages_as_recipient() {
		$this->bp->set_current_user( $this->random_user );

		// Create thread.
		$message = $this->create_thread_object();

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $this->admin,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $message->date_sent ),
					],
				]
			] );
	}

	public function test_get_thread_messages_as_another_recipient() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u2 );

		// Create thread.
		$message = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u1, $u2 ]
			]
		);

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $u1,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $message->date_sent ),
					],
				]
			] );
	}

	public function test_get_thread_messages_as_a_moderator() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object( [ 'sender_id'  => $this->user ] );

		$this->assertQuerySuccessful( $this->get_thread_messages(
			$message->thread_id
		) )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'messages', [
				'nodes' => [
					0 => [
						'id'          => $this->toRelayId( 'message', $message->id ),
						'threadId'    => $message->thread_id,
						'databaseId'  => $message->id,
						'sender'      => [
							'databaseId' => $this->user,
						],
						'subject'     => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $message->subject ) ),
						'excerpt'     => apply_filters( 'bp_get_message_thread_excerpt', wp_strip_all_tags( bp_create_excerpt( $message->message, 75 ) ) ),
						'message'     => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $message->message ) ),
						'isStarred'   => false,
						'dateSent'    => WPGraphQL\Utils\Utils::prepare_date_response( $message->date_sent ),
					],
				]
			] );
	}

	/**
	 * Get thread messages.
	 *
	 * @param int|null $thread_id Thread ID.
	 * @param array    $variables Variables.
	 * @return array
	 */
	protected function get_thread_messages( $thread_id = null, array $variables = [] ): array {
		$variables['threadId'] = $thread_id ?? $this->thread->thread_id;
		$query                 = 'query messagesQuery(
			$threadId:Int
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:ThreadToMessageConnectionWhereArgs
		) {
			thread(threadId: $threadId) {
				databaseId
				id
				messages(
					first:$first
					last:$last
					after:$after
					before:$before
					where:$where
				) {
					nodes {
						id
						threadId
						databaseId
						sender {
							databaseId
						}
						subject
						excerpt
						message
						isStarred
						dateSent
					}
				}
			}
		}';

		$operation_name = 'messagesQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
