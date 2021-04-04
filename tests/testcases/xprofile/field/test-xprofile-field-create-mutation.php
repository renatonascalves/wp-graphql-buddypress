<?php

/**
 * Test_XProfile_Field_Create_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
	}

	public function test_create_xprofile_field() {
        $this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'createXProfileField' => [
						'clientMutationId' => $this->client_mutation_id,
						'field' => [
							'name'        => 'XProfile Field Test',
							'description' => 'Description',
						],
					],
				],
			],
			$this->create_xprofile_field()
		);
	}

	public function test_create_xprofile_field_user_not_logged_in() {
		$this->assertQueryFailed( $this->create_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
    }

    public function test_create_xprofile_field_without_permission() {
        $this->bp->set_current_user( $this->factory->user->create() );

		$this->assertQueryFailed( $this->create_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
    }
}
