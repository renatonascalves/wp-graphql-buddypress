<?php

/**
 * Test_XProfile_createXProfileGroup_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_createXProfileGroup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_admins_can_create_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->create_xprofile_group() )
			->hasField( 'name', 'XProfile Group Test' )
			->hasField( 'description', 'Description' )
			->hasField( 'canDelete', true );
	}

	public function test_create_xprofile_group_can_delete() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->create_xprofile_group( [ 'canDelete' => false ] ) )
			->hasField( 'canDelete', true );
	}

	public function test_create_xprofile_group_without_logged_in_user() {
		$this->assertQueryFailed( $this->create_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_xprofile_group_without_required_field() {
		$this->assertQueryFailed( $this->create_xprofile_group( [ 'name' => null ] ) )
			->expectedErrorMessage( 'Variable "$name" of non-null type "String!" must not be null.' );
	}

	/**
	 * Create XProfile Group.
	 *
	 * @param array $args Variables.
	 * @return array
	 */
	protected function create_xprofile_group( array $args = [] ): array {
		$query = '
			mutation createXProfileGroupTest(
				$clientMutationId:String!
				$name:String!
				$description:String
				$canDelete:Boolean
			) {
				createXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						description: $description
						canDelete: $canDelete
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

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Group Test',
				'description'      => 'Description',
				'canDelete'        => null,
			]
		);

		$operation_name = 'createXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
