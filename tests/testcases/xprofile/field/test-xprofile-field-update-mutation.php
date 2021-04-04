<?php

/**
 * Test_XProfile_Field_Update_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Update_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
	}

    public function test_update_xprofile_field() {
        $field_id = $this->bp_factory->xprofile_field->create(
            [
                'name'           => 'Field Name',
                'field_group_id' => $this->xprofile_group_id
            ]
        );

		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'updateXProfileField' => [
						'clientMutationId' => $this->client_mutation_id,
						'field'            => [
                            'fieldId' => $field_id,
							'name'    => 'Updated',
						],
					],
				],
			],
			$this->update_xprofile_field( $field_id, 'Updated' )
		);
	}

    /**
	 * @todo test field_order update
	 */
	public function test_update_xprofile_field_field_order() {
		return $this->assertTrue( true );
	}

    public function test_update_xprofile_field_with_no_input() {
		$query = '
			mutation updateXProfileFieldTest( $clientMutationId: String! ) {
				updateXProfileField(
					input: {
						clientMutationId: $clientMutationId
					}
				)
				{
                    clientMutationId
					field {
						fieldId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
		];

		$operation_name = 'updateXProfileFieldTest';

		$this->assertQueryFailed( $this->graphql( compact( 'query', 'operation_name', 'variables' ) ) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_update_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->update_xprofile_field( 99999999 ) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_update_xprofile_field_user_not_logged_in() {
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );

		$this->assertQueryFailed( $this->update_xprofile_field( $field_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
    }

    public function test_update_xprofile_field_user_without_permission() {
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );

		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        $this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->update_xprofile_field( $field_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
