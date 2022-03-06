<?php

/**
 * Test_Messages_starMessage_Mutation Class.
 *
 * @group messages
 * @group star
 */
class Test_Messages_starMessage_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_star_message_as_sender() {
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

		$this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->star_message( $message->id ) )
			->hasField( 'databaseId', $message->id )
			->hasField( 'threadId', $message->thread_id )
			->hasField( 'isStarred', true );

		$this->assertTrue( bp_messages_is_message_starred( $message->id, $u1 ) );
		$this->assertFalse( bp_messages_is_message_starred( $message->id, $u2 ) );

		$this->assertQuerySuccessful( $this->star_message( $message->id ) )
			->hasField( 'databaseId', $message->id )
			->hasField( 'threadId', $message->thread_id )
			->hasField( 'isStarred', false );

		$this->assertFalse( bp_messages_is_message_starred( $message->id, $u1 ) );
		$this->assertFalse( bp_messages_is_message_starred( $message->id, $u2 ) );
	}

	public function test_star_message_as_recipient() {
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

		$m2 = $this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->star_message( $m2->id ) )
			->hasField( 'databaseId', $m2->id )
			->hasField( 'threadId', $m2->thread_id )
			->hasField( 'isStarred', true );

		$this->assertTrue( bp_messages_is_message_starred( $m2->id, $u2 ) );
		$this->assertFalse( bp_messages_is_message_starred( $m2->id, $u1 ) );

		$this->assertQuerySuccessful( $this->star_message( $m2->id ) )
			->hasField( 'databaseId', $m2->id )
			->hasField( 'threadId', $m2->thread_id )
			->hasField( 'isStarred', false );

		$this->assertFalse( bp_messages_is_message_starred( $m2->id, $u2 ) );
		$this->assertFalse( bp_messages_is_message_starred( $m2->id, $u1 ) );
	}

	public function test_star_message_with_unauthenticated_user() {
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

		$this->assertQueryFailed( $this->star_message( $message->id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_star_message_with_invalid_user() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		// Create thread.
		$message = $this->create_thread_object(
			[
				'sender_id'  => $u1,
				'recipients' => [ $u2 ],
				'content'    => 'Foo',
			]
		);

		$this->create_thread_object(
			[
				'thread_id'  => $message->thread_id,
				'sender_id'  => $u2,
				'recipients' => [ $u1 ],
				'content'    => 'Bar',
			]
		);

		$this->bp->set_current_user( $u3 );

		$this->assertQueryFailed( $this->star_message( $message->id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_star_message_with_invalid_id() {
		$this->assertQueryFailed( $this->star_message( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This message does not exist.' );
	}

	/**
	 * (Un)star message.
	 *
	 * @param int $message_id Message ID.
	 * @return array
	 */
	protected function star_message( int $message_id ): array {
		$query = '
			mutation starMessageTest(
				$clientMutationId:String!
				$messageId:Int
			) {
				starMessage(
					input: {
						clientMutationId:$clientMutationId
						messageId:$messageId
					}
				)
				{
					clientMutationId
					message {
						id
						threadId
						databaseId
						isStarred
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'messageId'        => $message_id,
		];

		$operation_name = 'starMessageTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
