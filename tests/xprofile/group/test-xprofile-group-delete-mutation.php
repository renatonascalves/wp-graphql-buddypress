<?php

/**
 * Test_XProfile_Group_Delete_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Delete_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $client_mutation_id;
    public $xprofile_group_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
        $this->xprofile_group_id  = $this->bp_factory->xprofile_group->create();
		$this->admin              = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
	}

	public function test_delete_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$mutation = $this->delete_xprofile_group();

		$this->assertEquals(
			[
				'data' => [
					'deleteXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
					],
				],
			],
			do_graphql_request( $mutation[0], 'deleteXProfileGroupTest', $mutation[1] )
		);
	}

	public function test_delete_xprofile_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$mutation = $this->delete_xprofile_group( 99999999 );
        $response = do_graphql_request( $mutation[0], 'deleteXProfileGroupTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile group does not exist.', $response['errors'][0]['message'] );
	}

	public function test_delete_xprofile_group_user_not_logged_in() {
        $mutation = $this->delete_xprofile_group();
        $response = do_graphql_request( $mutation[0], 'deleteXProfileGroupTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	public function test_delete_xprofile_group_user_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

        $mutation = $this->delete_xprofile_group();
        $response = do_graphql_request( $mutation[0], 'deleteXProfileGroupTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function delete_xprofile_group( $xprofile_group_id = null ): array {
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
                }
            }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $xprofile_group_id ?? $this->xprofile_group_id,
		];

		return [ $mutation, $variables ];
	}
}
