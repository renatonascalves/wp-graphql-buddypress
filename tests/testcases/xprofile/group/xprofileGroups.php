<?php

/**
 * Test_xprofileGroups_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_xprofileGroups_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_xprofile_groups_query() {
		$u1 = $this->bp_factory->xprofile_group->create();
		$u2 = $this->bp_factory->xprofile_group->create();
		$u3 = $this->bp_factory->xprofile_group->create();
		$u4 = $this->bp_factory->xprofile_group->create();

		$response = $this->xprofileGroupsQuery();

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['xprofileGroups']['nodes'], 'groupId' );

		// Check our four XProfile groups.
		$this->assertTrue( in_array( $u1, $ids, true ) );
		$this->assertTrue( in_array( $u2, $ids, true ) );
		$this->assertTrue( in_array( $u3, $ids, true ) );
		$this->assertTrue( in_array( $u4, $ids, true ) );
	}

	public function test_xprofile_groups_query_paginated() {
		$this->bp_factory->xprofile_group->create_many( 4 );

		// Query groups.
		$response = $this->xprofileGroupsQuery( [ 'first' => 2 ] );

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( $response['data']['xprofileGroups']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $response['data']['xprofileGroups']['pageInfo']['hasPreviousPage'] );
	}

	/**
	 * XProfile Groups query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function xprofileGroupsQuery( array $variables = [] ): array {
		$query = 'query xprofileGroupsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToXProfileGroupConnectionWhereArgs
		) {
			xprofileGroups(
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
						groupId
						name
					}
				}
				nodes {
					id
					groupId
				}
			}
		}';

		$operation_name = 'xprofileGroupsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
