<?php

/**
 * est_Group_groupMembershipRequests_Queries Class.
 *
 * @group group-membership
 * @group groups
 * @group invitation
 * @group invite
 * @group request
 */
class Test_Group_groupMembershipRequests_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Private Group ID.
	 *
	 * @var int
	 */
	public $private_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->random_user,
				'status'     => 'private',
			]
		);
	}

	public function test_get_public_group_membership_requests() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->group, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->group, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->group, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->group,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy']['invitations']['nodes'] );
	}

	public function test_get_group_membership_requests() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['groupBy']['invitations']['nodes'] ) === 3 );
	}

	public function test_get_group_membership_requests_as_group_creator() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->random_user );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['groupBy']['invitations']['nodes'] ) === 3 );
	}

	public function test_get_group_membership_requests_as_group_admin() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->add_user_to_group( $u4, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u4 );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['groupBy']['invitations']['nodes'] ) === 3 );
	}

	public function test_get_group_membership_requests_as_group_moderator() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->add_user_to_group( $u4, $this->private_group_id, [ 'is_mod' => true ] );
		$this->bp->set_current_user( $u4 );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['groupBy']['invitations']['nodes'] ) === 3 );
	}

	public function test_get_group_membership_requests_as_requestor() {
		$u = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u ] );

		$this->bp->set_current_user( $u );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );

		// User is not from private group, so it can't see the group itself,
		// let alone the requests in it.
		$this->assertEmpty( $response['data']['groupBy'] );
	}

	public function test_get_group_membership_requests_as_group_member() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->add_user_to_group( $u4, $this->private_group_id );
		$this->bp->set_current_user( $u4 );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy']['invitations']['nodes'] );
	}

	public function test_get_group_membership_requests_using_user_id_param() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->admin );

		// Try a user without a request.
		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [
					'userId' => $u4,
					'type'   => 'REQUEST'
				]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy']['invitations']['nodes'] );

		// Try another user with a request.
		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [
					'userId' => $u1,
					'type'   => 'REQUEST'
				]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['groupBy']['invitations']['nodes'] ) === 1 );
	}

	public function test_get_group_membership_requests_unauthenticated() {
		$response = $this->groupMembershipRequestsQuery(
			[
				'id' => $this->private_group_id,
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy'] );
	}

	public function test_get_group_membership_requests_before() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();

		$r1 = groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		$r2 = groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id'     => $this->private_group_id,
				'last'   => 1,
				'before' => $this->key_to_cursor( $r2 ),
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $r1, $response['data']['groupBy']['invitations']['edges'][0]['node']['databaseId'] );
	}

	public function test_get_group_membership_requests_after() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();

		$r1 = groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u1 ] );
		$r2 = groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u2 ] );
		groups_send_membership_request( [ 'group_id' => $this->private_group_id, 'user_id' => $u3 ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupMembershipRequestsQuery(
			[
				'id'    => $this->private_group_id,
				'after' => $this->key_to_cursor( $r1 ),
				'where' => [ 'type' => 'REQUEST' ]
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertSame( $r2, $response['data']['groupBy']['invitations']['edges'][0]['node']['databaseId'] );
	}

	/**
	 * Get group membership requests.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function groupMembershipRequestsQuery( array $variables = [] ): array {
		$query = 'query groupMembershipRequestsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:GroupToGroupInvitationConnectionWhereArgs
			$id:Int
		) {
			groupBy(groupId: $id) {
				id
				databaseId
				invitations(
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
							id
							databaseId
						}
					}
					nodes {
						id
						databaseId
					}
				}
			}
		}';

		$operation_name = 'groupMembershipRequestsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
