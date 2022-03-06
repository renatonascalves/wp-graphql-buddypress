<?php

/**
 * Test_Friendship_friends_Queries Class.
 *
 * @group friends
 */
class Test_Friendship_friends_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_get_friends_from_member() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user ) );
		$this->create_friendship_object( $u2, absint( $this->user ) );
		$this->create_friendship_object( $u3, absint( $this->user ) );

		$this->bp->set_current_user( $this->user );

		$response = $this->get_friends( [ 'id' => $this->toRelayId( 'user', (string) $this->user ) ] );

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['friends']['nodes'] ) === 3 );
	}

	public function test_get_first_friends_from_member() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user ) );
		$this->create_friendship_object( $u2, absint( $this->user ) );

		$this->bp->set_current_user( $this->user );

		$response = $this->get_friends(
			[
				'id'    => $this->toRelayId( 'user', (string) $this->user ),
				'first' => 1,
				'after' => '',
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $u1, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
	}

	public function test_get_friends_from_member_after() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user ) );
		$this->create_friendship_object( $u2, absint( $this->user ) );
		$this->create_friendship_object( $u3, absint( $this->user ) );

		$this->bp->set_current_user( $this->user );

		$response = $this->get_friends(
			[
				'id'    => $this->toRelayId( 'user', (string) $this->user ),
				'after' => $this->key_to_cursor( BP_Friends_Friendship::get_friendship_id( $u2, $this->user ) ),
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $u3, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
	}

	public function test_get_friends_from_member_before() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user ) );
		$this->create_friendship_object( $u2, absint( $this->user ) );
		$this->create_friendship_object( $u3, absint( $this->user ) );

		$this->bp->set_current_user( $this->user );

		$response = $this->get_friends(
			[
				'id'     => $this->toRelayId( 'user', (string) $this->user ),
				'last'   => 1,
				'before' => $this->key_to_cursor( BP_Friends_Friendship::get_friendship_id( $u2, $this->user ) ),
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $u1, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
	}

	/**
	 * Get friends query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function get_friends( array $variables = [] ): array {
		$query = 'query friendsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:UserToFriendshipConnectionWhereArgs
			$id:ID!
		) {
			user(id:$id) {
				id,
				friends(
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
							initiator {
								userId
							}
							friend {
								userId
							}
						}
					}
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
		}';

		$operation_name = 'friendsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
