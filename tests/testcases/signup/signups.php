<?php

/**
 * Test_Signup_signupQuery_Query Class.
 *
 * @group signup
 */
class Test_Signup_signupQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	}

	/**
	 * Set up.
	 */
	public function tearDown(): void {
		remove_filter( 'bp_get_signup_allowed', '__return_true' );

		parent::tearDown();
	}

	public function test_get_signups_authenticated() {
		$this->set_user();

		$a1 = $this->create_signup_id();
		$a2 = $this->create_signup_id();
		$a3 = $this->create_signup_id();

		$results = $this->signupQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['signups']['nodes'], 'databaseId' );

		// Check signups.
		$this->assertTrue( in_array( $a1, $ids, true ) );
		$this->assertTrue( in_array( $a2, $ids, true ) );
		$this->assertTrue( in_array( $a3, $ids, true ) );
	}

	public function test_signups_unauthenticated() {
		$this->create_signup_id();
		$this->create_signup_id();

		$this->assertQuerySuccessful( $this->signupQuery() )
			->notHasNodes();
	}

	public function test_get_signups_with_invalid_order_type() {
		$this->set_user();

		$this->assertQueryFailed( $this->signupQuery( [ 'where' => [ 'orderBy' => 'random-status' ] ] ) )
			->expectedErrorMessage( 'Variable "$where" got invalid value {"orderBy":"random-status"}; Expected type SignupOrderByEnum at value.orderBy.' );
	}

	public function test_get_signups_sorted() {
		$this->set_user();

		$a1 = $this->create_signup_id();
		$this->create_signup_id();
		$a3 = $this->create_signup_id();

		// ASC.
		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'order' => 'ASC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a1 );

		// DESC.
		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'order' => 'DESC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a3 );

		// Default: DESC.
		$this->assertQuerySuccessful( $this->signupQuery() )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a3 );
	}

	public function test_get_active_signups() {
		$this->set_user();

		$activation_key = wp_generate_password( 32, false );

		$a1 = $this->create_signup_id();
		$a2 = $this->create_signup_id( [ 'activation_key' => $activation_key ] );

		// Activate signup.
		bp_core_activate_signup( $activation_key );

		// Inactive.
		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'active' => 0 ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );

		// Inactive.
		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'active' => 1 ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a2 );
	}

	public function test_get_signups_with_activation_key() {
		$this->set_user();

		$activation_key = wp_generate_password( 32, false );
		$a1             = $this->create_signup_id( [ 'activation_key' => $activation_key ] );

		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'activationKey' => $activation_key ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_signups_with_user_login() {
		$this->set_user();

		$user_login = 'user' . wp_rand( 1, 20 );
		$a1         = $this->create_signup_id( [ 'user_login' => $user_login ] );

		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'userLogin' => $user_login ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_signups_with_user_email() {
		$this->set_user();

		$user_email = sprintf( 'user%d@example.com', wp_rand( 1, 20 ) );
		$a1         = $this->create_signup_id( [ 'user_email' => $user_email ] );

		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'userEmail' => $user_email ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_signups_with_search() {
		$this->set_user();

		$user_email = sprintf( 'user%d@example.com', wp_rand( 1, 20 ) );
		$a1         = $this->create_signup_id( [ 'user_email' => $user_email ] );

		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'search' => $user_email ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_signups_with_include() {
		$this->set_user();

		$a1 = $this->create_signup_id();

		$this->assertQuerySuccessful( $this->signupQuery( [ 'where' => [ 'include' => $a1 ] ] ) )
			->HasEdges()
			->notHasNextPage()
			->notHasPreviousPage()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_first_signup() {
		$this->set_user();

		$this->create_signup_id();
		$this->create_signup_id();
		$a1 = $this->create_signup_id();

		// The first here is the last one created. The latest signup.
		$this->assertQuerySuccessful(
			$this->signupQuery(
				[
					'first' => 1,
					'after' => '',
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a1 )
			->hasNextPage();
	}

	public function test_get_signups_after() {
		$this->set_user();

		$a1 = $this->create_signup_id();
		$a2 = $this->create_signup_id();

		$this->assertQuerySuccessful( $this->signupQuery( [ 'after' => $this->key_to_cursor( $a1 ) ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a2 )
			->hasPreviousPage();
	}

	public function test_get_signups_before() {
		$this->set_user();

		$this->create_signup_id();
		$a2 = $this->create_signup_id();
		$a3 = $this->create_signup_id();

		$this->assertQuerySuccessful(
			$this->signupQuery(
				[
					'last'   => 1,
					'before' => $this->key_to_cursor( $a3 ),
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a2 )
			->hasNextPage();
	}

	/**
	 * Signup query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function signupQuery( array $variables = [] ): array {
		$query = 'query signupQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToSignupConnectionWhereArgs
		) {
			signups(
				first:$first
				last:$last
				after:$after
				before:$before
				where:$where
			) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						id
						databaseId
					}
				}
				nodes {
					id
					databaseId
				}
			}
		}';

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
