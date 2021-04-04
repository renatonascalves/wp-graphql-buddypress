<?php

/**Test_Attachment_Blog_Avatar_Mutation
 * Test_Attachment_Group_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group attachment-blog-avatar
 */
class Test_Attachment_Blog_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @group blog-avatar
	 */
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

		$this->bp->set_current_user(
			$this->bp_factory->user->create(
				[ 'role' => 'administrator' ]
			)
		);

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'BLOG', $blog );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_avatar_image( 'full', 'blog', $blog ),
							'thumb' => $this->get_avatar_image( 'thumb', 'blog', $blog ),
						],
					],
				],
			],
			$response
		);

		// Confirm that the default avatar is not present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );
	}

	/**
	 * @group blog-avatar
	 */
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

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_without_a_loggin_in_user() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_an_invalid_blog_id() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_member_without_permissions() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
