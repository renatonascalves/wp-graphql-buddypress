<?php
/**
 * Test_Messages_thread_Queries Class.
 *
 * @group threads
 * @group messages
 */
class Test_Messages_thread_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		$this->global_id = $this->toRelayId( 'thread', $this->thread->thread_id );
	}

	public function test_get_a_thread_as_sender() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_thread( $this->thread->thread_id, 'DATABASE_ID' ) )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->thread->thread_id )
			->hasField( 'unreadCount', null );
	}

	public function test_get_a_thread_as_recipient() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_thread( $this->thread->thread_id, 'DATABASE_ID' ) )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->thread->thread_id )
			->hasField( 'unreadCount', null );
	}

	public function test_get_thread_as_moderator() {
		$u1 = $this->bp_factory->user->create();

		// Create thread.
		$thread = $this->create_thread_object( [ 'sender_id' => $u1 ] );

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		// Moderator can see it all.
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id, 'DATABASE_ID' ) )
			->hasField( 'id', $this->toRelayId( 'thread', $thread->thread_id ) )
			->hasField( 'databaseId', $thread->thread_id );
	}

	public function test_get_thread_unread_count() {
		$u1 = $this->bp_factory->user->create();

		// Create thread.
		$thread = $this->create_thread_object( [ 'sender_id' => $u1 ] );

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $u1,
				'recipients' => [ $this->random_user ],
				'content'    => 'Bar',
			]
		);

		// Check as the sender.
		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id, 'DATABASE_ID' ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 0 );

		// Check as the recipient.
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id, 'DATABASE_ID' ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 2 );
	}

	public function test_get_thread_user_with_no_access() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		// Create thread.
		$thread = $this->create_thread_object( [ 'sender_id' => $u1 ] );

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		$this->bp->set_current_user( $u2 );

		$response = $this->get_a_thread( $thread->thread_id, 'DATABASE_ID' );

		$this->assertEmpty( $response['data']['thread'] );
	}

	public function test_get_a_thread_with_unauthenticated_user() {
		$response = $this->get_a_thread( $this->thread->thread_id, 'DATABASE_ID' );

		$this->assertEmpty( $response['data']['thread'] );
	}

	public function test_get_thread_with_invalid_id() {
		$this->assertQueryFailed( $this->get_a_thread( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER, 'ID' ) )
			->expectedErrorMessage( 'The "id" is invalid.' );
	}

	public function test_get_thread_with_invalid_thread_id() {
		$this->assertQueryFailed( $this->get_a_thread( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER, 'DATABASE_ID' ) )
			->expectedErrorMessage( 'This thread does not exist.' );
	}

	/**
	 * Get a thread.
	 *
	 * @param int|null    $thread_id Thread ID.
	 * @param string|null $type      Type.
	 * @return array
	 */
	protected function get_a_thread( $thread_id = null, $type = null ): array {
		$thread = $thread_id ?? $this->thread->thread_id;
		$query  = "
			query {
				thread(id: {$thread}, idType: {$type}) {
					databaseId
					id
					unreadCount
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
