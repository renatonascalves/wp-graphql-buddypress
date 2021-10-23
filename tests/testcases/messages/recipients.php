<?php
/**
 * Test_Messages_recipients_Queries Class.
 *
 * @group threads
 * @group recipients
 */
class Test_Messages_recipients_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

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

	public function test_get_thread_recipients() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			array(
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			)
		);

		$results = $this->get_thread_recipients( $message->thread_id );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['thread']['recipients']['nodes'], 'databaseId' );

		$this->assertTrue( 1 === $results['data']['thread']['recipients']['nodes'][0]['totalMessagesUnreadCount'] );
		$this->assertTrue( 1 === $results['data']['thread']['recipients']['nodes'][1]['totalMessagesUnreadCount'] );
		$this->assertTrue( in_array( $this->random_user, $ids, true ) );
		$this->assertTrue( in_array( $this->admin, $ids, true ) );
	}

	public function test_thread_recipients_order_desc() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			array(
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			)
		);

		$results = $this->get_thread_recipients(
			$message->thread_id,
			[ 'where' => [ 'order' => 'DESC' ] ]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['thread']['recipients']['nodes'], 'databaseId' );

		$this->assertTrue( in_array( $this->admin, $ids, true ) );
		$this->assertTrue( in_array( $this->random_user, $ids, true ) );
	}

	public function test_get_first_thread_recipient() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			array(
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			)
		);

		$results = $this->get_thread_recipients(
			$message->thread_id,
			[
				'first' => 1,
				'after' => '',
			]
		);

		$nodes = $results['data']['thread']['recipients']['nodes'];
		$ids   = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertQuerySuccessful( $results )
			->hasField( 'id', $this->toRelayId( 'thread', $message->thread_id ) )
			->hasField( 'databaseId', $message->thread_id )
			->HasEdges();

		$this->assertCount( 1, $nodes );
		$this->assertTrue( false === in_array( $this->admin, $ids, true ) );
		$this->assertTrue( in_array( $this->random_user, $ids, true ) );
	}

	public function test_get_thread_recipient_after() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		// Create thread.
		$message = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
			]
		);

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		$results = $this->get_thread_recipients(
			$message->thread_id,
			[
				'first' => 1,
				'after' => $this->key_to_cursor( $u1 )
			]
		);

		$nodes = $results['data']['thread']['recipients']['nodes'];
		$ids   = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertQuerySuccessful( $results )
			->hasField( 'id', $this->toRelayId( 'thread', (string) $message->thread_id ) )
			->hasField( 'databaseId', $message->thread_id )
			->HasEdges();

		$this->assertCount( 1, $nodes );
		$this->assertTrue( in_array( $u2, $ids, true ) );
	}

	/**
	 * Get thread recipients.
	 *
	 * @param int|null $thread_id Thread ID.
	 * @param array    $variables Variables.
	 * @return array
	 */
	protected function get_thread_recipients( $thread_id = null, array $variables = [] ): array {
		$variables['threadId'] = $thread_id ?? $this->thread->thread_id;
		$query                 = 'query recipientsQuery(
			$threadId:Int
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:ThreadToUserConnectionWhereArgs
		) {
			thread(threadId: $threadId) {
				databaseId
				id
				recipients(
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
							totalMessagesUnreadCount
						}
					}
					nodes {
						databaseId
						totalMessagesUnreadCount
					}
				}
			}
		}';

		$operation_name = 'recipientsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
