<?php

/**
 * Test_Blogs_Queries Class.
 *
 * @group blogs
 */
class Test_Blogs_Queries extends WP_UnitTestCase {

	public $bp_factory;
	public $bp;
	public $admin;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_blog_query() {

		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		$u = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u );

		$b1 = $this->bp_factory->blog->create();

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'blog', $b1 );

		$query = "{
			blogBy( id: \"{$global_id}\" ) {
				id
				blogId
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'blogBy' => [
						'id' => $global_id,
						'blogId'      => $b1,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_blogs_query() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		$u1 = $this->factory->blog->create();
		$u2 = $this->factory->blog->create();
		$u3 = $this->factory->blog->create();
		$u4 = $this->factory->blog->create();

		$this->bp->set_current_user( $this->admin );

		$results = $this->blogsQuery();

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		// Confirm total count.
		$this->assertTrue( count( $results['data']['blogs']['nodes'] ) === 4 );
	}

	public function test_blogs_query_include() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		$u1 = $this->factory->blog->create();
		$u2 = $this->factory->blog->create();
		$u3 = $this->factory->blog->create();
		$u4 = $this->factory->blog->create();

		$this->bp->set_current_user( $this->admin );

		$results = $this->blogsQuery(
			[
				'where' => [
					'include' => [ $u1, $u2 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		// Confirm total count.
		$this->assertTrue( count( $results['data']['blogs']['nodes'] ) === 2 );
	}

	public function test_blogs_query_paginated() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		$u1 = $this->factory->blog->create();
		$u2 = $this->factory->blog->create();
		$u3 = $this->factory->blog->create();
		$u4 = $this->factory->blog->create();

		$this->bp->set_current_user( $this->admin );

		/**
		 * Here we're querying the blogs in our dataset.
		 */
		$results = $this->blogsQuery(
			[
				'first' => 2,
				'where' => [
					'include' => [ $u1, $u2, $u3, $u4 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( 1, $results['data']['blogs']['pageInfo']['hasNextPage'] );
	}

	protected function blogsQuery( $variables = '' ) {
		$query = 'query blogsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToBlogConnectionWhereArgs) {
			blogs( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						blogId
					}
				}
				nodes {
				  blogId
				}
			}
		}';

		return do_graphql_request( $query, 'blogsQuery', $variables );
	}
}
