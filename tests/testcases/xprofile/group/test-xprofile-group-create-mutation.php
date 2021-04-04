<?php

/**
 * Test_XProfile_Group_Create_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_create_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'createXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name'        => 'XProfile Group Test',
							'description' => 'Description',
						],
					],
				],
			],
			$this->create_xprofile_group()
		);
	}

	public function test_create_xprofile_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->create_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create() );

		$this->assertQueryFailed( $this->create_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
