<?php

/**
 * Test_XProfile_Group_Delete_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

    public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create();
	}

	public function test_delete_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'deleteXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
					],
				],
			],
			$this->delete_xprofile_group()
		);
	}

	public function test_delete_xprofile_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_xprofile_group( 99999999 ) )
			->expectedErrorMessage( 'This XProfile group does not exist.' );
	}

	public function test_delete_xprofile_group_user_not_logged_in() {
        $this->assertQueryFailed( $this->delete_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_xprofile_group_user_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

        $this->assertQueryFailed( $this->delete_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
