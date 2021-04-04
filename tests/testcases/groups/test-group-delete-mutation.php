<?php

/**
 * Test_Group_Delete_Mutation Class.
 *
 * @group groups
 */
class Test_Group_Delete_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->group_id = $this->bp_factory->group->create(
			[
				'name'        => 'Deleted Group',
				'creator_id'  => $this->admin,
			]
		);
	}

	public function test_delete_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_group() )
			->hasField( 'deleted', true )
			->hasField( 'name', 'Deleted Group' )
			->notHasField( 'link' );
	}

	public function test_delete_group_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_delete_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_user_without_permission() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_moderators_can_delete() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_group() )
			->hasField( 'deleted', true );
	}

	protected function delete_group( $group_id = null ) {
		$query = '
			mutation deleteGroupTest($clientMutationId: String!, $groupId: Int) {
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
			'groupId'          => $group_id ?? $this->group_id,
		];

		$operation_name = 'deleteGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
