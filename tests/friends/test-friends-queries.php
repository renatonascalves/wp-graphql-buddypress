<?php

/**
 * Test_Friendship_Queries Class.
 *
 * @group friendship
 */
class Test_Friendship_Queries extends WP_UnitTestCase {

	public $bp_factory;
	public $bp;
	public $user;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->user       = $this->factory->user->create();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_friendship_by_with_initiator() {
		$u = $this->bp_factory->user->create();
		$f = $this->create_friendship( $u, $this->user );

		$this->bp->set_current_user( $this->user );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );

		$query = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
				friendshipId
				isConfirmed
				initiator {
					userId
				}
				friend {
					userId
				}
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'friendshipBy' => [
						'id'           => $global_id,
						'friendshipId' => $f,
						'isConfirmed' => false,
						'initiator' => [
							'userId' => $this->user,
						],
						'friend' => [
							'userId' => $u,
						],
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_friendship_by_with_invited_friend() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$f = $this->create_friendship( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );

		$query = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
				friendshipId
				isConfirmed
				friend {
					userId
				}
				initiator {
					userId
				}
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'friendshipBy' => [
						'id'           => $global_id,
						'friendshipId' => $f,
						'isConfirmed' => false,
						'friend' => [
							'userId' => $u1,
						],
						'initiator' => [
							'userId' => $u2,
						],
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_friendship_by_with_non_logged_in_user() {
		$f         = $this->create_friendship();
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );
		$query     = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
			}
		}";

		$this->assertArrayHasKey( 'errors', do_graphql_request( $query ) );
	}

	public function test_friendship_by_with_unauthorized_member() {
		$f = $this->create_friendship();

		$u = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );

		$query = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
			}
		}";

		$this->assertArrayHasKey( 'errors', do_graphql_request( $query ) );
	}

	protected function create_friendship( $u = 0, $initiator = 0 ) {
		if ( empty( $u ) ) {
			$u = $this->factory->user->create();
		}

		if ( empty( $initiator ) ) {
			$initiator = $this->factory->user->create();
		}

		$friendship                    = new BP_Friends_Friendship();
		$friendship->initiator_user_id = $initiator;
		$friendship->friend_user_id    = $u;
		$friendship->is_confirmed      = 0;
		$friendship->is_limited        = 0;
		$friendship->date_created      = bp_core_current_time();
		$friendship->save();

		return $friendship->id;
	}
}
