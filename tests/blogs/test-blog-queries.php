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

	public function test_blog_query_with_body_by() {
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
				blogAdmin {
					userId
				}
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'blogBy' => [
						'id'        => $global_id,
						'blogId'    => $b1,
						'blogAdmin' => [
							'userId' => $u,
						],
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_blog_query_with_body_by_non_logged_in_user() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		$u1 = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u1 );
		$b1 = $this->bp_factory->blog->create();

		$u2 = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u2 );

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'blog', $b1 );

		$query = "{
			blogBy( id: \"{$global_id}\" ) {
				id
				blogId
				blogAdmin {
					userId
				}
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'blogBy' => [
						'id'        => $global_id,
						'blogId'    => $b1,
						'blogAdmin' => [
							'userId' => null,
						],
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

		$this->bp->set_current_user( $this->admin );

		$b1 = $this->factory->blog->create();
		$b2 = $this->factory->blog->create();
		$this->factory->blog->create();
		$this->factory->blog->create();

		$results = $this->blogsQuery();

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertTrue( ! empty( $results['data'] ) );

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'blogId' );

		$this->assertTrue( count( $blogs_ids ) === 4 );
		$this->assertContains( $b1, $blogs_ids );
		$this->assertContains( $b2, $blogs_ids );
	}

	public function test_blogs_query_using_where_include() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}
		
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->factory->blog->create();
		$u2 = $this->factory->blog->create();
		$u3 = $this->factory->blog->create();
		$u4 = $this->factory->blog->create();

		$results = $this->blogsQuery(
			[
				'where' => [
					'include' => [ $u1, $u2 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'blogId' );
		
		// Confirm total count.
		$this->assertTrue( count( $blogs_ids ) === 2 );

		// Confirm if included blog ids.
		$this->assertContains( $u1, $blogs_ids );
		$this->assertContains( $u2, $blogs_ids );

		// Confirm not included blog ids.
		$this->assertNotContains( $u3, $blogs_ids );
		$this->assertNotContains( $u4, $blogs_ids );
	}

	public function test_blogs_query_paginated() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}
		
		$this->bp->set_current_user( $this->admin );

		$u1 = $this->factory->blog->create();
		$u2 = $this->factory->blog->create();
		$u3 = $this->factory->blog->create();
		$u4 = $this->factory->blog->create();

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

		// Confirm emptiness.
		$this->assertNotEmpty( $results );

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'blogId' );

		// Confirm pagination.
		$this->assertTrue( $results['data']['blogs']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['blogs']['pageInfo']['hasPreviousPage'] );
		
		// Confirm total count.
		$this->assertTrue( count( $blogs_ids ) === 2 );

		// @todo confirm second pagination.
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
