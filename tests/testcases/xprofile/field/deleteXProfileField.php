<?php

/**
 * Test_XProfile_deleteXProfileField_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_deleteXProfileField_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * XProfile Field ID.
	 *
	 * @var int
	 */
	public $xprofile_field_id;

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
		$this->xprofile_field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
	}

	public function test_admins_can_delete_xprofile_field() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_xprofile_field() )
			->hasField( 'databaseId', $this->xprofile_field_id )
			->hasField( 'deleted', true );
	}

	public function test_delete_with_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_xprofile_field( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_delete_xprofile_field_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_xprofile_field_user_without_permission() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->delete_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete XProfile Field.
	 *
	 * @param int|null $xprofile_field_id Field ID.
	 * @return array
	 */
	protected function delete_xprofile_field( $xprofile_field_id = null ): array {
		$query = '
			mutation deleteXProfileFieldTest(
				$clientMutationId: String!
				$databaseId: Int
			) {
				deleteXProfileField(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					deleted
					field {
						databaseId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $xprofile_field_id ?? $this->xprofile_field_id,
		];

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
