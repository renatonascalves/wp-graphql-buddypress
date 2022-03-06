<?php

/**
 * Test_XProfile_deleteXProfileGroup_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_deleteXProfileGroup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create();
	}

	public function test_admins_can_delete_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_xprofile_group() )
			->hasField( 'deleted', true );
	}

	public function test_delete_xprofile_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_xprofile_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This XProfile group does not exist.' );
	}

	public function test_delete_xprofile_group_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_xprofile_group_with_user_without_permission() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete XProfile Group.
	 *
	 * @param int $xprofile_group_id XProfile Group ID.
	 * @return array
	 */
	protected function delete_xprofile_group( $xprofile_group_id = null ): array {
		$query = '
            mutation deleteXProfileGroupTest(
				$clientMutationId: String!
				$groupId: Int
			) {
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

		$operation_name = 'deleteXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

}
