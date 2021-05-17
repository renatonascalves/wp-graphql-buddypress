<?php

/**
 * Test_Blogs_blogsQuery_Queries Class.
 *
 * @group blogs
 */
class Test_Blogs_blogsQuery_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_blogs_query() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->admin );

		$b1 = $this->bp_factory->blog->create();
		$b2 = $this->bp_factory->blog->create();
		$this->bp_factory->blog->create();
		$this->bp_factory->blog->create();

		$results = $this->blogsQuery();

		$this->assertQuerySuccessful( $results )
			->hasEdges();

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'databaseId' );

		$this->assertContains( $b1, $blogs_ids );
		$this->assertContains( $b2, $blogs_ids );
	}

	public function test_get_first_blog() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->admin );

		$b1 = $this->bp_factory->blog->create();

		$this->assertQuerySuccessful( $this->blogsQuery( [
			'first' => 1,
			'after' => ''
		] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $b1 );
	}

	public function test_get_blog_after() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->admin );

		$b1 = $this->bp_factory->blog->create();
		$b2 = $this->bp_factory->blog->create();

		$this->assertQuerySuccessful( $this->blogsQuery( [ 'after' => $this->key_to_cursor( $b1 ) ] ) )
			->hasEdges()
			->firstEdgeNodeField( 'databaseId', $b2 )
			->hasPreviousPage();
	}

	public function test_get_blog_before() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->admin );

		$this->bp_factory->blog->create();
		$b2 = $this->bp_factory->blog->create();

		$this->assertQuerySuccessful( $this->blogsQuery( [
			'last'   => 1,
			'before' => $this->key_to_cursor( $b2 )
		] ) )
			->hasNextPage();
	}

	public function test_get_blogs_using_where_include() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->blog->create();
		$u2 = $this->bp_factory->blog->create();
		$u3 = $this->bp_factory->blog->create();
		$u4 = $this->bp_factory->blog->create();

		$results = $this->blogsQuery(
			[
				'where' => [
					'include' => [ $u1, $u2 ],
				]
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasEdges();

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'databaseId' );

		// Confirm total count.
		$this->assertTrue( count( $blogs_ids ) === 2 );

		// Confirm if included blog ids.
		$this->assertContains( $u1, $blogs_ids );
		$this->assertContains( $u2, $blogs_ids );

		// Confirm not included blog ids.
		$this->assertNotContains( $u3, $blogs_ids );
		$this->assertNotContains( $u4, $blogs_ids );
	}

	/**
	 * Blogs Query.
	 *
	 * @param array $variables Query variables.
	 * @return array
	 */
	protected function blogsQuery( array $variables = [] ): array {
		$query = 'query blogsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToBlogConnectionWhereArgs
		) {
			blogs(
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
						databaseId
					}
				}
				nodes {
					databaseId
				}
			}
		}';

		$operation_name = 'blogsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
