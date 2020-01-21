<?php

/**
 * Test_Friendship_Mutations Class.
 *
 * @group friendship-mutation
 */
class Test_Friendship_Mutations extends \WP_UnitTestCase {

	public $user;
	public $bp_factory;
	public $bp;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_create_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
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
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
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
		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);
	}

	public function test_user_can_not_create_friendship_to_other_people() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u3 );

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);
	}

	public function test_create_friendship_user_not_logged_id() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$mutation = $this->create_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);
	}

	public function test_create_friendship_invalid_user() {
		$mutation = $this->create_friendship(
			$this->bp_factory->user->create(),
			111
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'createFriendshipTest', $mutation[1] )
		);
	}

	public function test_create_friendship_without_a_friend_user() {
		$u1 = $this->bp_factory->user->create();

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
				'clientMutationId' => $this->client_mutation_id,
				'initiatorId'      => $u1,
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_accept_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		$mutation = $this->update_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'updateFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'friendship' => [
							'isConfirmed' => 1,
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
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);

	}

	public function test_initiator_can_not_accept_his_own_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$mutation = $this->update_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);
	}

	public function test_user_can_not_accept_other_user_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u3 );

		$mutation = $this->update_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);
	}

	public function test_accept_friendship_with_user_not_logged_in() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$mutation = $this->update_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);
	}

	public function test_accept_friendship_with_invalid_users() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		// Invalid friend.
		$mutation = $this->update_friendship( $u1, 111 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);

		$this->bp->set_current_user( $u2 );

		// Invalid initiator.
		$mutation = $this->update_friendship( 111, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'updateFriendshipTest', $mutation[1] )
		);
	}

	public function test_initiator_withdraw_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$mutation = $this->delete_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'deleteFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted' => true,
						'friendship' => [
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
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);

	}

	public function test_friend_reject_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		$mutation = $this->delete_friendship( $u1, $u2 );

		$this->assertEquals(
			[
				'data' => [
					'deleteFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted' => true,
						'friendship' => [
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
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);

	}

	public function test_delete_friendship_with_user_not_logged_in() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$mutation = $this->delete_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);
	}

	public function test_user_can_not_delete_or_reject_other_user_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u3 );

		$mutation = $this->delete_friendship( $u1, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);
	}

	public function test_delete_with_invalid_users() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		// Invalid friend.
		$mutation = $this->delete_friendship( $u1, 111 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);

		$this->bp->set_current_user( $u2 );

		// Invalid initiator.
		$mutation = $this->update_friendship( 111, $u2 );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation[0], 'deleteFriendshipTest', $mutation[1] )
		);
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
				'clientMutationId' => $this->client_mutation_id,
				'initiatorId'      => $initiator,
				'friendId'         => $friend,
			]
		);

		return [ $mutation, $variables ];
	}

	protected function update_friendship( $initiator, $friend ) {
		$mutation = '
		mutation updateFriendshipTest( $clientMutationId: String!, $initiatorId: Int!, $friendId: Int! ) {
			updateFriendship(
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
				'clientMutationId' => $this->client_mutation_id,
				'initiatorId'      => $initiator,
				'friendId'         => $friend,
			]
		);

		return [ $mutation, $variables ];
	}

	protected function delete_friendship( $initiator, $friend ) {
		$mutation = '
		mutation deleteFriendshipTest( $clientMutationId: String!, $initiatorId: Int!, $friendId: Int! ) {
			deleteFriendship(
				input: {
					clientMutationId: $clientMutationId
					initiatorId: $initiatorId
					friendId: $friendId
				}
			)
          	{
				clientMutationId
				deleted
		    	friendship {
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
				'clientMutationId' => $this->client_mutation_id,
				'initiatorId'      => $initiator,
				'friendId'         => $friend,
			]
		);

		return [ $mutation, $variables ];
	}

	protected function create_friendship_object( $u = 0, $a = 0 ) {
		if ( empty( $u ) ) {
			$u = $this->factory->user->create();
		}

		if ( empty( $a ) ) {
			$a = $this->factory->user->create();
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
