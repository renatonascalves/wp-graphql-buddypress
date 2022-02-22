<?php

use WPGraphQL\Extensions\BuddyPress\Data\BlogHelper;
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
		if ( ! is_multisite() ) {
			$this->assertTrue( 1 === $signup->count_sent );
		}
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
		if ( ! is_multisite() ) {
			$this->assertTrue( 1 === $signup->count_sent );
		}
	}

	public function test_create_signup_with_invalid_password() {
		$this->assertQueryFailed( $this->create_signup( [ 'password' => '\\Antislash' ] ) )
			->expectedErrorMessage( 'Passwords cannot be empty or contain the "\" character.' );
	}

	public function test_create_signup_with_empty_user_login() {
		$error_message = 'Please enter a username';

		if ( is_multisite() ) {
			$error_message = 'Please enter a username.';
		}

		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => '' ] ) )
			->expectedErrorMessage( $error_message );
	}

	public function test_create_signup_with_illegal_login() {
		$error_message = 'That username is not allowed';

		if ( is_multisite() ) {
			$error_message = 'Usernames can only contain lowercase letters (a-z) and numbers.';
		}

		update_site_option( 'illegal_names', [ 'user_name' ] );

		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'user_name' ] ) )
			->expectedErrorMessage( $error_message );
	}

	public function test_create_signup_with_invalid_login() {
		$error_message = 'Sorry, usernames may not contain the character "_"!';

		if ( is_multisite() ) {
			$error_message = 'Usernames can only contain lowercase letters (a-z) and numbers.';
		}

		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'user_name' ] ) )
			->expectedErrorMessage( $error_message );
	}

	public function test_create_signup_with_short_login() {
		$error_message = 'Username must be at least 4 characters';

		if ( is_multisite() ) {
			$error_message = 'Username must be at least 4 characters.';
		}

		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => 'use' ] ) )
			->expectedErrorMessage( $error_message );
	}

	public function test_create_signup_with_numbers_only() {
		$this->assertQueryFailed( $this->create_signup( [ 'userLogin' => '123456' ] ) )
			->expectedErrorMessage( 'Sorry, usernames must have letters too!' );
	}

	public function test_create_signup_with_site() {
		$this->skipWithoutMultisite();

		update_site_option( 'registration', 'all' );

		$response = $this->create_signup_with_site();

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
							'blog'       => [
								'domain'   => $signup->domain,
								'uri'      => BlogHelper::get_blog_uri( $signup ),
								'path'     => $signup->path,
								'language' => strtoupper( $signup->meta['WPLANG'] ),
								'public'   => $signup->meta['public'],
								'name'     => $signup->title,
							],
						],
					],
				],
			],
			$response
		);

		delete_site_option( 'registration' );
	}

	public function test_create_signup_with_site_with_illegal_name() {
		$this->skipWithoutMultisite();

		update_site_option( 'registration', 'all' );

		$this->assertQueryFailed( $this->create_signup_with_site( [ 'siteName' => 'administrator' ] ) )
			->expectedErrorMessage( 'That name is not allowed.' );

		delete_site_option( 'registration' );
	}

	public function test_create_signup_with_site_with_blog_signup_disabled() {
		$this->skipWithoutMultisite();

		$this->assertQueryFailed( $this->create_signup_with_site() )
			->expectedErrorMessage( 'You are trying to create a blog but blog signup is disabled.' );
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

	/**
	 * Create signup with site mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_signup_with_site( array $args = [] ): array {
		$query = '
			mutation createSignupTest(
				$clientMutationId:String!
				$userLogin:String!
				$userEmail:String!
				$userName:String!
				$password:String!
				$siteName:String
				$siteTitle:String
				$sitePublic:Boolean
				$siteLanguage:SiteLanguagesEnum
			) {
				createSignup(
					input: {
						clientMutationId: $clientMutationId
						userLogin: $userLogin
						userEmail: $userEmail
						userName: $userName
						password: $password
						siteName: $siteName
						siteTitle: $siteTitle
						sitePublic: $sitePublic
						siteLanguage: $siteLanguage
					}
				)
				{
					clientMutationId
					signup {
						id
						databaseId
						blog {
							domain
							uri
							path
							language
							public
							name
						}
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
				'siteName'         => 'user' . wp_rand( 1, 20 ),
				'siteTitle'        => 'New Site',
				'sitePublic'       => true,
				'siteLanguage'     => 'EN_GB',
			]
		);

		$operation_name = 'createSignupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
