<?php

/**
 * Test_Blogs_blogBy_Queries Class.
 *
 * @group blogs
 */
class Test_Blogs_blogBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_blog_query() {
		$this->skipWithoutMultisite();

		$this->bp->set_current_user( $this->user );

		$blog_id = $this->bp_factory->blog->create(
			[
				'title'  => 'The Foo Bar Blog',
				'domain' => 'foo-bar',
				'path'   => 'blog',
			]
		);

		switch_to_blog( $blog_id );

		$this->factory()->post->create();

		restore_current_blog();

		$this->assertQuerySuccessful( $this->get_a_blog( $blog_id ) )
			->hasField( 'id', $this->toRelayId( 'blog', $blog_id ) )
			->hasField( 'admin', [ 'userId' => $this->user ] )
			->hasField( 'name', 'The Foo Bar Blog' )
			->hasField( 'uri', 'http://foo-bar/blog/' )
			->hasField( 'domain', 'foo-bar' )
			->hasField( 'path', '/blog/' )
			->hasField( 'description', 'Just another Test Blog Network site' )
			->hasField(
				'attachmentAvatar',
				[
					'full' => $this->get_avatar_image( 'full', 'blog', $blog_id ),
				]
			)
			->hasField( 'attachmentCover', null )
			->hasField( 'databaseId', $blog_id );

		// Confirm that the default blog avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'blog', $blog_id ), 'mystery-blog' ) );
	}

	public function test_blog_query_invalid_blog_id() {
		$this->skipWithoutMultisite();

		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->get_a_blog( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage(
				sprintf(
					'No Blog was found with ID: %d',
					GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER
				)
			);
	}

	public function test_get_blog_with_avatar_disabled() {
		$this->skipWithoutMultisite();

		buddypress()->avatar->show_avatars = false;

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_a_blog( $this->bp_factory->blog->create() ) )
			->hasField( 'attachmentAvatar', null );

		buddypress()->avatar->show_avatars = true;
	}

	/**
	 * Get a blog.
	 *
	 * @param int|null $blog_id Blog ID.
	 * @return array
	 */
	protected function get_a_blog( $blog_id = null ): array {
		$query = "
			query {
				blogBy(blogId: {$blog_id}) {
					id
					databaseId
					admin {
						userId
					}
					name
					uri
					domain
					path
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

		return $this->graphql( compact( 'query' ) );
	}
}
