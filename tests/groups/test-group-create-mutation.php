<?php

/**
 * Test_Groups_Create_Mutation Class.
 *
 * @group groups
 */
class Test_Groups_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

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

	public function test_create_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name' => 'Group Test',
							'slug' => 'group-slug',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->create_group()
		);
	}

	/**
	 * BuddyPress group creation is open by default.
	 */
	public function test_create_group_as_a_regular_user() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name' => 'Group Test',
							'slug' => 'group-slug',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->create_group()
		);
	}

	public function test_create_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->create_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_group_when_disabled() {
		$this->bp->set_current_user( $this->factory->user->create() );

		add_filter( 'bp_user_can_create_groups', '__return_false' );

		$this->assertQueryFailed( $this->create_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_group_with_valid_status() {
		$this->bp->set_current_user( $this->factory->user->create() );

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name'   => 'Group Test',
							'slug'   => 'group-slug',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->create_group( 'PUBLIC' )
		);
	}

	public function test_create_group_with_invalid_status() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->create_group( 'random-status' ) )
			->expectedErrorMessage( 'Variable "$status" got invalid value "random-status"; Expected type GroupStatusEnum.' );
	}
}
