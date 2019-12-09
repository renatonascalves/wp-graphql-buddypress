<?php

/**
 * Test_XProfile_Group_Mutations Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Mutations extends WP_UnitTestCase {

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

	public function test_create_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation createXProfileGroupTest(
			$clientMutationId:String!,
			$name:String!
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
					canDelete
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Group Test',
				'description'      => 'Description',
			]
		);

		$this->assertEquals(
			[
				'data' => [
					'createXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name'        => 'XProfile Group Test',
							'description' => 'Description',
							'canDelete'   => true,
						],
					],
				],
			],
			do_graphql_request( $mutation, 'createXProfileGroupTest', $variables )
		);
	}

	public function test_create_xprofile_group_user_not_logged_in() {
		$mutation = '
		mutation createXProfileGroupTest(
			$clientMutationId:String!,
			$name:String!
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
					canDelete
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Group Test',
				'description'      => 'Description',
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createXProfileGroupTest', $variables )
		);
	}

	public function test_create_xprofile_group_without_permission() {
		$u = $this->factory->user->create();
		$this->bp->set_current_user( $u );

		$mutation = '
		mutation createXProfileGroupTest(
			$clientMutationId:String!,
			$name:String!
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
					canDelete
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Group Test',
				'description'      => 'Description',
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createXProfileGroupTest', $variables )
		);
	}

	public function test_delete_xprofile_group() {
		$u1 = $this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation deleteXProfileGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
              		groupId: $groupId
		    	}
          	)
          	{
            	clientMutationId
            	deleted
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $u1,
		];

		$this->assertEquals(
			[
				'data' => [
					'deleteXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'group'            => [
							'name' => 'Deleted Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'deleteXProfileGroupTest', $variables )
		);
	}

	public function test_delete_xprofile_group_invalid_group_id() {
		$this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation deleteXProfileGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
              		groupId: $groupId
		    	}
          	)
          	{
            	clientMutationId
            	deleted
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => 99999999,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteXProfileGroupTest', $variables )
		);
	}

	public function test_delete_xprofile_group_user_not_logged_in() {
		$u1 = $this->bp_factory->xprofile_group->create( [ 'name' => 'Deleted Group' ] );

		$mutation = '
		mutation deleteXProfileGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
              		groupId: $groupId
		    	}
          	)
          	{
            	clientMutationId
            	deleted
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $u1,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_delete_xprofile_group_user_without_permission() {
		$g1 = $this->bp_factory->xprofile_group->create();
		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp->set_current_user( $u1 );

		$mutation = '
		mutation deleteXProfileGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
              		groupId: $groupId
		    	}
          	)
          	{
            	clientMutationId
            	deleted
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $g1,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_update_xprofile_group() {
		$group_id = $this->bp_factory->xprofile_group->create( [
			'name' => 'XProfile Group Name',
		] );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation updateXProfileGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
					groupId: $groupId
					name: $name
		    	}
          	)
          	{
            	clientMutationId
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id,
			'name'             => 'Updated XProfile Group',
		];

		/**
		 * Compare the actual output vs the expected output
		 */
		$this->assertEquals(
			[
				'data' => [
					'updateXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated XProfile Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'updateXProfileGroupTest', $variables )
		);
	}

	public function test_update_xprofile_group_invalid_group_id() {
		$this->bp_factory->xprofile_group->create( [
			'name' => 'XProfile Group Name',
		] );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation updateXProfileGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
					groupId: $groupId
					name: $name
		    	}
          	)
          	{
            	clientMutationId
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => 99999999,
			'name'             => 'Updated XProfile Group',
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'updateXProfileGroupTest', $variables )
		);
	}

	public function test_update_xprofile_group_user_not_logged_in() {
		$group_id = $this->bp_factory->xprofile_group->create( [
			'name' => 'XProfile Group Name',
		] );

		$mutation = '
		mutation updateXProfileGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
					groupId: $groupId
					name: $name
		    	}
          	)
          	{
            	clientMutationId
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id,
			'name'             => 'Updated Group',
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'updateXProfileGroupTest', $variables )
		);
	}

	public function test_update_xprofile_group_without_permission() {
		$u1 = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$group_id = $this->bp_factory->xprofile_group->create( [
			'name' => 'XProfile Group Name',
		] );

		$this->bp->set_current_user( $u1 );

		$mutation = '
		mutation updateXProfileGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateXProfileGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
					groupId: $groupId
					name: $name
		    	}
          	)
          	{
            	clientMutationId
            	group {
					name
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id,
			'name'             => 'Updated XProfile Group',
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}
}
