<?php

/**
 * Test_Group_Update_Mutation Class.
 *
 * @group groups
 */
class Test_Group_Update_Mutation extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $group_id;
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
		$this->group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $this->admin,
		) );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_update_group() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $u );

		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name'   => 'Updated Group',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->update_group( 'PUBLIC',$group_id, 'Updated Group' )
		);
	}

	public function test_update_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->update_group( 'PUBLIC', 99999999, 'Updated Group' );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This group does not exist.', $response['errors'][0]['message'] );
	}

	public function test_update_group_without_a_logged_in() {
		$response = $this->update_group( 'PUBLIC', $this->group_id, 'Updated Group' );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_update_group_without_permission() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp->set_current_user( $u );

		$response = $this->update_group( 'PUBLIC', $this->group_id, 'Updated Group' );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_update_group_moderators_can_update() {
		$u        = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$group_id = $this->bp_factory->group->create( array(
			'name'        => 'Group',
			'creator_id'  => $u,
		) );

		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'updateGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated Group',
							'status' => 'PUBLIC',
						],
					],
				],
			],
			$this->update_group( 'PUBLIC', $group_id, 'Updated Group' )
		);
	}

	public function test_update_group_invalid_status() {
		$u = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		$this->bp->set_current_user( $u );

		$response = $this->update_group( 'random-status' );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Variable "$status" got invalid value "random-status"; Expected type GroupStatusEnum.', $response['errors'][0]['message'] );
	}

	protected function update_group( $status = null, $group_id = null, $name = null ) {
		$mutation = '
			mutation updateGroupTest( $clientMutationId: String!, $name: String, $groupId: Int, $status:GroupStatusEnum ) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						groupId: $groupId
						name: $name
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
			'groupId'          => $group_id ?? $this->group_id,
			'status'           => $status ?? 'PUBLIC',
			'name'             => $name ?? 'Group',
		];

		return do_graphql_request( $mutation, 'updateGroupTest', $variables );
	}
}
