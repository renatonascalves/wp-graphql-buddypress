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

	public function test_get_members_friends_query() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $this->user );
		$this->create_friendship_object( $u2, $this->user );
		$this->create_friendship_object( $u3, $this->user );

		$global_id = $this->toRelayId( 'user', $this->user );

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

		$response = $this->graphql( compact( 'query') );

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $response );

		// Check our four members.
		$this->assertTrue( count( $response['data']['user']['friends']['nodes'] ) === 3 );
	}
}
