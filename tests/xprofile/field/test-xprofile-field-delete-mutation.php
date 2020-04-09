<?php

/**
 * Test_XProfile_Field_Delete_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Delete_Mutation extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $xprofile_group_id;
	public $xprofile_field_id;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
		$this->xprofile_field_id  = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
		$this->admin              = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
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

		$response = $this->delete_xprofile_field( 99999999 );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile field does not exist.', $response['errors'][0]['message'] );
	}

	public function test_delete_xprofile_field_user_not_logged_in() {
		$response = $this->delete_xprofile_field( $this->xprofile_field_id );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
    }

    public function test_delete_xprofile_field_user_without_permission() {
		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        $this->bp->set_current_user( $u1 );

		$response = $this->delete_xprofile_field( $this->xprofile_field_id );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function delete_xprofile_field( $field_id = null ) {
		$mutation = '
			mutation deleteXProfileFieldTest( $clientMutationId: String!, $fieldId: Int ) {
				deleteXProfileField(
					input: {
						clientMutationId: $clientMutationId
						fieldId: $fieldId
					}
				)
				{
					clientMutationId
					deleted
					field {
						fieldId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'fieldId'          => $field_id,
		];

		return do_graphql_request( $mutation, 'deleteXProfileFieldTest', $variables );
	}
}
