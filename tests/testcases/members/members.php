<?php

/**
 * Test_Member_membersQuery_Queries Class.
 *
 * @group members
 */
class Test_Member_membersQuery_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_members_query_with_support_for_the_community_visibility() {
		$this->toggle_component_visibility();

		$this->bp_factory->user->create();
		$this->bp_factory->user->create();

		// Query members.
		$this->assertQuerySuccessful( $this->membersQuery() )->notHasNodes();

		$this->toggle_component_visibility( false );

		// Query members.
		$this->assertQuerySuccessful( $this->membersQuery() )->HasNodes();
	}

	public function test_members_query() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();
		$u4 = $this->bp_factory->user->create();

		// Query members.
		$response = $this->membersQuery();

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['members']['nodes'], 'userId' );

		// Check our four members.
		$this->assertTrue( in_array( $u1, $ids, true ) );
		$this->assertTrue( in_array( $u2, $ids, true ) );
		$this->assertTrue( in_array( $u3, $ids, true ) );
		$this->assertTrue( in_array( $u4, $ids, true ) );
	}

	public function test_members_query_paginated() {
		$this->bp_factory->user->create_many( 4 );

		// Query members.
		$response = $this->membersQuery( [ 'first' => 2 ] );

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( $response['data']['members']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $response['data']['members']['pageInfo']['hasPreviousPage'] );
	}

	/**
	 * Member query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function membersQuery( array $variables = [] ): array {
		$query = 'query membersQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToMembersConnectionWhereArgs
		) {
			members(
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
						userId
					}
				}
				nodes {
					userId
				}
			}
		}';

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
