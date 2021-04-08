<?php

/**
 * Test_Blogs_blogBy_Queries Class.
 *
 * @group blogs
 */
class Test_Blogs_blogBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_blog_query() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		$blog = $this->bp_factory->blog->create_and_get(
			[
				'title' => 'The Foo Bar Blog',
			]
		);

		$global_id = $this->toRelayId( 'blog', $blog->blog_id );
		$query     = "
			query {
				blogBy(id: \"{$global_id}\") {
					id
					blogId
					blogAdmin {
						userId
					}
					name
					description
					attachmentAvatar {
						full
					}
					attachmentCover {
						full
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'blogBy' => [
						'id'        => $global_id,
						'blogId'    => absint( $blog->blog_id ),
						'blogAdmin' => [
							'userId' => $this->user,
						],
						'name'  => 'The Foo Bar Blog',
						'description'  => 'Just another Test Blog Network site',
						'attachmentAvatar' => [
							'full'  => $this->get_avatar_image( 'full', 'blog', $blog->blog_id ),
						],
						'attachmentCover'  => null,
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
	}

	public function test_blog_query_invalid_blog_id() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$blog_id = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query   = "
			query {
				blogBy(blogId: {$blog_id}) {
					blogId
				}
			}
		";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage(
				sprintf(
					'No Blog was found with ID: %d',
					GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER
				)
			);
	}

	public function test_blog_query_with_blogid_param() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$blog_id = $this->bp_factory->blog->create();
		$query   = "
			query {
				blogBy(blogId: {$blog_id}) {
					id
					blogId
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'id', $this->toRelayId( 'blog', $blog_id ) )
			->hasField( 'blogId', $blog_id );
	}
}
