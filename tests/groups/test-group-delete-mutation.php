<?php

/**
 * Test_Group_Delete_Mutation Class.
 *
 * @group groups
 */
class Test_Group_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $group_id;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->admin              = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
		$this->group_id = $this->bp_factory->group->create( array(
			'name'        => 'Deleted Group',
			'creator_id'  => $this->admin,
		) );

	}

	public function test_delete_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'deleteGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
					],
				],
			],
			$this->delete_group()
		);
	}

	public function test_delete_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_group( 99999999 ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_delete_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_user_without_permission() {
		$u = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_moderators_can_delete() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'deleteGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
					],
				],
			],
			$this->delete_group()
		);
	}
}
