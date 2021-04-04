<?php

/**
 * Test_Blogs_Queries Class.
 *
 * @group blogs
 */
class Test_Blogs_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_blog_query_with_body_by() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$u = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u );

		$b1 = $this->bp_factory->blog->create();

		$global_id = $this->toRelayId( 'blog', $b1 );

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
			$this->graphql( compact( 'query' ) )
		);
	}

	public function test_blog_query_with_another_logged_in_user() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$u1 = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u1 );
		$b1 = $this->bp_factory->blog->create();

		$u2 = $this->bp_factory->user->create();
		$this->bp->set_current_user( $u2 );

		$global_id = $this->toRelayId( 'blog', $b1 );

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
							'userId' => $u1,
						],
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
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

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'blogId' );

		$this->assertTrue( count( $blogs_ids ) === 5 );
		$this->assertContains( $b1, $blogs_ids );
		$this->assertContains( $b2, $blogs_ids );
	}

	public function test_blogs_query_using_where_include() {
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

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );

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

		$this->bp->set_current_user( $this->admin );

		$u1 = $this->bp_factory->blog->create();
		$u2 = $this->bp_factory->blog->create();
		$u3 = $this->bp_factory->blog->create();
		$u4 = $this->bp_factory->blog->create();

		// Here we're querying the blogs in our dataset.
		$results = $this->blogsQuery(
			[
				'first' => 2,
				'where' => [
					'include' => [ $u1, $u2, $u3, $u4 ],
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );

		$blogs_ids = wp_list_pluck( $results['data']['blogs']['nodes'], 'blogId' );

		// Confirm pagination.
		$this->assertTrue( $results['data']['blogs']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['blogs']['pageInfo']['hasPreviousPage'] );

		// Confirm total count.
		$this->assertTrue( count( $blogs_ids ) === 2 );

		// @todo confirm second pagination.
	}
}
