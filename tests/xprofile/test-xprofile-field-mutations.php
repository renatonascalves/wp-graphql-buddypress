<?php

/**
 * Test_XProfile_Field_Mutations Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Mutations extends WP_UnitTestCase {

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

	public function tearDown() {
		parent::tearDown();
	}

	public function test_create_xprofile_field() {
        $this->bp->set_current_user( $this->admin );
        
        $group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );

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
                'groupId'          => (int) $group_id,
                'type'             => 'TEXTBOX',
			]
        );

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
			do_graphql_request( $mutation, 'createXProfileFieldTest', $variables )
		);
	}

	public function test_create_xprofile_field_user_not_logged_in() {
		$group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );

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
                'groupId'          => (int) $group_id,
                'type'             => 'TEXTBOX',
			]
        );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createXProfileFieldTest', $variables )
		);
    }
    
    public function test_create_xprofile_field_without_permission() {
		$u = $this->factory->user->create();
        $this->bp->set_current_user( $u );

		$group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );

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
                'groupId'          => (int) $group_id,
                'type'             => 'TEXTBOX',
			]
        );

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createXProfileFieldTest', $variables )
		);
    }

	public function test_delete_xprofile_field() {
        $u1 = $this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $u1 ] );

		$this->bp->set_current_user( $this->admin );

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

		$this->assertEquals(
			[
				'data' => [
					'deleteXProfileField' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'field'            => [
							'fieldId' => $field_id,
						],
					],
				],
			],
			do_graphql_request( $mutation, 'deleteXProfileFieldTest', $variables )
		);
	}

	public function test_delete_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

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
			'fieldId'          => 99999999,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteXProfileFieldTest', $variables )
		);
	}

	public function test_delete_xprofile_field_user_not_logged_in() {        
        $u1 = $this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $u1 ] );

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

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteXProfileFieldTest', $variables )
		);
    }

    public function test_delete_xprofile_group_user_without_permission() {
        $u1 = $this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );
        $field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $u1 ] );
        
		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        $this->bp->set_current_user( $u1 );

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

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteXProfileFieldTest', $variables )
		);
	}
}
