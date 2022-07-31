<?php

use WPGraphQL\Utils\Utils;

/**
 * Test_Signup_signup_Queries Class.
 *
 * @group signup
 */
class Test_Signup_signup_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Signup int.
	 *
	 * @var int
	 */
	public $signup_id;

	/**
	 * Signup allowed.
	 *
	 * @var bool
	 */
	protected $signup_allowed;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );

		$this->signup_id = $this->create_signup_id();
		$this->global_id = $this->toRelayId( 'signup', (string) $this->signup_id );
	}

	public function test_get_signup() {
		$signup_id = $this->create_signup_id();

		$this->set_user();

		$signup = $this->bp_factory->signup->get_object_by_id( $signup_id );

		$this->assertQuerySuccessful( $this->get_signup( $signup_id ) )
			->hasField( 'databaseId', $signup->id )
			->hasField( 'userName', $signup->user_name )
			->hasField( 'userLogin', $signup->user_login )
			->hasField( 'userEmail', $signup->user_email )
			->hasField( 'countSent', $signup->count_sent )
			->hasField( 'registered', Utils::prepare_date_response( $signup->registered ) )
			->hasField( 'registeredGmt', Utils::prepare_date_response( $signup->registered ), get_date_from_gmt( $signup->registered ) )
			->hasField( 'dateSent', Utils::prepare_date_response( $signup->date_sent ) )
			->hasField( 'dateSentGmt', Utils::prepare_date_response( $signup->date_sent ), get_date_from_gmt( $signup->date_sent ) );
	}

	public function test_get_signup_with_invalid_id() {
		$this->set_user();

		$this->assertQueryFailed( $this->get_signup( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This signup does not exist.' );
	}

	public function test_get_signup_unauthenticated() {
		$response = $this->get_signup( $this->signup_id );

		$this->assertEmpty( $response['data']['signup'] );
	}

	public function test_get_signup_unauthorized() {
		$this->bp->set_current_user( $this->user );

		$response = $this->get_signup( $this->signup_id );

		$this->assertEmpty( $response['data']['signup'] );
	}

	/**
	 * Get a signup.
	 *
	 * @param int    $signup_id Signup ID.
	 * @param string $type      Type.
	 * @return array
	 */
	protected function get_signup( int $signup_id, $type = 'DATABASE_ID' ): array {
		$query = "
			query {
				signup(id: {$signup_id}, idType: {$type}) {
					id,
					databaseId
					userName
					userLogin
					userEmail
					countSent
					registered
					registeredGmt
					dateSent
					dateSentGmt
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
