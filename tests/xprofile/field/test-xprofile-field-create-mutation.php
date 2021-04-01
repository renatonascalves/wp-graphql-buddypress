<?php

/**
 * Test_XProfile_Field_Create_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Create_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

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
		$response = $this->create_xprofile_field();

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
    }

    public function test_create_xprofile_field_without_permission() {
        $this->bp->set_current_user( $this->factory->user->create() );

		$response = $this->create_xprofile_field();

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
    }

	protected function create_xprofile_field( $xprofile_group_id = null ) {
		$mutation = '
			mutation createXProfileFieldTest(
				$clientMutationId:String!,
				$name:String!,
				$description:String,
				$groupId:Int!,
				$type:XProfileFieldTypesEnum!,
			) {
				createXProfileField(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						description: $description
						groupId: $groupId
						type: $type
					}
				)
				{
					clientMutationId
					field {
						name
						description
					}
				}
			}
        ';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Field Test',
                'description'      => 'Description',
                'groupId'          => $xprofile_group_id ?? (int) $this->xprofile_group_id,
                'type'             => 'TEXTBOX',
			]
        );

		return do_graphql_request( $mutation, 'createXProfileFieldTest', $variables );
	}
}
