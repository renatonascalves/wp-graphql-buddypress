<?php

/**
 * Test_Groups_groupsQuery_Query Class.
 *
 * @group groups
 */
class Test_Groups_groupsQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_private_group_with_authenticated_user() {
		$this->bp->set_current_user( $this->admin );

		$private_group_id = $this->create_group_object( [ 'status' => 'private' ] );

		$results = $this->groupsQuery(
			[
				'where' => [
					'include' => [ $private_group_id ],
					'status'  => [ 'PRIVATE']
				]
			]
		);

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( $private_group_id, $results['data']['groups']['nodes'][0]['databaseId'] );
	}

	public function test_getting_private_group_with_unauthenticated_user() {
		$this->bp->set_current_user( $this->user );

		$results = $this->groupsQuery(
			[
				'where' => [
					'include' => [ $this->create_group_object( [ 'status' => 'private' ] ) ],
					'status'  => [ 'PRIVATE']
				]
			]
		);

		$this->assertQuerySuccessful( $results );
		$this->assertTrue( empty( $results['data']['groups']['edges'] ) );
	}

	public function test_groups_query_with_hidden_groups() {
		$this->bp->set_current_user( $this->admin );

		$hidden_group_id = $this->create_group_object( [ 'status' => 'hidden' ] );

		$results = $this->groupsQuery(
			[
				'where' => [
					'include' => [ $hidden_group_id ],
					'status'  => [ 'HIDDEN']
				]
			]
		);

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( $hidden_group_id, $results['data']['groups']['nodes'][0]['databaseId'] );
	}

	public function test_groups_query() {
		$u1 = $this->create_group_object();
		$u2 = $this->create_group_object();
		$u3 = $this->create_group_object();

		$this->bp->set_current_user( $this->admin );

		$results = $this->groupsQuery();

		$this->assertQuerySuccessful( $results );

		$ids = wp_list_pluck( $results['data']['groups']['nodes'], 'databaseId' );

		// Check groups.
		$this->assertTrue( in_array( $u1, $ids, true ) );
		$this->assertTrue( in_array( $u2, $ids, true ) );
		$this->assertTrue( in_array( $u3, $ids, true ) );
	}

	public function test_group_query_paginated() {
		$this->create_group_object();

		$results = $this->groupsQuery( [ 'first' => 1 ] );

		$this->assertQuerySuccessful( $results );
		$this->assertTrue( $results['data']['groups']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['groups']['pageInfo']['hasPreviousPage'] );
	}

	public function test_group_query_paginated_logged_in() {
		$this->bp->set_current_user( $this->admin );
		$this->create_group_object();
		$results = $this->groupsQuery( [ 'first' => 1 ] );

		$this->assertQuerySuccessful( $results );
		$this->assertTrue( $results['data']['groups']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['groups']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( $this->group, $results['data']['groups']['nodes'][0]['databaseId'] );
	}

	public function test_group_query_with_admins_unauthenticated_user() {
		$this->create_group_object();

		$results = $this->groupsQuery();

		$this->assertQuerySuccessful( $results );
		$this->assertTrue( empty( $results['data']['groups']['nodes'][0]['admins'] ) );
	}

	public function test_group_query_with_admins_authenticated_user() {
		$this->bp->set_current_user( $this->admin );

		$results = $this->groupsQuery();

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( $this->user, $results['data']['groups']['nodes'][0]['admins'][0]['userId'] );
	}

	public function test_group_query_with_group_types() {
		$this->bp->set_current_user( $this->admin );

		bp_groups_set_group_type( $this->group, 'foo' );

		$results = $this->groupsQuery();

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( [ 'FOO' ], $results['data']['groups']['nodes'][0]['types'] );
	}

	/**
	 * Groups query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function groupsQuery( array $variables = [] ): array {
		$query = 'query groupsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToGroupConnectionWhereArgs
		) {
			groups(
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
						name
					}
				}
				nodes {
					id
					databaseId
					admins {
						userId
					}
					types
				}
			}
		}';

		$operation_name = 'groupsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
