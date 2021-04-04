<?php

/**
 * Test_Group_Update_Mutation Class.
 *
 * @group groups
 */
class Test_Group_Update_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->group_id = $this->bp_factory->group->create( array(
			'name'        => 'Deleted Group',
			'creator_id'  => $this->admin,
		) );
	}

	public function test_update_group() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name'   => 'Updated Group',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->update_group( 'PUBLIC',$group_id, 'Updated Group' )
		);
	}

	public function test_update_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->update_group( 'PUBLIC', 99999999, 'Updated Group' ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_update_group_without_a_logged_in() {
		$this->assertQueryFailed( $this->update_group( 'PUBLIC', $this->group_id, 'Updated Group' ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

		$this->assertQueryFailed( $this->update_group( 'PUBLIC', $this->group_id, 'Updated Group' ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_moderators_can_update() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated Group',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->update_group( 'PUBLIC', $group_id, 'Updated Group' )
		);
	}

	public function test_update_group_invalid_status() {
		$u = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->update_group( 'random-status' ) )
			->expectedErrorMessage( 'Variable "$status" got invalid value "random-status"; Expected type GroupStatusEnum.' );
	}
}
