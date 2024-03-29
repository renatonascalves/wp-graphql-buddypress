<?php
/**
 * Test_Messages_recipients_Queries Class.
 *
 * @group threads
 * @group recipients
 */
class Test_Messages_recipients_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_thread_recipients() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$results = $this->get_thread_recipients(
			[
				'id'     => $message->thread_id,
				'idType' => 'DATABASE_ID',
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['thread']['recipients']['nodes'], 'databaseId' );

		$this->assertTrue( 1 === $results['data']['thread']['recipients']['nodes'][0]['totalMessagesUnreadCount'] );
		$this->assertTrue( 1 === $results['data']['thread']['recipients']['nodes'][1]['totalMessagesUnreadCount'] );
		$this->assertContains( $this->random_user, $ids );
		$this->assertContains( $this->admin, $ids );
	}

	public function test_thread_recipients_order_desc() {
		$this->bp->set_current_user( $this->admin );

		// Create thread.
		$message = $this->create_thread_object();

		// Reply.
		$this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$results = $this->get_thread_recipients(
			[
				'id'     => $message->thread_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'order' => 'DESC' ],
			]
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
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->admin ],
				'content'    => 'Bar',
			]
		);

		$results = $this->get_thread_recipients(
			[
				'id'     => $message->thread_id,
				'idType' => 'DATABASE_ID',
				'first'  => 1,
				'after'  => '',
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

	/**
	 * Get thread recipients.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function get_thread_recipients( array $variables = [] ): array {
		$query = 'query recipientsQuery(
			$id:ID!
			$idType:ThreadIdTypeEnum
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:ThreadToUserConnectionWhereArgs
		) {
			thread(id: $id, idType: $idType ) {
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

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
