<?php

/**
 * Test_Signup_resendEmailSignup_Mutation Class.
 *
 * @group renato
 */
class Test_Signup_resendEmailSignup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	}

	public function test_signup_resend_email() {
		$this->set_user();

		$a = $this->create_signup_id();

		$this->assertQuerySuccessful( $this->resend_signup( $a ) )
			->hasField( 'sent', true );
	}

	public function test_resend_with_invalid_signup_id() {
		$this->assertQueryFailed( $this->resend_signup( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This signup does not exist.' );
	}

	public function test_resend_with_active_signup() {
		$this->skipWithMultisite();

		$a = $this->create_signup_id();

		$signup = new BP_Signup( $a );

		// Activate the signup.
		bp_core_activate_signup( $signup->activation_key );

		$this->assertQueryFailed( $this->resend_signup( $a ) )
			->expectedErrorMessage( 'Your account has already been activated.' );
	}

	/**
	 * Resend signup email mutation.
	 *
	 * @param int|null $signup_id Signup ID.
	 * @return array
	 */
	protected function resend_signup( $signup_id = null ): array {
		$query = '
			mutation resendSignupEmailTest(
				$clientMutationId:String!
				$databaseId:Int
			) {
				resendSignupEmail(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					sent
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $signup_id,
		];

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
