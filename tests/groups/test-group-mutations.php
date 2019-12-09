<?php

/**
 * Test_Groups_Mutations Class.
 *
 * @group groups
 */
class Test_Groups_Mutations extends WP_UnitTestCase {

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

	public function test_create_group() {
		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation createGroupTest(
			$clientMutationId:String!,
			$name:String!
			$slug:String
		) {
			createGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					slug: $slug
				}
			)
          	{
				clientMutationId
		    	group {
					name
					slug
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
			]
		);

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name' => 'Group Test',
							'slug' => 'group-slug',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'createGroupTest', $variables )
		);
	}

	public function test_create_group_user_not_logged_in() {
		$mutation = '
		mutation createGroupTest(
			$clientMutationId:String!,
			$name:String
			$slug:String
		) {
			createGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					slug: $slug
				}
			)
          	{
				clientMutationId
		    	group {
					name
					slug
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createGroupTest', $variables )
		);
	}

	public function test_create_group_as_regular_user() {
		$u = $this->factory->user->create();
		$this->bp->set_current_user( $u );

		$mutation = '
		mutation createGroupTest(
			$clientMutationId:String!,
			$name:String!
			$slug:String
		) {
			createGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					slug: $slug
				}
			)
          	{
				clientMutationId
		    	group {
					name
					slug
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
			]
		);

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'name' => 'Group Test',
							'slug' => 'group-slug',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'createGroupTest', $variables )
		);
	}

	public function test_create_group_with_valid_status() {
		$u = $this->factory->user->create();
		$this->bp->set_current_user( $u );

		$mutation = '
		mutation createGroupTest(
			$clientMutationId:String!,
			$name:String!
			$slug:String
			$status:GroupStatusEnum
		) {
			createGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					slug: $slug
					status: $status
				}
			)
          	{
				clientMutationId
		    	group {
					name
					slug
					status
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
				'status'           => 'PUBLIC',
			]
		);

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
			do_graphql_request( $mutation, 'createGroupTest', $variables )
		);
	}

	public function test_create_group_with_invalid_status() {
		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation createGroupTest(
			$clientMutationId:String!,
			$name:String
			$slug:String
			$status:GroupStatusEnum
		) {
			createGroup(
				input: {
					clientMutationId: $clientMutationId
					name: $name
					slug: $slug
					status: $status
				}
			)
          	{
				clientMutationId
		    	group {
					name
					slug
					status
		    	}
          	}
        }
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
				'status'           => 'random-status',
			]
		);

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'createGroupTest', $variables )
		);
	}

	public function test_delete_group() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Deleted Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteGroup(
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
			'groupId'          => $group_id,
		];

		$this->assertEquals(
			[
				'data' => [
					'deleteGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'group'            => [
							'name' => 'Deleted Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_delete_group_invalid_group_id() {
		$u = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteGroup(
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
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_delete_group_user_not_logged_in() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$mutation = '
		mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteGroup(
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
			'groupId'          => $group_id,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_delete_group_user_without_permission() {
		$u         = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$u1        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id  = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u1 );

		$mutation = '
		mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteGroup(
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
			'groupId'          => $group_id,
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_delete_group_moderators_can_delete() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Deleted Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
			deleteGroup(
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
			'groupId'          => $group_id,
		];

		$this->assertEquals(
			[
				'data' => [
					'deleteGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'group'            => [
							'name' => 'Deleted Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'deleteGroupTest', $variables )
		);
	}

	public function test_update_group() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateGroup(
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

		/**
		 * Compare the actual output vs the expected output
		 */
		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}

	public function test_update_group_invalid_group_id() {
		$u = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateGroup(
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
			'name'             => 'Updated Group',
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}

	public function test_update_group_user_not_logged_in() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateGroup(
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
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}

	public function test_update_group_without_permission() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$u1        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u1 );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateGroup(
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
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}

	public function test_update_group_moderators_can_update() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $this->admin );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
			updateGroup(
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

		/**
		 * Compare the actual output vs the expected output
		 */
		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated Group',
						],
					],
				],
			],
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}

	public function test_update_group_invalid_status() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation updateGroupTest( $clientMutationId: String!, $groupId: Int, $status:GroupStatusEnum ) {
			updateGroup(
		    	input: {
		      		clientMutationId: $clientMutationId
					groupId: $groupId
					status: $status
		    	}
          	)
          	{
            	clientMutationId
            	group {
					name
					status
            	}
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id,
			'status'           => 'random-status',
		];

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $mutation, 'updateGroupTest', $variables )
		);
	}
}
