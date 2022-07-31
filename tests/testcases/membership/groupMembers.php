<?php

/**
 * Test_Group_Members_Queries Class.
 *
 * @group group-membership
 * @group groups
 */
class Test_Group_Members_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_public_group() {
		$u1 = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u1, $this->group );

		$response = $this->get_group_members( [ 'id' => $this->group, 'idType' => 'DATABASE_ID', ] );

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->group )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $u1 ] ],
				],
			);
	}

	public function test_get_group_members_excluding_banned() {
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u1, $this->group );
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		// Ban member.
		( new BP_Groups_Member( $this->random_user, $this->group ) )->ban();

		$response = $this->get_group_members(
			[
				'id'    => $this->group,
				'idType' => 'DATABASE_ID',
				'where' => [ 'excludeBanned' => true ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->group )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $u1 ] ],
				],
			);
	}

	public function test_get_group_members_excluding_banned_without_permission() {
		$this->bp->set_current_user( $this->user );

		$response = $this->get_group_members(
			[
				'id'    => $this->group,
				'idType' => 'DATABASE_ID',
				'where' => [ 'excludeBanned' => true ],
			]
		);

		$this->assertQueryFailed( $response )
			->expectedErrorMessage( 'Sorry, you do not have the necessary permissions to filter with this param.' );
	}

	public function test_get_group_members_without_excluded_ones() {
		$group_id = $this->bp_factory->group->create();
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u1, $group_id );
		$this->bp->add_user_to_group( $this->random_user, $group_id );

		$response = $this->get_group_members(
			[
				'id'    => $group_id,
				'idType' => 'DATABASE_ID',
				'where' => [ 'exclude' => $u1 ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $group_id )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $this->random_user ] ],
				],
			);
	}

	public function test_get_group_members_without_admins() {
		$group_id = $this->bp_factory->group->create();
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $group_id, [ 'is_admin' => true ] );
		$this->bp->add_user_to_group( $u2, $group_id, [ 'is_mod' => true ] );
		$this->bp->add_user_to_group( $this->random_user, $group_id );

		$response = $this->get_group_members(
			[
				'id'     => $group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'excludeAdminsMods' => true ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $group_id )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $this->random_user ] ],
				],
			);
	}

	public function test_get_group_mod_using_member_roles() {
		$group_id = $this->bp_factory->group->create();
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $group_id, [ 'is_admin' => true ] );
		$this->bp->add_user_to_group( $u2, $group_id, [ 'is_mod' => true ] );
		$this->bp->add_user_to_group( $this->random_user, $group_id );

		$response = $this->get_group_members(
			[
				'id'     => $group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'groupMemberRoles' => 'MOD' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $group_id )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $u2 ] ],
				],
			);
	}

	public function test_get_group_members_using_member_roles() {
		$group_id = $this->bp_factory->group->create();
		$this->bp->set_current_user( $this->admin );
		$this->bp->add_user_to_group( $this->random_user, $group_id );

		$response = $this->get_group_members(
			[
				'id'    => $group_id,
				'idType' => 'DATABASE_ID',
				'where' => [ 'groupMemberRoles' => 'MEMBER' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $group_id )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $this->random_user ] ],
				],
			);
	}

	public function test_get_group_banned_members_using_member_roles() {
		$group_id = $this->bp_factory->group->create();
		$u1       = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $group_id );
		$this->bp->add_user_to_group( $this->random_user, $group_id );

		// Ban member.
		( new BP_Groups_Member( $this->random_user, $group_id ) )->ban();

		$this->bp->set_current_user( $this->admin );

		$response = $this->get_group_members(
			[
				'id'     => $group_id,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'groupMemberRoles' => 'BANNED' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $group_id )
			->hasField(
				'members',
				[
					'nodes' => [ 0 => [ 'userId' => $this->random_user ] ],
				],
			);
	}

	public function test_get_group_banned_members_using_member_roles_without_permission() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $this->group );
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		// Ban member.
		( new BP_Groups_Member( $this->random_user, $this->group ) )->ban();

		$this->bp->set_current_user( $u2 );

		$response = $this->get_group_members(
			[
				'id'     => $this->group,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'groupMemberRoles' => 'BANNED' ],
			]
		);

		$this->assertQueryFailed( $response )
			->expectedErrorMessage( 'Sorry, you do not have the necessary permissions to filter with this param.' );
	}

	public function test_get_group_members_alphabetically() {
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create( [ 'name' => 'Jorge' ] );
		$u2 = $this->bp_factory->user->create( [ 'name' => 'Alfred' ] );
		$this->bp->add_user_to_group( $u1, $this->group );
		$this->bp->add_user_to_group( $u2, $this->group );

		$response = $this->get_group_members(
			[
				'id'     => $this->group,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'ALPHABETICAL' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->group )
			->hasField(
				'members',
				[
					'nodes' => [
						0 => [ 'userId' => $u1 ],
						1 => [ 'userId' => $u2 ],
					],
				],
			);
	}

	public function test_get_group_members_first_join() {
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u1, $this->group );
		$this->bp->add_user_to_group( $u2, $this->group );

		$response = $this->get_group_members(
			[
				'id'     => $this->group,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'FIRST_JOINED' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->group )
			->hasField(
				'members',
				[
					'nodes' => [
						0 => [ 'userId' => $u1 ],
						1 => [ 'userId' => $u2 ],
					],
				],
			);
	}

	public function test_get_group_members_last_joined() {
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $this->group );
		$this->bp->add_user_to_group( $u2, $this->group );

		$response = $this->get_group_members(
			[
				'id'     => $this->group,
				'idType' => 'DATABASE_ID',
				'where'  => [ 'type' => 'LAST_JOINED' ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->group )
			->hasField(
				'members',
				[
					'nodes' => [
						0 => [ 'userId' => $u1 ],
						1 => [ 'userId' => $u2 ],
					],
				],
			);
	}

	/**
	 * Get group members.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function get_group_members( array $variables = [] ): array {
		$query = 'query groupMembersQuery(
			$id:ID!
			$idType:GroupIdTypeEnum
			$where:GroupToUserConnectionWhereArgs
		) {
			group(id: $id, idType: $idType) {
				databaseId
				members(where: $where) {
					nodes {
						userId
					}
				}
			}
		}';

		$operation_name = 'groupMembersQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
