<?php

/**
 * Test_Group_Members_Queries Class.
 *
 * @group group-membership
 */
class Test_Group_Members_Queries extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

	public static $admin;
	public static $bp;
	public static $bp_factory;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp         = new BP_UnitTestCase();
		self::$bp_factory = new BP_UnitTest_Factory();
		self::$admin      = self::factory()->user->create( [ 'role' => 'administrator' ] );
	}

	public function test_group_by_query() {
		$group_id = $this->create_group_object();

		$u1 = self::factory()->user->create();
		$u2 = self::factory()->user->create();

		$this->populate_group_with_members( [ $u1, $u2 ], $group_id );

		$global_id = $this->toRelayId( 'group', $group_id );

		self::$bp->set_current_user( self::$admin );

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
		}";

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
			do_graphql_request( $query )
		);
	}

	protected function groupsQuery( $variables ) {
		$query = 'query groupsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToGroupConnectionWhereArgs) {
			groups( first:$first last:$last after:$after before:$before where:$where ) {
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

		return do_graphql_request( $query, 'groupsQuery', $variables );
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

		$this->assertNotEmpty( $results );
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

		$this->assertNotEmpty( $results );
		$this->assertEquals( 1, count( $results['data']['groups']['edges'] ) );
		$this->assertEquals( $second_to_last_post_id, $results['data']['groups']['edges'][0]['node']['groupId'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['edges'][0]['cursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['startCursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['endCursor'] );
	}

	protected function create_group_object( $args = [] ) {
		$args = array_merge(
			[
				'slug'         => 'group-test',
				'name'         => 'Group Test',
				'description'  => 'Group Description',
				'creator_id'   => self::$admin,
			],
			$args
		);

		// Create group.
		return self::$bp_factory->group->create( $args );
	}

	/**
	 * Add members to the group.
	 */
	protected function populate_group_with_members( $members, $group_id ) {
		foreach ( $members as $member_id ) {
			self::$bp->add_user_to_group( $member_id, $group_id );
		}
	}
}
