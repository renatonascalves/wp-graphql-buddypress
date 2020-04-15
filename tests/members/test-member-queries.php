<?php

/**
 * Test_Member_Queries Class.
 *
 * @group members
 */
class Test_Member_Queries extends WP_UnitTestCase {

	public static $admin;
	public static $user;
	public static $bp;
	public static $bp_factory;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp         = new BP_UnitTestCase();
		self::$bp_factory = new BP_UnitTest_Factory();
		self::$user       = self::factory()->user->create();
		self::$admin      = self::factory()->user->create( [ 'role' => 'administrator' ] );
	}

	public function test_member_query() {
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'user', self::$admin );

		// Register and set member types.
		bp_register_member_type( 'foo' );
		bp_set_member_type( self::$admin, 'foo' );

        self::$bp->set_current_user( self::$admin );

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
						'link'        => bp_core_get_user_domain( self::$admin ),
						'memberTypes' => [ 'foo' ],
						'mentionName' => bp_activity_get_user_mentionname( self::$admin ),
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_members_query() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		$u3 = self::$bp_factory->user->create();
		$u4 = self::$bp_factory->user->create();

		self::$bp->set_current_user( self::$admin );

		$results = $this->membersQuery(
			[
				'where' => [
					'include' => [ $u1, $u2, $u3, $u4 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$ids = wp_list_pluck(
			$results['data']['members']['nodes'],
			'userId'
		);

		// Check our four members.
		$this->assertTrue( count( $ids ) === 4 );
		$this->assertTrue( in_array( $u1, $ids, true ) );
	}

	public function test_members_query_paginated() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		$u3 = self::$bp_factory->user->create();
		$u4 = self::$bp_factory->user->create();

		self::$bp->set_current_user( self::$admin );

		// Here we're querying the members in our dataset.
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

		// Confirm there is a next page since we are fetching 2 members per page.
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
