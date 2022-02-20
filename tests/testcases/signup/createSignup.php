<?php

use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;

/**
 * Test_Signup_createSignup_Mutation Class.
 *
 * @group signup
 */
class Test_Signup_createSignup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	}

	public function test_create_signup() {
		$response = $this->create_signup();

		$this->assertQuerySuccessful( $response );

		$signup = SignupHelper::get_signup( $response['data']['createSignup']['signup']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createSignup' => [
						'clientMutationId' => $this->client_mutation_id,
						'signup'           => [
							'id'         => $this->toRelayId( 'signup', (string) $signup->id ),
							'databaseId' => $signup->id,
						],
					],
				],
			],
			$response
		);

		// Email was sent.
		$this->assertTrue( 1 === $signup->count_sent );
	}

	public function test_create_signup_authenticated() {
		$this->set_user();

		$response = $this->create_signup();

		$this->assertQuerySuccessful( $response );

		$signup = SignupHelper::get_signup( $response['data']['createSignup']['signup']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createSignup' => [
						'clientMutationId' => $this->client_mutation_id,
						'signup' => [
							'id'               => $this->toRelayId( 'signup', (string) $signup->id ),
							'databaseId'       => $signup->id,
						],
					],
				],
			],
			$response
		);

		// Email was sent.
		$this->assertTrue( 1 === $signup->count_sent );
	}

	public function test_create_signup_with_invalid_password() {
		$this->assertQueryFailed( $this->create_signup( [ 'password' => '\\Antislash' ] ) )
			->expectedErrorMessage( 'Passwords cannot be empty or contain the "\" character.' );
	}

	public function test_create_signup_with_empty_user_login() {
		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => '' ] ) )
			->expectedErrorMessage( 'Please enter a username' );
	}

	public function test_create_signup_with_illegal_login() {
		update_site_option( 'illegal_names', [ 'user_name' ] );

		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'user_name' ] ) )
			->expectedErrorMessage( 'That username is not allowed' );
	}

	public function test_create_signup_with_invalid_login() {
		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'user_name' ] ) )
			->expectedErrorMessage( 'Sorry, usernames may not contain the character "_"!' );
	}

	public function test_create_signup_with_short_login() {
		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'use' ] ) )
			->expectedErrorMessage( 'Username must be at least 4 characters' );
	}

	public function test_create_signup_with_numbers_only() {
		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => '123456' ] ) )
			->expectedErrorMessage( 'Sorry, usernames must have letters too!' );
	}

	/**
	 * Create signup mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_signup( array $args = [] ): array {
		$query = '
			mutation createSignupTest(
				$clientMutationId:String!
				$userLogin:String!
				$userEmail:String!
				$userName:String!
				$password:String!
			) {
				createSignup(
					input: {

						clientMutationId: $clientMutationId
						userLogin: $userLogin
						userEmail: $userEmail
						userName: $userName
						password: $password
					}
				)
				{
					clientMutationId
					signup {
						id
						databaseId
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'userLogin'        => 'user' . wp_rand( 1, 20 ),
				'userEmail'        => sprintf( 'user%d@example.com', wp_rand( 1, 20 ) ),
				'userName'         => 'New User',
				'password'         => wp_generate_password( 12, false ),
			]
		);

		$operation_name = 'createSignupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
