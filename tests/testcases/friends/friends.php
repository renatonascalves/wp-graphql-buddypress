<?php

/**
 * Test_Friendship_friends_Queries Class.
 *
 * @group friends
 */
class Test_Friendship_friends_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_friends_from_member() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user_id ) );
		$this->create_friendship_object( $u2, absint( $this->user_id ) );
		$this->create_friendship_object( $u3, absint( $this->user_id ) );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->get_friends( [ 'id' => $this->toRelayId( 'user', (string) $this->user_id ) ] );

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['friends']['nodes'] ) === 3 );
	}

	public function test_get_first_friends_from_member() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user_id ) );
		$this->create_friendship_object( $u2, absint( $this->user_id ) );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->get_friends(
			[
				'id'    => $this->toRelayId( 'user', (string) $this->user_id ),
				'first' => 1,
				'after' => '',
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $u2, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user_id, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
	}

	public function test_get_friends_from_member_after() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user_id ) );
		$this->create_friendship_object( $u2, absint( $this->user_id ) );
		$this->create_friendship_object( $u3, absint( $this->user_id ) );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->get_friends(
			[
				'id'    => $this->toRelayId( 'user', (string) $this->user_id ),
				'first' => 1,
				'after' => $this->key_to_cursor( BP_Friends_Friendship::get_friendship_id( $u2, $this->user_id ) ),
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $u3, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user_id, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
	}

	public function test_get_friends_from_member_before() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, absint( $this->user_id ) );
		$this->create_friendship_object( $u2, absint( $this->user_id ) );
		$this->create_friendship_object( $u3, absint( $this->user_id ) );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->get_friends(
			[
				'id'     => $this->toRelayId( 'user', (string) $this->user_id ),
				'last'   => 1,
				'before' => $this->key_to_cursor( BP_Friends_Friendship::get_friendship_id( $u2, $this->user_id ) ),
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasEdges();

		$this->assertSame( $u3, $response['data']['user']['friends']['edges'][0]['node']['initiator']['userId'] );
		$this->assertSame( $this->user_id, $response['data']['user']['friends']['edges'][0]['node']['friend']['userId'] );
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

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
