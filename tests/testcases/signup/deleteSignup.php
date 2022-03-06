<?php

/**
 * Test_Signup_deleteSignup_Mutation Class.
 *
 * @group signup
 */
class Test_Signup_deleteSignup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	}

	public function test_signup_delete() {
		$this->set_user();

		$a = $this->create_signup_id();

		$this->assertQuerySuccessful( $this->delete_signup( $a ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $a );
	}

	public function test_delete_signup_with_invalid_signup_id() {
		$this->set_user();

		$this->assertQueryFailed( $this->delete_signup( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This signup does not exist.' );
	}

	public function test_delete_signup_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_signup( $this->create_signup_id() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_signup_user_without_permission() {
		$a = $this->create_signup_id();

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_signup( $a ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete signup mutation.
	 *
	 * @param int|null $signup_id Signup ID.
	 * @return array
	 */
	protected function delete_signup( $signup_id = null ): array {
		$query = '
			mutation deleteSignupTest(
				$clientMutationId:String!
				$signupId:Int
			) {
				deleteSignup(
					input: {
						clientMutationId: $clientMutationId
						signupId: $signupId
					}
				)
				{
					clientMutationId
					deleted
					signup {
						id
						databaseId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'signupId'         => $signup_id,
		];

		$operation_name = 'deleteSignupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
