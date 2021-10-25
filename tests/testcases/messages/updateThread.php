<?php

/**
 * Test_Messages_updateThread_Mutation Class.
 *
 * @group thread
 */
class Test_Messages_updateThread_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_mark_thread_as_read() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->assertEquals( 1, messages_get_unread_count( $u2 ) );

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->update_thread( $thread->thread_id, [ 'read' => true ] ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 0 );

		$this->assertEquals( 0, messages_get_unread_count( $u2 ) );
	}

	public function test_mark_thread_as_unread() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->assertEquals( 1, messages_get_unread_count( $u2 ) );

		messages_mark_thread_read( $thread->thread_id, $u2 );

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->update_thread( $thread->thread_id, [ 'unRead' => true ] ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 1 );

		$this->assertEquals( 1, messages_get_unread_count( $u2 ) );
	}

	public function test_update_message_with_invalid_id() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u2 );

		$this->assertQueryFailed( $this->update_thread( $thread->thread_id, [ 'messageId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This message does not exist.' );
	}

	public function test_update_message_with_different_thread_message_id() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u2 );

		$this->assertQueryFailed( $this->update_thread( $thread->thread_id, [ 'messageId' => $this->thread->id ] ) )
			->expectedErrorMessage( 'There was an error trying to update the message.' );
	}

	/**
	 * @todo Add logic to test this using the `bp_graphql_messages_can_edit_item_meta` hook.
	 */
	public function test_update_message_with_sender_id() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->update_thread( $thread->thread_id ) )
			->hasField( 'databaseId', $thread->thread_id )
			->hasField( 'unreadCount', 0 );
	}

	public function test_update_message_with_recipient() {
		$u1 =  $this->bp_factory->user->create();
		$u2 =  $this->bp_factory->user->create();

		$thread = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u2 );

		$this->assertQueryFailed( $this->update_thread( $thread->thread_id ) )
			->expectedErrorMessage( 'There was an error trying to update the message.' );
	}

	public function update_thread_with_unauthenticated_user() {
		$this->assertQueryFailed( $this->update_thread( $this->thread->thread_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function update_thread_user_without_permission() {
		$u1 =  $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->update_thread( $this->thread->thread_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function update_thread_invalid_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_thread( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This thread does not exist.' );
	}

	/**
	 * Update thread mutation.
	 *
	 * @param array $args Variables.
	 * @return array
	 */
	protected function update_thread( $thread_id = null, array $args = [] ): array {
		$query = '
			mutation updateThreadTest(
				$clientMutationId:String!
				$threadId:Int
				$messageId:Int
				$read:Boolean
				$unRead:Boolean
			) {
				updateThread(
					input: {
						clientMutationId:$clientMutationId
						threadId:$threadId
						messageId:$messageId
						read:$read
						unRead:$unRead
					}
				)
				{
					clientMutationId
					thread {
						databaseId
						unreadCount
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'threadId'         => $thread_id,
				'messageId'        => null,
				'read'             => null,
				'unRead'           => null,
			]
		);

		$operation_name = 'updateThreadTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
