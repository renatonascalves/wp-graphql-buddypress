<?php

/**
 * Test_Friendship_Create_Mutation Class.
 *
 * @group friends
 */
class Test_Friendship_Create_Mutation extends WP_UnitTestCase {

	public static $bp_factory;
	public static $user;
	public static $bp;
	public static $client_mutation_id;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp                 = new BP_UnitTestCase();
		self::$bp_factory         = new BP_UnitTest_Factory();
		self::$client_mutation_id = 'someUniqueId';
		self::$user               = self::factory()->user->create();
	}

	public function test_create_friendship() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u1 );

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => self::$client_mutation_id,
						'friendship' => [
							'isConfirmed' => false,
							'initiator' => [
								'userId' => $u1,
							],
							'friend' => [
								'userId' => $u2,
							],
						],
					],
				],
			],
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);
	}

	public function test_create_friendship_but_already_has_friendship_request() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u1 );

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => self::$client_mutation_id,
						'friendship' => [
							'isConfirmed' => false,
							'initiator' => [
								'userId' => $u1,
							],
							'friend' => [
								'userId' => $u2,
							],
						],
					],
				],
			],
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);

		// Already friends.
		$response = do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'You already have a pending friendship request with this user.', $response['errors'][0]['message'] );
	}

	public function test_user_can_not_create_friendship_to_other_people() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		$u3 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u3 );

		$mutation = $this->create_friendship( $u1, $u2 );
		$response = do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you do not have permission to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_create_friendship_user_not_logged_id() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		$mutation = $this->create_friendship( $u1, $u2 );
		$response = do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you do not have permission to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_create_friendship_invalid_user() {
		$mutation = $this->create_friendship(
			self::$bp_factory->user->create(),
			111
		);

		$response = do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'There was a problem confirming if user is valid.', $response['errors'][0]['message'] );
	}

	public function test_create_friendship_without_a_friend_user() {
		$u1 = self::$bp_factory->user->create();

		$mutation = '
		mutation createFriendshipTest( $clientMutationId: String!, $initiatorId: Int ) {
			createFriendship(
				input: {
					clientMutationId: $clientMutationId
					initiatorId: $initiatorId
				}
			)
          	{
				clientMutationId
		    	friendship {
					isConfirmed
					initiator {
						userId
					}
					friend {
						userId
					}
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => self::$client_mutation_id,
				'initiatorId'      => $u1,
			]
		);

		$response = do_graphql_request( $mutation, 'createFriendshipTest', $variables );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Field CreateFriendshipInput.friendId of required type Int! was not provided.', $response['errors'][0]['message'] );
	}

	protected function create_friendship( $initiator, $friend ) {
		$mutation = '
		mutation createFriendshipTest( $clientMutationId: String!, $initiatorId: Int, $friendId: Int! ) {
			createFriendship(
				input: {
					clientMutationId: $clientMutationId
					initiatorId: $initiatorId
					friendId: $friendId
				}
			)
          	{
				clientMutationId
		    	friendship {
					isConfirmed
					initiator {
						userId
					}
					friend {
						userId
					}
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => self::$client_mutation_id,
				'initiatorId'      => $initiator,
				'friendId'         => $friend,
			]
		);

		return [ $mutation, $variables ];
	}

	protected function create_friendship_object( $u = 0, $a = 0 ) {
		if ( empty( $u ) ) {
			$u = self::factory()->user->create();
		}

		if ( empty( $a ) ) {
			$a = self::factory()->user->create();
		}

		$friendship                    = new BP_Friends_Friendship();
		$friendship->initiator_user_id = $u;
		$friendship->friend_user_id    = $a;
		$friendship->is_confirmed      = 0;
		$friendship->date_created      = bp_core_current_time();
		$friendship->save();

		return $friendship->id;
	}
}
