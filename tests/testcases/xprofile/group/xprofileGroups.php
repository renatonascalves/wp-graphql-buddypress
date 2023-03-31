<?php

/**
 * Test_xprofileGroups_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_xprofileGroups_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_xprofile_groups_query() {
		$u1 = $this->bp_factory->xprofile_group->create();
		$u2 = $this->bp_factory->xprofile_group->create();
		$u3 = $this->bp_factory->xprofile_group->create();
		$u4 = $this->bp_factory->xprofile_group->create();

		$response = $this->xprofileGroupsQuery();

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['xprofileGroups']['nodes'], 'databaseId' );

		$this->assertContains( $u1, $ids );
		$this->assertContains( $u2, $ids );
		$this->assertContains( $u3, $ids );
		$this->assertContains( $u4, $ids );
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
						databaseId
						name
					}
				}
				nodes {
					id
					databaseId
				}
			}
		}';

		$operation_name = 'xprofileGroupsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
