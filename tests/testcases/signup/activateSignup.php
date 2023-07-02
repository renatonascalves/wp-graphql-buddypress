<?php

/**
 * Test_Signup_activateSignup_Mutation Class.
 *
 * @group signup
 */
class Test_Signup_activateSignup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	}

	public function test_activate_signup() {
		$activation_key = wp_generate_password( 32, false );

		$a = $this->create_signup_id( [ 'activation_key' => $activation_key ] );

		$this->assertQuerySuccessful( $this->activate_signup( $activation_key ) )
			->hasField( 'databaseId', $a )
			->hasField( 'active', true );
	}

	public function test_activate_signup_authenticated() {
		$this->set_user();

		$activation_key = wp_generate_password( 32, false );

		$a = $this->create_signup_id( [ 'activation_key' => $activation_key ] );

		$this->assertQuerySuccessful( $this->activate_signup( $activation_key ) )
			->hasField( 'databaseId', $a )
			->hasField( 'active', true );
	}

	public function test_activate_signup_with_invalid_key() {
		$this->assertQueryFailed( $this->activate_signup( 'fake' ) )
			->expectedErrorMessage( 'Invalid activation key.' );
	}

	public function test_activate_signup_already_active() {
		$activation_key = wp_generate_password( 32, false );

		$a = $this->create_signup_id( [ 'activation_key' => $activation_key ] );

		// Activate the signup.
		BP_Signup::activate( [ $a ] );

		$this->assertQueryFailed( $this->activate_signup( $activation_key ) )
			->expectedErrorMessage( 'The user is already active.' );
	}

	/**
	 * Activate signup mutation.
	 *
	 * @param string|null $key Activation key.
	 * @return array
	 */
	protected function activate_signup( $key = null ): array {
		$query = '
			mutation activateSignupTest(
				$clientMutationId:String!
				$activationKey:String!
			) {
				activateSignup(
					input: {
						clientMutationId: $clientMutationId
						activationKey: $activationKey
					}
				)
				{
					clientMutationId
					signup {
						id
						databaseId
						active
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'activationKey'    => $key,
		];

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
