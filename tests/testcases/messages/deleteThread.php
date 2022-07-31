<?php

/**
 * Test_Messages_deleteThread_Mutation Class.
 *
 * @group threads
 */
class Test_Messages_deleteThread_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_delete_thread_from_sender() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		// Create thread.
		$message = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->delete_thread( $message->thread_id ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $message->thread_id );

		$this->assertTrue( null === messages_check_thread_access( $message->thread_id, $u1 ) );
		$this->assertTrue( (bool) messages_check_thread_access( $message->thread_id, $u2 ) );
	}

	public function test_delete_thread_from_recipient() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		// Reply.
		$message = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->delete_thread( $message->thread_id ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $message->thread_id );

		$this->assertTrue( null === messages_check_thread_access( $message->thread_id, $u2 ) );
		$this->assertTrue( (bool) messages_check_thread_access( $message->thread_id, $u1 ) );
	}

	public function test_delete_thread_with_unauthenticated_user() {
		$this->assertQueryFailed( $this->delete_thread( $this->thread->thread_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_thread_user_without_permission() {
		$u1 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->delete_thread( $this->thread->thread_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_thread_invalid_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_thread( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This thread does not exist.' );
	}

	/**
	 * Delete thread mutation.
	 *
	 * @param int|null $thread_id Thread ID.
	 * @return array
	 */
	protected function delete_thread( $thread_id = null ): array {
		$query = '
			mutation deleteThreadTest(
				$clientMutationId:String!
				$databaseId:Int
			) {
				deleteThread(
					input: {
						clientMutationId:$clientMutationId
						databaseId:$databaseId
					}
				)
				{
					clientMutationId
					deleted
					thread {
						databaseId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $thread_id,
		];

		$operation_name = 'deleteThreadTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
