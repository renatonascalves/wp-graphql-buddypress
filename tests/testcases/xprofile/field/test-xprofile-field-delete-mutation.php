<?php

/**
 * Test_XProfile_Field_Delete_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $xprofile_field_id;
	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
		$this->xprofile_field_id  = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
	}

	public function test_delete_xprofile_field() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'deleteXProfileField' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'field'            => [
							'fieldId' => $this->xprofile_field_id,
						],
					],
				],
			],
			$this->delete_xprofile_field( $this->xprofile_field_id )
		);
	}

	public function test_delete_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_xprofile_field( 99999999 ) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_delete_xprofile_field_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_xprofile_field( $this->xprofile_field_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
    }

    public function test_delete_xprofile_field_user_without_permission() {
		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        $this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->delete_xprofile_field( $this->xprofile_field_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
