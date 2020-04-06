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

	public function test_get_members_friends_query() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship( $u1, $this->user );
		$this->create_friendship( $u2, $this->user );
		$this->create_friendship( $u3, $this->user );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'user', $this->user );

        $this->bp->set_current_user( $this->user );

		// Create the query.
		$query = "
			query {
				user(id: \"{$global_id}\") {
					friends {
						nodes {
							initiator {
								userId
							}
							friend {
								userId
							}
						}
					}
				}
			}
		";

		$results = do_graphql_request( $query );

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		// Check our four members.
		$this->assertTrue( count( $results['data']['user']['friends']['nodes'] ) === 3 );
	}

	public function test_getting_friendship_with_initiator() {
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

	public function test_getting_friendship_with_invited_friend() {
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

	public function test_getting_friendship_with_non_logged_in_user() {
		$f         = $this->create_friendship();
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );
		$query     = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
			}
		}";

		$response = do_graphql_request( $query );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you need to be logged in to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_friendship_with_unauthorized_member() {
		$f = $this->create_friendship();

		$u = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'friendship', $f );

		$query = "{
			friendshipBy( id: \"{$global_id}\" ) {
				id
			}
		}";

		$response = do_graphql_request( $query );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you don\'t have permission to see this friendship.', $response['errors'][0]['message'] );
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
