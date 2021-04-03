<?php

/**
 * Test_Member_Delete_Mutation Class.
 *
 * @group members
 */
class Test_Member_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase  {

	public $bp_factory;
	public $bp;
	public $admin;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->admin              = $this->factory->user->create();
		$this->client_mutation_id = 'someUniqueId';
	}

	public function test_member_can_delete_his_own_account() {
		$u = self::factory()->user->create();

		self::$bp->set_current_user( $u );

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
		$u = self::factory()->user->create();

		self::$bp->set_current_user( self::$admin );

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
		self::$bp->set_current_user( self::$user );

		$this->assertQueryFailed( $this->delete_member( self::$admin ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
	}

	public function test_member_can_not_delete_his_account_with_account_deletion_disabled() {
		bp_update_option( 'bp-disable-account-deletion', true );
		$this->assertTrue( bp_disable_account_deletion() );

		$u = self::$user;
		self::$bp->set_current_user( $u );

		$this->assertQueryFailed( $this->delete_member( $u ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );

		bp_update_option( 'bp-disable-account-deletion', false );
    }

	public function test_member_needs_to_be_loggin_to_delete_account() {
		$this->assertQueryFailed( $this->delete_member( self::$user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }

	public function test_delete_member_with_invalid_id() {
		self::$bp->set_current_user( self::$user );

		$this->assertQueryFailed( $this->delete_member( 0000 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to delete users.' );
    }
}
