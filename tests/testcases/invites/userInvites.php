<?php

/**
 * Test_User_userInvites_Queries Class.
 *
 * @group member
 * @group user
 * @group groups
 * @group invitation
 * @group invite
 * @group request
 */
class Test_User_userInvites_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->g1 = $this->bp_factory->group->create(
			[
				'status'     => 'private',
				'creator_id' => $this->random_user,
			]
		);
		$this->g2 = $this->bp_factory->group->create(
			[
				'status'     => 'private',
				'creator_id' => $this->random_user,
			]
		);
		$this->g3 = $this->bp_factory->group->create(
			[
				'status'     => 'private',
				'creator_id' => $this->random_user,
			]
		);
		$this->g4 = $this->bp_factory->group->create(
			[
				'status'     => 'private',
				'creator_id' => $this->random_user,
			]
		);
	}

	public function test_get_user_invites() {
		$u1 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1 ], $this->g1, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g2, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g3, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g4, $this->random_user );

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 4 );
	}

	public function test_get_user_invites_as_admin() {
		$u1 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1 ], $this->g1, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g2, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g3, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g4, $this->random_user );

		$this->bp->set_current_user( $this->admin );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 4 );
	}

	public function test_get_user_invites_without_access() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1 ], $this->g1, $this->random_user );

		$this->bp->set_current_user( $u2 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['user']['invitations']['nodes'] );
	}

	public function test_get_user_invites_from_group() {
		$u1 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1 ], $this->g1, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g2, $this->random_user );
		$this->populate_group_with_invites( [ $u1 ], $this->g4, $this->random_user );
		$invite = groups_invite_user(
			[
				'user_id'     => $u1,
				'group_id'    => $this->g3,
				'inviter_id'  => $this->random_user,
				'send_invite' => 1,
			]
		);

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [
					'itemId' => $this->g3,
					'type'   => 'INVITE',
				],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 1 );
		$this->assertSame( $invite, $response['data']['user']['invitations']['nodes'][0]['databaseId'] );
	}

	public function test_get_user_invites_from_invalid_group() {
		$u1 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1 ], $this->g1, $this->random_user );

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [
					'itemId' => $this->g2,
					'type'   => 'INVITE',
				],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['user']['invitations']['nodes'] );
	}

	public function test_get_user_group_requests() {
		$u1 = $this->factory->user->create();
		$r1 = groups_send_membership_request(
			[
				'group_id' => $this->g1,
				'user_id'  => $u1,
			]
		);
		$r2 = groups_send_membership_request(
			[
				'group_id' => $this->g2,
				'user_id'  => $u1,
			]
		);

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [ 'type' => 'REQUEST' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 2 );
		$this->assertEqualSets(
			[ $r1, $r2 ],
			wp_list_pluck(
				$response['data']['user']['invitations']['nodes'],
				'databaseId'
			)
		);
	}

	public function test_get_user_group_requests_as_admin() {
		$u1 = $this->factory->user->create();
		$r1 = groups_send_membership_request(
			[
				'group_id' => $this->g1,
				'user_id'  => $u1,
			]
		);
		$r2 = groups_send_membership_request(
			[
				'group_id' => $this->g2,
				'user_id'  => $u1,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [ 'type' => 'REQUEST' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 2 );
		$this->assertEqualSets(
			[ $r1, $r2 ],
			wp_list_pluck(
				$response['data']['user']['invitations']['nodes'],
				'databaseId'
			)
		);
	}

	public function test_get_user_group_requests_from_specific_group() {
		$u1 = $this->factory->user->create();

		groups_send_membership_request(
			[
				'group_id' => $this->g1,
				'user_id'  => $u1,
			]
		);
		$request = groups_send_membership_request(
			[
				'group_id' => $this->g2,
				'user_id'  => $u1,
			]
		);

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [
					'itemId' => $this->g2,
					'type'   => 'REQUEST',
				],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['user']['invitations']['nodes'] ) === 1 );
		$this->assertSame( $request, $response['data']['user']['invitations']['nodes'][0]['databaseId'] );
	}

	public function test_get_user_group_requests_from_invalid_group() {
		$u1 = $this->factory->user->create();

		groups_send_membership_request(
			[
				'group_id' => $this->g1,
				'user_id'  => $u1,
			]
		);

		$this->bp->set_current_user( $u1 );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u1 ),
				'where' => [
					'itemId' => $this->g3,
					'type'   => 'REQUEST',
				],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['user']['invitations']['nodes'] );
	}

	public function test_get_user_requests_unauthenticated() {
		$u = $this->factory->user->create();

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u ),
				'where' => [ 'type' => 'REQUEST' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['user']['invitations']['nodes'] );

		$response = $this->getMemberInvitesQuery(
			[
				'id'    => $this->toRelayId( 'user', (string) $u ),
				'where' => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['user']['invitations']['nodes'] );
	}

	public function test_get_user_requests_without_type() {
		$u = $this->factory->user->create();

		$this->bp->set_current_user( $u );

		$response = $this->getMemberInvitesQuery(
			[ 'id' => $this->toRelayId( 'user', (string) $u ) ]
		);

		$this->assertQueryFailed( $response );
	}

	/**
	 * Get invites|requests from user.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function getMemberInvitesQuery( array $variables = [] ): array {
		$query = 'query getMemberInvitesQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:UserToGroupInvitationConnectionWhereArgs
			$id:ID!
		) {
			user(id: $id) {
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

		$operation_name = 'getMemberInvitesQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
