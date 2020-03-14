<?php

/**
 * Test_XProfile_Field_Update_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Update_Mutation extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $xprofile_group_id;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->xprofile_group_id  = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
		$this->admin              = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
	}

	public function tearDown() {
		parent::tearDown();
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
		$mutation = '
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

		$response = do_graphql_request( $mutation, 'updateXProfileFieldTest', $variables );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile field does not exist.', $response['errors'][0]['message'] );
	}

	public function test_update_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->update_xprofile_field( 99999999 );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile field does not exist.', $response['errors'][0]['message'] );
	}

	public function test_update_xprofile_field_user_not_logged_in() {
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
		$response = $this->update_xprofile_field( $field_id );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
    }

    public function test_update_xprofile_field_user_without_permission() {
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );

		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        $this->bp->set_current_user( $u1 );

		$response = $this->update_xprofile_field( $field_id );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function update_xprofile_field( $field_id = null, $name = null ) {
		$mutation = '
			mutation updateXProfileFieldTest( $clientMutationId: String!, $fieldId: Int, $name: String ) {
				updateXProfileField(
					input: {
						clientMutationId: $clientMutationId
						fieldId: $fieldId
                        name: $name
					}
				)
				{
					clientMutationId
					field {
						fieldId
                        name
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'fieldId'          => $field_id,
            'name'             => $name ?? 'Updated XProfile Group',
		];

		return do_graphql_request( $mutation, 'updateXProfileFieldTest', $variables );
	}
}
