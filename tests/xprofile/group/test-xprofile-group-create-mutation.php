<?php

/**
 * Test_XProfile_Group_Create_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
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
