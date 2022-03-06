<?php

/**
 * Test_Messages_threadsQuery_Query Class.
 *
 * @group threads
 * @group messages
 */
class Test_Messages_threadsQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_get_threads_with_unauthenticated_user() {

		// Create thread.
		$thread = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$this->assertQuerySuccessful( $this->threadsQuery() )
			->notHasNodes();
	}

	public function test_get_threads_with_where_param_and_authenticated_user() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$thread = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$this->assertQuerySuccessful( $this->threadsQuery( [ 'where' => [ 'userId' => $this->admin ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $thread->thread_id );
	}

	public function test_get_threads_with_incorrect_authenticated_user() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		// Create thread.
		$thread = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $thread->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$this->assertQuerySuccessful( $this->threadsQuery() )
			->notHasNodes();
	}

	public function test_threads_query() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		// Create thread.
		$t1 = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t1->thread_id,
				'sender_id'  => $u2,
				'content'    => 'Foo',
				'recipients' => [ $u1 ],
			]
		);

		// Create another thread.
		$t2 = $this->create_thread_object(
			[
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t2->thread_id,
				'sender_id'  => $u1,
				'content'    => 'Foo',
				'recipients' => [ $u2 ],
			]
		);

		$results = $this->threadsQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['threads']['nodes'], 'databaseId' );

		// Check threads.
		$this->assertTrue( in_array( $t1->thread_id, $ids, true ) );
		$this->assertTrue( in_array( $t2->thread_id, $ids, true ) );
	}

	public function test_get_first_thread() {
		$this->markTestSkipped();

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		// Create thread.
		$t1 = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t1->thread_id,
				'sender_id'  => $u2,
				'content'    => 'Foo',
				'recipients' => [ $u1 ],
			]
		);

		// Create another thread.
		$t2 = $this->create_thread_object(
			[
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t2->thread_id,
				'sender_id'  => $u1,
				'content'    => 'Foo',
				'recipients' => [ $u2 ],
			]
		);

		$this->assertQuerySuccessful(
			$this->threadsQuery(
				[
					'first' => 1,
					'after' => '',
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $t1->thread_id )
			->hasNextPage();
	}

	public function test_get_thread_after() {
		$this->markTestSkipped();

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		// Create thread.
		$t1 = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t1->thread_id,
				'sender_id'  => $u2,
				'content'    => 'Foo',
				'recipients' => [ $u1 ],
			]
		);

		// Create another thread.
		$t2 = $this->create_thread_object(
			[
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t2->thread_id,
				'sender_id'  => $u1,
				'content'    => 'Foo',
				'recipients' => [ $u2 ],
			]
		);

		$this->assertQuerySuccessful( $this->threadsQuery( [ 'after' => $this->key_to_cursor( $t1->thread_id ) ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $t2->thread_id )
			->hasPreviousPage();
	}

	public function test_get_thread_before() {
		$this->markTestSkipped();

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		// Create thread.
		$t1 = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t1->thread_id,
				'sender_id'  => $u2,
				'content'    => 'Foo',
				'recipients' => [ $u1 ],
			]
		);

		// Create another thread.
		$t2 = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u3 ],
				'content'    => 'Bar',
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $t2->thread_id,
				'sender_id'  => $u3,
				'content'    => 'Foo',
				'recipients' => [ $u1 ],
			]
		);

		$this->assertQuerySuccessful(
			$this->threadsQuery(
				[
					'last'   => 1,
					'before' => $this->key_to_cursor( $t2->thread_id ),
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $t1->thread_id )
			->hasNextPage();
	}

	/**
	 * Threads query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function threadsQuery( array $variables = [] ): array {
		$query = 'query threadsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToThreadConnectionWhereArgs
		) {
			threads(
				first:$first
				last:$last
				after:$after
				before:$before
				where:$where
			) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						databaseId
						id
						unreadCount
					}
				}
				nodes {
					databaseId
					id
					unreadCount
				}
			}
		}';

		$operation_name = 'threadsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
