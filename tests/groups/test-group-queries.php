<?php

/**
 * Test_Groups_Queries Class.
 *
 * @group groups
 */
class Test_Groups_Queries extends WP_UnitTestCase {

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
		] );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_group_query() {

		$group_id  = $this->create_group_object();
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'group', $group_id );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(id: \"{$global_id}\") {
				id,
				groupId
				name
				status
				description(format: RAW)
				totalMemberCount
				lastActivity
				hasForum
				link
				creator {
					userId
				}
				mods {
					userId
				}
				admins {
					userId
				}
				parent {
					groupId
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
						'name'             => 'Group Test',
						'status'           => 'PUBLIC',
						'totalMemberCount' => null,
						'description'      => 'Group Description',
						'lastActivity'     => null,
						'hasForum'         => 1,
						'link'             => bp_get_group_permalink( new \BP_Groups_Group( $group_id ) ),
						'creator'          => [
							'userId' => $this->admin,
						],
						'mods'             => null,
						'admins'           => [
							0 => [
								'userId' => $this->admin,
							],
						],
						'parent'           => null,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_id_param() {

		$group_id  = $this->create_group_object();
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'group', $group_id );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(id: \"{$global_id}\") {
				id,
				groupId
				name
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'      => $global_id,
						'groupId' => $group_id,
						'name'    => 'Group Test',
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_groupid_param() {

		$group_id = $this->create_group_object();
		$query    = "
		query {
			groupBy(groupId: {$group_id}) {
				groupId
				name
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'groupId' => $group_id,
						'name'    => 'Group Test',
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_slug_param() {

		$slug     = 'group-test';
		$group_id = $this->create_group_object();

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(slug: \"{$slug}\") {
				groupId
				slug
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'groupId' => $group_id,
						'slug'    => $slug,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_first_group_in_a_group_connection_query() {

		$this->create_group_object();

		/**
		 * Here we're querying the first group in our dataset.
		 */
		$results = $this->groupsQuery(
			[
				'first' => 1,
			]
		);

		/**
		 * Let's query the first group in our data set so we can test against it.
		 */
		$first_group = groups_get_groups(
			[
				'per_page' => 1,
			]
		);

		$first_group_id   = $first_group['groups'][0]->id;
		$expected_cursor = \GraphQLRelay\Connection\ArrayConnection::offsetToCursor( $first_group_id );

		$this->assertNotEmpty( $results );
		$this->assertEquals( 1, count( $results['data']['groups']['edges'] ) );
		$this->assertEquals( $first_group_id, $results['data']['groups']['edges'][0]['node']['groupId'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['edges'][0]['cursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['startCursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['endCursor'] );
		$this->assertEquals( $first_group_id, $results['data']['groups']['nodes'][0]['groupId'] );

		$this->forwardPagination( $expected_cursor );
	}

	public function test_last_group_in_a_group_connection_query() {
		$this->create_group_object();
		$this->create_group_object();

		/**
		 * Here we're trying to query the last post in our dataset
		 */
		$results = $this->groupsQuery(
			[
				'last' => 1
			]
		);

		/**
		 * Let's query the last group in our data set so we can test against it.
		 */
		$last_group = groups_get_groups(
			[
				'per_page' => 1,
				'order'    => 'ASC',
			]
		);

		$last_group_id   = $last_group['groups'][0]->id;
		$expected_cursor = \GraphQLRelay\Connection\ArrayConnection::offsetToCursor( $last_group_id );

		$this->assertNotEmpty( $results );
		$this->assertEquals( 1, count( $results['data']['groups']['edges'] ) );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['edges'][0]['cursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['startCursor'] );
		$this->assertEquals( $expected_cursor, $results['data']['groups']['pageInfo']['endCursor'] );

		$this->backwardPagination( $expected_cursor );
	}

	public function test_get_group_with_parent_group() {

		$parent_id = $this->create_group_object();
		$child_id  = $this->bp_factory->group->create( [
			'parent_id' => $parent_id,
		] );

		$global_id       = \GraphQLRelay\Relay::toGlobalId( 'group', $parent_id );
		$global_child_id = \GraphQLRelay\Relay::toGlobalId( 'group', $child_id );

		$query = "{
			groupBy(id: \"{$global_child_id}\") {
				id
				groupId
				parent {
					id
					groupId
				}
			}
		}";

		$actual = do_graphql_request( $query );

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $actual );

		$parent = $actual['data']['groupBy']['parent'];
		$child  = $actual['data']['groupBy'];

		/**
		 * Make sure the child and parent data matches what we expect
		 */
		$this->assertEquals( $global_id, $parent['id'] );
		$this->assertEquals( $parent_id, $parent['groupId'] );
		$this->assertEquals( $global_child_id, $child['id'] );
		$this->assertEquals( $child_id, $child['groupId'] );
	}

	public function test_private_group_with_access() {

		$this->bp->set_current_user( $this->admin );

		$private_group_id = $this->create_group_object(
			[
				'status' => 'private'
			]
		);

		/**
		 * Here we're querying the groups in our dataset.
		 */
		$results = $this->groupsQuery(
			[
				'where' => [
					'include' => [ $private_group_id ],
					'status'  => [ 'PRIVATE']
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $private_group_id, $results['data']['groups']['nodes'][0]['groupId'] );
	}

	public function test_private_group_without_access() {
		$u = $this->factory->user->create();
		$this->bp->set_current_user( $u );

		$private_group_id = $this->create_group_object(
			[
				'status' => 'private'
			]
		);

		// Returns an error as the user has no access to the private group.
		$this->assertArrayHasKey(
			'errors',
			$this->groupsQuery(
				[
					'where' => [
						'include' => [ $private_group_id ],
						'status'  => [ 'PRIVATE']
					]
				]
			)
		);
	}

	public function test_hidden_group_without_access() {
		$u = $this->factory->user->create();
		$this->bp->set_current_user( $u );

		$hidden_group_id = $this->create_group_object(
			[
				'status' => 'hidden'
			]
		);

		// Returns an error as the user has no access to the private group.
		$this->assertArrayHasKey(
			'errors',
			$this->groupsQuery(
				[
					'where' => [
						'include' => [ $hidden_group_id ],
						'status'  => [ 'HIDDEN']
					]
				]
			)
		);
	}

	public function test_groups_query_with_hidden_groups() {
		$this->bp->set_current_user( $this->admin );

		$hidden_group_id = $this->create_group_object(
			[
				'status' => 'hidden'
			]
		);

		/**
		 * Here we're querying the groups in our dataset.
		 */
		$results = $this->groupsQuery(
			[
				'where' => [
					'include' => [ $hidden_group_id ],
					'status'  => [ 'HIDDEN']
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $hidden_group_id, $results['data']['groups']['nodes'][0]['groupId'] );
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
				'creator_id'   => $this->admin,
			],
			$args
		);

		// Create group.
		return $this->bp_factory->group->create( $args );
	}
}
