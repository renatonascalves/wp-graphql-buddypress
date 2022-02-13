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
	public function setUp() {
		parent::setUp();

		$this->global_id = $this->toRelayId( 'thread', $this->thread->thread_id );
	}

	public function test_get_a_thread_as_sender() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_thread() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->thread->thread_id )
			->hasField( 'unreadCount', null );
	}

	public function test_get_a_thread_as_recipient() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_thread() )
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

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id ) )
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

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 0 );

		// Check as the recipient.
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_thread( $thread->thread_id ) )
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

		$response = $this->get_a_thread( $thread->thread_id );

		$this->assertEmpty( $response['data']['thread'] );
	}

	public function test_get_a_thread_with_unauthenticated_user() {
		$response = $this->get_a_thread();

		$this->assertEmpty( $response['data']['thread'] );
	}

	public function test_thread_by_query_with_threadid_param() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_thread() )
			->hasField( 'id', $this->global_id );
	}

	public function test_thread_by_query_with_id_param() {
		$this->bp->set_current_user( $this->admin );

		$query = "
			query {
				thread(id: \"{$this->global_id}\") {
					id,
					databaseId
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'databaseId', $this->thread->thread_id )
			->hasField( 'id', $this->global_id );
	}

	public function test_get_thread_with_invalid_id() {
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			thread(id: \"{$id}\") {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'The "id" is invalid.' );
	}

	public function test_get_thread_with_invalid_thread_id() {
		$this->assertQueryFailed( $this->get_a_thread( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This thread does not exist.' );
	}

	public function test_get_thread_with_unknown_id_argument() {
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			thread(threadID: \"{$id}\") {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'Unknown argument "threadID" on field "thread" of type "RootQuery". Did you mean "threadId"?' );
	}

	/**
	 * Get a thread.
	 *
	 * @param int|null $thread_id Thread ID.
	 * @return array
	 */
	protected function get_a_thread( $thread_id = null ): array {
		$thread = $thread_id ?? $this->thread->thread_id;
		$query  = "
			query {
				thread(threadId: {$thread}) {
					databaseId
					id
					unreadCount
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
