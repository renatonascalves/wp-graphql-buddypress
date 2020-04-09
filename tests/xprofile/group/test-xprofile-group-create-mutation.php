<?php

/**
 * Test_XProfile_Group_Create_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Create_Mutation extends WP_UnitTestCase {

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

		$mutation = $this->create_xprofile_group();

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
			do_graphql_request( $mutation[0], 'createXProfileGroupTest', $mutation[1] )
		);
	}

	public function test_create_xprofile_group_user_not_logged_in() {
		$mutation = $this->create_xprofile_group();
		$response = do_graphql_request( $mutation[0], 'createXProfileGroupTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_create_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create() );

		$mutation = $this->create_xprofile_group();
		$response = do_graphql_request( $mutation[0], 'createXProfileGroupTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function create_xprofile_group( $name = null, $desc = null ) {

		$mutation = '
		mutation createXProfileGroupTest(
			$clientMutationId:String!,
			$name:String!,
			$description:String
		) {
			createXProfileGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					description: $description
				}
			)
          	{
				clientMutationId
		    	group {
					name
					description
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => $name ?? 'XProfile Group Test',
				'description'      => $desc ?? 'Description',
			]
		);

		return [ $mutation, $variables ];
	}
}
