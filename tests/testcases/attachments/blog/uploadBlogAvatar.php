<?php

/**
 * Test_Attachment_Blog_Avatar_Mutation
 *
 * @group attachment-avatar
 * @group blog-avatar
 */
class Test_Attachment_Blog_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_blog_upload_avatar() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		// Skip until BuddyPress core supports it.
		$this->markTestSkipped();

		$blog = $this->bp_factory->blog->create();

		$this->bp->set_current_user( $this->admin );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'BLOG', $blog );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'blog', $blog ),
				'thumb' => $this->get_avatar_image( 'thumb', 'blog', $blog ),
			]);

		// Confirm that the default avatar is not present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );
	}

	public function test_blog_avatar_upload_with_upload_disabled() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		// Disabling blog avatar upload.
		buddypress()->avatar->show_avatars = false;

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, blog avatar upload is disabled.' );

		// Enable it.
		buddypress()->avatar->show_avatars = true;
	}

	public function test_blog_avatar_upload_without_loggin_in_user() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_blog_avatar_upload_with_an_invalid_blog_id() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_blog_avatar_upload_with_member_without_permissions() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
