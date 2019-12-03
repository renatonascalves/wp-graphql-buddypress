<?php

/**
 * Test_Members_Queries Class.
 *
 * @group members
 */
class Test_Members_Queries extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create( [
			'role'       => 'administrator',
            'user_email' => 'admin@example.com',
            'user_login' => 'user',
		] );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_member_query() {

        $global_id = \GraphQLRelay\Relay::toGlobalId( 'user', $this->admin );

        
        $user    = get_user_by( 'id', $this->admin );
        $this->bp->set_current_user( $this->admin );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			user(id: \"{$global_id}\") {
				link
			}
        }";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'user' => [
                        'link' => bp_core_get_user_domain( $this->admin ),
					],
				],
			],
			do_graphql_request( $query )
		);
    }
}
