<?php

/**
 * Test_Group_groupInvites_Queries Class.
 *
 * @group groups
 * @group invitation
 * @group invite
 */
class Test_Group_groupInvites_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

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

	public function test_get_group_invites() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1, $u2, $u3, $u4 ], $this->private_group_id, $this->random_user );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['group']['invitations']['nodes'] ) === 4 );
	}

	public function test_get_group_invites_as_group_creator() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1, $u2, $u3, $u4 ], $this->private_group_id, $this->random_user );

		$this->bp->set_current_user( $this->random_user );

		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['group']['invitations']['nodes'] ) === 4 );
	}

	public function test_get_group_invites_as_group_admin() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();
		$u5 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1, $u2, $u3, $u4 ], $this->private_group_id, $this->random_user );

		$this->bp->add_user_to_group( $u5, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u5 );

		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertCount( 4, $response['data']['group']['invitations']['nodes'] );
	}

	public function test_get_group_invites_as_group_moderator() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();
		$u5 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1, $u2, $u3, $u4 ], $this->private_group_id, $this->random_user );

		$this->bp->add_user_to_group( $u5, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u5 );

		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( count( $response['data']['group']['invitations']['nodes'] ) === 4 );
	}

	public function test_get_group_invites_as_group_member() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();
		$u5 = $this->factory->user->create();

		$this->populate_group_with_invites( [ $u1, $u2, $u3, $u4 ], $this->private_group_id, $this->random_user );

		$this->bp->add_user_to_group( $u5, $this->private_group_id );
		$this->bp->set_current_user( $u5 );

		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['group']['invitations']['nodes'] );
	}

	public function test_get_group_invites_unauthenticated() {
		$response = $this->groupInvitesQuery(
			[
				'id'     => $this->private_group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'INVITE' ],
			]
		);

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['group'] );
	}

	/**
	 * Get group membership requests.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function groupInvitesQuery( array $variables = [] ): array {
		$query = 'query groupInvitesQuery(
			$id:ID!
			$idType:GroupIdTypeEnum
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:GroupToGroupInvitationConnectionWhereArgs
		) {
			group(id: $id, idType: $idType) {
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

		$operation_name = 'groupInvitesQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
