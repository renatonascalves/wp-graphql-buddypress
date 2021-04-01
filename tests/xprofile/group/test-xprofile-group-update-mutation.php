<?php

/**
 * Test_XProfile_Group_Update_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Update_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

	public $admin;
	public $xprofile_group_id;
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
		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create(
			[
				'name' => 'XProfile Group Name',
			]
		);
	}

	public function test_update_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

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
			$this->update_xprofile_group( $this->xprofile_group_id )
		);
	}

	public function test_update_xprofile_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->update_xprofile_group( 99999999 );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile group does not exist.', $response['errors'][0]['message'] );
	}

	public function test_update_xprofile_group_user_not_logged_in() {
		$response = $this->update_xprofile_group( $this->xprofile_group_id );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_update_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

		$response = $this->update_xprofile_group( $this->xprofile_group_id, 'Updated XProfile Group' );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @todo test group_order update
	 */
	public function test_update_xprofile_group_group_order() {
		return $this->assertTrue( true );
	}

	public function test_update_xprofile_group_with_no_input() {
		$mutation = '
			mutation updateXProfileGroupTest( $clientMutationId: String! ) {
				updateXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
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
		];

		$response = do_graphql_request( $mutation, 'updateXProfileGroupTest', $variables );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function update_xprofile_group( $group_id = 0, $name = null ) {
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
			'name'             => $name ?? 'Updated XProfile Group',
		];

		return do_graphql_request( $mutation, 'updateXProfileGroupTest', $variables );
	}
}
