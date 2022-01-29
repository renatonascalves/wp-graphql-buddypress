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

	public function test_get_private_groups_with_where_param_and_authenticated_user() {
		$this->bp->set_current_user( $this->admin );

		$private_group_id = $this->create_group_object( [ 'status' => 'private' ] );

		$this->assertQuerySuccessful( $this->groupsQuery(
			[
				'where' => [
					'include' => [ $private_group_id ],
					'status'  => [ 'PRIVATE']
				]
			]
		) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $private_group_id );
	}

	public function test_get_private_groups_with_where_param_and_unauthenticated_user() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->groupsQuery(
			[
				'where' => [
					'include' => [ $this->create_group_object( [ 'status' => 'private' ] ) ],
					'status'  => [ 'PRIVATE']
				]
			]
		) )
			->notHasEdges()
			->notHasNodes();
	}

	public function test_get_hidden_groups_with_where_param() {
		$this->bp->set_current_user( $this->admin );

		$hidden_group_id = $this->create_group_object( [ 'status' => 'hidden' ] );

		$this->assertQuerySuccessful( $this->groupsQuery(
			[
				'where' => [
					'include' => [ $hidden_group_id ],
					'status'  => [ 'HIDDEN']
				]
			]
		) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $hidden_group_id );
	}

	public function test_groups_query() {
		$g1 = $this->create_group_object();
		$g2 = $this->create_group_object();
		$g3 = $this->create_group_object();

		$this->bp->set_current_user( $this->admin );

		$results = $this->groupsQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['groups']['nodes'], 'databaseId' );

		// Check groups.
		$this->assertTrue( in_array( $g1, $ids, true ) );
		$this->assertTrue( in_array( $g2, $ids, true ) );
		$this->assertTrue( in_array( $g3, $ids, true ) );
	}

	public function test_get_first_group() {
		$this->bp->set_current_user( $this->admin );
		$this->create_group_object();

		$this->assertQuerySuccessful( $this->groupsQuery( [
			'first' => 1,
			'after' => ''
		] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $this->group )
			->hasNextPage();
	}

	public function test_get_group_after() {
		$this->bp->set_current_user( $this->admin );

		$g1 = $this->create_group_object();
		$g2 = $this->create_group_object();

		$this->assertQuerySuccessful( $this->groupsQuery( [ 'after' => $this->key_to_cursor( $g1 ) ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $g2 )
			->hasPreviousPage();
	}

	public function test_get_group_before() {
		$this->bp->set_current_user( $this->admin );

		$g1 = $this->create_group_object();
		$g2 = $this->create_group_object();
		$g3 = $this->create_group_object();

		$this->assertQuerySuccessful( $this->groupsQuery( [
			'last'   => 1,
			'before' => $this->key_to_cursor( $g3 )
		] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $g1 )
			->hasNextPage();
	}

	public function test_get_group_admins_with_unauthenticated_user() {
		$this->create_group_object();

		$this->assertQuerySuccessful( $this->groupsQuery() )
			->hasNodes()
			->firstNodesNodeField( 'admins', null );
	}

	public function test_get_group_admins_with_authenticated_user() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->groupsQuery() )
			->hasNodes()
			->firstNodesNodeField( 'admins', [ [ 'userId' => $this->user ] ] );
	}

	public function test_get_group_types() {
		$this->bp->set_current_user( $this->admin );

		bp_groups_set_group_type( $this->group, 'foo' );

		$this->assertQuerySuccessful( $this->groupsQuery() )
			->hasNodes()
			->firstNodesNodeField( 'types', [ 'FOO' ] );
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
