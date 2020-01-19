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
		$this->user               = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_create_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

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
				'initiatorId'      => $u1,
				'friendId'         => $u2,
			]
		);

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
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_user_can_not_create_friendship_to_other_people() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u3 );

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
				'initiatorId'      => $u1,
				'friendId'         => $u2,
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_create_friendship_user_not_logged_id() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

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
				'initiatorId'      => $u1,
				'friendId'         => $u2,
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_create_friendship_invalid_user() {
		$u1 = $this->bp_factory->user->create();

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
				'initiatorId'      => $u1,
				'friendId'         => 111,
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_create_friendship_force_accept() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $this->user );

		$mutation = '
		mutation createFriendshipTest( $clientMutationId: String!, $initiatorId: Int, $friendId: Int!, $force: Boolean ) {
			createFriendship(
				input: {
					clientMutationId: $clientMutationId
					initiatorId: $initiatorId
					friendId: $friendId
					force: $force
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
				'friendId'         => $u2,
				'force'            => true,
			]
		);

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'friendship' => [
							'isConfirmed' => true,
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
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}

	public function test_create_friendship_force_accept_authorized() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$mutation = '
		mutation createFriendshipTest( $clientMutationId: String!, $initiatorId: Int, $friendId: Int!, $force: Boolean ) {
			createFriendship(
				input: {
					clientMutationId: $clientMutationId
					initiatorId: $initiatorId
					friendId: $friendId
					force: $force
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
				'friendId'         => $u2,
				'force'            => true,
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createFriendshipTest', $variables )
		);
	}
	
}
