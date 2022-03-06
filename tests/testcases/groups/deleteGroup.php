<?php

/**
 * Test_Group_deleteGroup_Mutation Class.
 *
 * @group groups
 */
class Test_Group_deleteGroup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Group ID.
	 *
	 * @var int
	 */
	public $group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->group_id = $this->bp_factory->group->create(
			[
				'name'       => 'Deleted Group',
				'creator_id' => $this->user,
			]
		);
	}

	public function test_group_non_member_but_site_admin_can_delete_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_group() )
			->hasField( 'deleted', true )
			->notHasField( 'uri' );
	}

	public function test_delete_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_delete_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_user_without_permission() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_banned_group_members_can_not_delete() {
		// Add user to group.
		$this->bp->add_user_to_group( $this->random_user, $this->group_id );

		// Ban member.
		( new BP_Groups_Member( $this->random_user, $this->group_id ) )->ban();

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_regular_group_member_can_not_delete_group() {
		// Add user to group.
		$this->bp->add_user_to_group( $this->random_user, $this->group_id );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_moderators_can_not_delete_group() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_admins_can_delete() {
		// Add user to group as a group admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->delete_group() )
			->hasField( 'deleted', true );
	}

	/**
	 * Delete group mutation.
	 *
	 * @param int|null $group_id Group ID.
	 * @return array
	 */
	protected function delete_group( $group_id = null ): array {
		$query = '
			mutation deleteGroupTest(
				$clientMutationId:String!
				$groupId:Int
			) {
				deleteGroup(
					input: {
						clientMutationId: $clientMutationId
						groupId: $groupId
					}
				)
				{
					clientMutationId
					deleted
					group {
						name
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id ?? $this->group_id,
		];

		$operation_name = 'deleteGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
