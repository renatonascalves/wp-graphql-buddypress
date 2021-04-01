<?php

/**
 * Test_Member_Delete_Mutation Class.
 *
 * @group members
 */
class Test_Member_Delete_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase  {

	public static $admin;
	public static $user;
	public static $bp;
	public static $client_mutation_id;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp                 = new BP_UnitTestCase();
		self::$client_mutation_id = 'someUniqueId';
		self::$user               = self::factory()->user->create();
		self::$admin              = self::factory()->user->create( [ 'role' => 'administrator' ] );
	}

	public function test_member_can_delete_his_own_account() {
		$u = self::factory()->user->create();

		self::$bp->set_current_user( $u );

		$guid = $this->toRelayId( 'user', $u );

		$this->assertEquals(
			[
				'data' => [
					'deleteUser' => [
						'clientMutationId' => self::$client_mutation_id,
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
						'clientMutationId' => self::$client_mutation_id,
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

		$response = $this->delete_member( self::$admin );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
	}

	public function test_member_can_not_delete_his_account_with_account_deletion_disabled() {
		bp_update_option( 'bp-disable-account-deletion', true );

		$this->assertTrue( bp_disable_account_deletion() );

		$u = self::$user;

		self::$bp->set_current_user( $u );

		$response = $this->delete_member( $u );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );

		bp_update_option( 'bp-disable-account-deletion', false );
    }

	public function test_member_needs_to_be_loggin_to_delete_account() {
		$response = $this->delete_member( self::$user );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
    }

	public function test_delete_member_with_invalid_id() {
		self::$bp->set_current_user( self::$user );

		$response = $this->delete_member( 000000 );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
    }

	protected function delete_member( $u = 0 ) {
		$mutation = '
			mutation deleteUserTest( $clientMutationId: String!, $id: ID! ) {
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
			'id'               => $this->toRelayId( 'user', $u ),
			'clientMutationId' => self::$client_mutation_id,
		];

		return do_graphql_request( $mutation, 'deleteUserTest', $variables );
	}
}
