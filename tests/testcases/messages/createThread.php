<?php

use WPGraphQL\Extensions\BuddyPress\Data\ThreadHelper;

/**
 * Test_Messages_createThread_Mutation Class.
 *
 * @group thread
 * @group messages
 */
class Test_Messages_createThread_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_create_thread() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$response = $this->create_thread( [ 'recipients' => [ $u2 ] ] );

		$this->assertQuerySuccessful( $response );

		$thread = ThreadHelper::get_thread_from_input(
			$response['data']['createThread']['thread']['databaseId']
		);

		$this->assertEquals(
			[
				'data' => [
					'createThread' => [
						'clientMutationId' => $this->client_mutation_id,
						'thread'           => [
							'id'         => $this->toRelayId( 'thread', (string) $thread->thread_id ),
							'databaseId' => $thread->thread_id,
							'recipients' => [
								'nodes' => [
									0 => [
										'id'         => $this->toRelayId( 'user', (string) $u2 ),
										'databaseId' => $u2,
									],
									1 => [
										'id'         => $this->toRelayId( 'user', (string) $u1 ),
										'databaseId' => $u1,
									],
								],
							],
							'messages'   => [
								'nodes' => [
									0 => [
										'id'         => $this->toRelayId( 'message', (string) $thread->last_message_id ),
										'threadId'   => $thread->thread_id,
										'databaseId' => $thread->last_message_id,
										'sender'     => [
											'id'         => $this->toRelayId( 'user', (string) $u1 ),
											'databaseId' => $u1,
										],
										'subject'    => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $thread->last_message_subject ) ),
										'message'    => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $thread->last_message_content ) ),
										'isStarred'  => false,
									],
								],
							],
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_message_to_already_existent_thread() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$r1 = $this->create_thread( [ 'recipients' => [ $u2 ] ] );

		$this->assertQuerySuccessful( $r1 );

		$this->bp->set_current_user( $u2 );

		$r2 = $this->create_thread(
			[
				'threadId'   => $r1['data']['createThread']['thread']['databaseId'],
				'message'    => 'Another message',
				'recipients' => [ $u1 ],
			]
		);

		$this->assertQuerySuccessful( $r2 );

		$thread = ThreadHelper::get_thread_from_input(
			$r2['data']['createThread']['thread']['databaseId']
		);

		$this->assertEquals(
			[
				'data' => [
					'createThread' => [
						'clientMutationId' => $this->client_mutation_id,
						'thread'           => [
							'id'         => $this->toRelayId( 'thread', (string) $thread->thread_id ),
							'databaseId' => $thread->thread_id,
							'recipients' => [
								'nodes' => [
									0 => [
										'id'         => $this->toRelayId( 'user', (string) $u2 ),
										'databaseId' => $u2,
									],
									1 => [
										'id'         => $this->toRelayId( 'user', (string) $u1 ),
										'databaseId' => $u1,
									],
								],
							],
							'messages'   => [
								'nodes' => [
									0 => [
										'id'         => $this->toRelayId( 'message', (string) $thread->messages[0]->id ),
										'threadId'   => $thread->thread_id,
										'databaseId' => $thread->messages[0]->id,
										'sender'     => [
											'id'         => $this->toRelayId( 'user', (string) $u1 ),
											'databaseId' => $u1,
										],
										'subject'    => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $thread->messages[0]->subject ) ),
										'message'    => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $thread->messages[0]->message ) ),
										'isStarred'  => false,
									],
									1 => [
										'id'         => $this->toRelayId( 'message', (string) $thread->messages[1]->id ),
										'threadId'   => $thread->thread_id,
										'databaseId' => $thread->messages[1]->id,
										'sender'     => [
											'id'         => $this->toRelayId( 'user', (string) $u2 ),
											'databaseId' => $u2,
										],
										'subject'    => apply_filters( 'bp_get_message_thread_subject', wp_staticize_emoji( $thread->messages[1]->subject ) ),
										'message'    => apply_filters( 'bp_get_the_thread_message_content', wp_staticize_emoji( $thread->messages[1]->message ) ),
										'isStarred'  => false,
									],
								],
							],
						],
					],
				],
			],
			$r2
		);
	}

	public function test_create_message_to_already_existent_thread_with_invalid_user() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$r1 = $this->create_thread( [ 'recipients' => [ $u2 ] ] );

		$this->assertQuerySuccessful( $r1 );

		$this->bp->set_current_user( $u3 );

		$response = $this->create_thread(
			[
				'threadId'   => $r1['data']['createThread']['thread']['databaseId'],
				'message'    => 'Another message',
				'recipients' => [ $u1 ],
			]
		);

		$this->assertQueryFailed( $response )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_message_with_invalid_thread_id() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$response = $this->create_thread(
			[
				'threadId'   => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER,
				'message'    => 'Another message',
				'recipients' => [ $u2 ],
			]
		);

		$this->assertQueryFailed( $response )
			->expectedErrorMessage( 'This thread does not exist.' );
	}

	public function test_create_thread_user_not_logged_in() {
		$u1 = $this->bp_factory->user->create();

		$this->assertQueryFailed( $this->create_thread( [ 'recipients' => [ $u1 ] ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_thread_with_null_message_field() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed(
			$this->create_thread(
				[
					'recipients' => [ $u2 ],
					'message'    => null,
				]
			)
		)
			->expectedErrorMessage( 'Variable "$message" of non-null type "String!" must not be null.' );
	}

	public function test_create_thread_with_empty_message_field() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed(
			$this->create_thread(
				[
					'recipients' => [ $u2 ],
					'message'    => '',
				]
			)
		)
			->expectedErrorMessage( 'Please, enter the content of the thread message.' );
	}

	public function test_create_thread_with_empty_recipients_field() {
		$u1 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->create_thread( [ 'recipients' => [] ] ) )
			->expectedErrorMessage( 'Recipients is a required field.' );
	}

	public function test_create_thread_with_invalid_recipient() {
		$u1 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->create_thread( [ 'recipients' => [ GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ] ) )
			->expectedErrorMessage( 'There was an error trying to create a thread message.' );
	}

	/**
	 * Create thread mutation helper method.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_thread( array $args = [] ): array {
		$query = '
			mutation createThreadTest(
				$clientMutationId:String!
				$message:String!
				$subject:String
				$threadId:Int
				$recipients:[Int]
			) {
				createThread(
					input: {
						clientMutationId: $clientMutationId
						message: $message
						threadId: $threadId
						subject: $subject
						recipients: $recipients
					}
				)
				{
					clientMutationId
					thread {
						id
						databaseId
						recipients {
							nodes {
								id,
								databaseId
							}
						}
						messages {
							nodes {
								id
								threadId
								databaseId
								sender {
									id
									databaseId
								}
								subject
								message
								isStarred
							}
						}
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'message'          => 'Message',
				'subject'          => 'Message Subject',
				'recipients'       => [],
				'threadId'         => null,
			]
		);

		$operation_name = 'createThreadTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
