<?php

/**
 * Test_Member_Delete_Mutation Class.
 *
 * @group members
 */
class Test_Member_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase  {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_member_can_delete_his_own_account() {
		$u = $this->factory()->user->create();

		$this->bp->set_current_user( $u );

		$guid = $this->toRelayId( 'user', $u );

		$this->assertEquals(
			[
				'data' => [
					'deleteUser' => [
						'clientMutationId' => $this->client_mutation_id,
						'deletedId'        => $guid,
						'user'             => [
							'userId' => $u,
							'id'     => $guid,
						]
					]
				]
			],
			$this->delete_member( $u )
		);

		// Make sure the user actually got deleted.
		$this->assertFalse( get_user_by( 'id', $u ) );
	}

	public function test_admins_can_delete_members() {
		$u = $this->factory()->user->create();

		$this->bp->set_current_user( $this->admin );

		$guid = $this->toRelayId( 'user', $u );

		$this->assertEquals(
			[
				'data' => [
					'deleteUser' => [
						'clientMutationId' => $this->client_mutation_id,
						'deletedId'        => $guid,
						'user'             => [
							'userId' => $u,
							'id'     => $guid,
						]
					]
				]
			],
			$this->delete_member( $u )
		);

		// Make sure the user actually got deleted.
		$this->assertFalse( get_user_by( 'id', $u ) );
	}

	public function test_member_can_not_delete_other_members_account() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_member( $this->admin ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
	}

	public function test_member_can_not_delete_his_account_with_account_deletion_disabled() {
		bp_update_option( 'bp-disable-account-deletion', true );
		$this->assertTrue( bp_disable_account_deletion() );

		$u = $this->user;
		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->delete_member( $u ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );

		bp_update_option( 'bp-disable-account-deletion', false );
    }

	public function test_member_needs_to_be_loggin_to_delete_account() {
		$this->assertQueryFailed( $this->delete_member( $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }

	public function test_delete_member_with_invalid_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_member( 0000 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }
}
