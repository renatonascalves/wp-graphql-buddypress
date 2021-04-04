<?php

/**
 * Test_Group_Members_Queries Class.
 *
 * @group group-membership
 */
class Test_Group_Members_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_group_by_query() {
		$group_id = $this->group;

		$u1 = $this->factory->user->create();
		$u2 = $this->factory->user->create();

		$this->populate_group_with_members( [ $u1, $u2 ], $group_id );

		$global_id = $this->toRelayId( 'group', $group_id );

		$this->bp->set_current_user( $this->admin );

		// Create the query string to pass to the $query.
		$query = "
			query {
				groupBy(id: \"{$global_id}\") {
					id,
					groupId
					members {
						nodes {
							userId
						}
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'               => $global_id,
						'groupId'          => $group_id,
						'members'          => [
							'nodes' => [
								0 => [
									'userId' => $u1
								],
								1 => [
									'userId' => $u2
								],
							]
						],
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
	}

	protected function forwardPagination( $cursor ) {
		$variables = [
			'first'  => 1,
			'after' => $cursor,
		];

		$results = $this->groupsQuery( $variables );

		$second_group = groups_get_groups(
			[
				'per_page' => 1,
				'page'     => 1,
			]
		);

		$second_group_id = $second_group['groups'][0]->id;
		$expected_cursor = \GraphQLRelay\Connection\ArrayConnection::offsetToCursor( $second_group_id );

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( 1, count( $results['data']['groups']['edges'] ) );
		$this->assertEquals( $second_group_id, $results['data']['groups']['edges'][0]['node']['groupId'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['edges'][0]['cursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['startCursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['endCursor'] );
	}

	protected function backwardPagination( $cursor ) {
		$results = $this->groupsQuery(
			[
				'last'   => 1,
				'before' => $cursor,
			]
		);

		$second_to_last_group = groups_get_groups(
			[
				'per_page' => 1,
				'page'     => 1,
				'order'    => 'ASC',
			]
		);

		$second_to_last_post_id = $second_to_last_group['groups'][0]->id;
		$expected_cursor        = \GraphQLRelay\Connection\ArrayConnection::offsetToCursor( $second_to_last_post_id );

		$this->assertQuerySuccessful( $results );
		$this->assertEquals( 1, count( $results['data']['groups']['edges'] ) );
		$this->assertEquals( $second_to_last_post_id, $results['data']['groups']['edges'][0]['node']['groupId'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['edges'][0]['cursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['startCursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['endCursor'] );
	}

	/**
	 * Add members to the group.
	 */
	protected function populate_group_with_members( $members, $group_id ) {
		foreach ( $members as $member_id ) {
			$this->bp->add_user_to_group( $member_id, $group_id );
		}
	}
}
