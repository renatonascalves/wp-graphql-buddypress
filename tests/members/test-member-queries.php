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

		// Register and set member types.
		bp_register_member_type( 'foo' );
		bp_set_member_type( $this->admin, 'foo' );

        $this->bp->set_current_user( $this->admin );

		// Create the query.
		$query = "
			query {
				user(id: \"{$global_id}\") {
					link
					memberTypes
					mentionName
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'user' => [
						'link'        => bp_core_get_user_domain( $this->admin ),
						'memberTypes' => [
							'foo',
						],
						'mentionName' => bp_activity_get_user_mentionname( $this->admin ),
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_members_query() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		$this->bp->set_current_user( $this->admin );

		$results = $this->membersQuery(
			[
				'where' => [
					'include' => [ $u1, $u2, $u3, $u4 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );
	}

	public function test_members_query_paginated() {
		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();
		$u3 = $this->factory->user->create();
		$u4 = $this->factory->user->create();

		$this->bp->set_current_user( $this->admin );

		// Here we're querying the groups in our dataset.
		$results = $this->membersQuery(
			[
				'first' => 2,
				'where' => [
					'include' => [ $u1, $u2, $u3, $u4 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( 1, $results['data']['members']['pageInfo']['hasNextPage'] );
	}

	protected function membersQuery( $variables ) {
		$query = 'query membersQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToMembersConnectionWhereArgs) {
			members( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						userId
					}
				}
				nodes {
				  userId
				}
			}
		}';

		return do_graphql_request( $query, 'membersQuery', $variables );
	}
}
