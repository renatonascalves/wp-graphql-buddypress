<?php

/**
 * Test_Member_deleteUser_Mutation Class.
 *
 * @group members
 */
class Test_Member_deleteUser_Mutation extends WPGraphQL_BuddyPress_UnitTestCase  {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_member_can_delete_his_own_account() {
		$this->bp->set_current_user( $this->user );

		$guid = $this->toRelayId( 'user', $this->user );

		$this->assertQuerySuccessful( $this->delete_member( absint( $this->user ) ) )
			->hasField( 'deletedId', $guid )
			->hasField( 'user', [
				'userId' => $this->user,
				'id'     => $guid
			] );

		// Make sure the user actually got deleted.
		$this->assertFalse( get_user_by( 'id', $this->user ) );
	}

	public function test_admins_can_delete_members() {
		$this->bp->set_current_user( $this->admin );

		$guid = $this->toRelayId( 'user', $this->user );

		$this->assertQuerySuccessful( $this->delete_member( absint( $this->user ) ) )
			->hasField( 'deletedId', $guid )
			->hasField( 'user', [
				'userId' => $this->user,
				'id'     => $guid
			] );

		// Make sure the user actually got deleted.
		$this->assertFalse( get_user_by( 'id', $this->user ) );
	}

	public function test_member_can_not_delete_other_members_account() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_member( absint( $this->admin ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
	}

	public function test_member_can_not_delete_his_account_with_account_deletion_disabled() {
		bp_update_option( 'bp-disable-account-deletion', true );

		// Account deletion is disabled.
		$this->assertTrue( bp_disable_account_deletion() );

		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_member( absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );

		bp_update_option( 'bp-disable-account-deletion', false );
    }

	public function test_member_needs_to_be_loggin_to_delete_account() {
		$this->assertQueryFailed( $this->delete_member( absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }

	public function test_delete_member_with_invalid_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_member( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }

	/**
	 * Delete a member.
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	protected function delete_member( int $user_id = 0 ): array {
		$query = '
			mutation deleteUserTest(
				$clientMutationId: String!
				$id: ID!
			) {
				deleteUser(
					input: {
						clientMutationId: $clientMutationId
						id: $id
					}
				) {
					clientMutationId
					deletedId
					user {
						userId
						id
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'id'               => $this->toRelayId( 'user', $user_id ),
		];

		$operation_name = 'deleteUserTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
