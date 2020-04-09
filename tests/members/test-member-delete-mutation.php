<?php

/**
 * Test_Member_Delete_Mutation Class.
 *
 * @group members
 */
class Test_Member_Delete_Mutation extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->client_mutation_id = 'someUniqueId';
		$this->admin              = $this->factory->user->create( [
			'role'       => 'administrator',
            'user_email' => 'admin@example.com',
            'user_login' => 'user',
		] );
	}

	public function test_member_delete_his_own_account() {
		bp_update_option( 'bp-disable-account-deletion', true );

		$u = $this->factory->user->create();

		$this->bp->set_current_user( $u );

		$guid = \GraphQLRelay\Relay::toGlobalId( 'user', $u );

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
			$this->delete_member( $guid )
		);

		$user_obj_after_delete = get_user_by( 'id', $u );

		// Make sure the user actually got deleted.
		$this->assertFalse( $user_obj_after_delete );
	}

	public function test_member_can_not_delete_other_member_account() {
		$u = $this->factory->user->create();

		$this->bp->set_current_user( $u );

		$guid = \GraphQLRelay\Relay::toGlobalId( 'user', $this->admin );

		$response = $this->delete_member( $guid );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
	}

	public function test_delete_member_with_account_deletion_disabled() {
		bp_update_option( 'bp-disable-account-deletion', false );

		$u = $this->factory->user->create();

		$this->bp->set_current_user( $u );

		$guid     = \GraphQLRelay\Relay::toGlobalId( 'user', $u );
		$response = $this->delete_member( $guid );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
    }

	public function test_delete_member_user_not_logged_in() {
		$guid     = \GraphQLRelay\Relay::toGlobalId( 'user', $this->admin );
		$response = $this->delete_member( $guid );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
    }

	public function test_delete_member_with_invalid_id() {
		$guid     = \GraphQLRelay\Relay::toGlobalId( 'user', 000000 );
		$response = $this->delete_member( $guid );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to delete users.', $response['errors'][0]['message'] );
    }

	public function test_admins_can_delete_member_account() {
		$u = $this->factory->user->create();

		$this->bp->set_current_user( $this->admin );

		$guid = \GraphQLRelay\Relay::toGlobalId( 'user', $u );

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
			$this->delete_member( $guid )
		);

		$user_obj_after_delete = get_user_by( 'id', $u );

		// Make sure the user actually got deleted.
		$this->assertFalse( $user_obj_after_delete );
	}

	protected function delete_member( $guid_id = 0 ) {
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
			'id'               => $guid_id,
			'clientMutationId' => $this->client_mutation_id,
		];

		return do_graphql_request( $mutation, 'deleteUserTest', $variables );
	}
}
