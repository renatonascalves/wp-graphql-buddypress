<?php

/**
 * Test_Attachment_uploadMemberAvatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group member-avatar
 */
class Test_Attachment_uploadMemberAvatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_member_can_upload_his_own_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'USER', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'user', absint( $this->user ) ),
				'thumb' => $this->get_avatar_image( 'thumb', 'user', absint( $this->user ) ),
			] );

		// Confirm that the default avatar IS NOT present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );
	}

	public function test_regular_admins_can_upload_any_member_avatar() {
		$this->bp->set_current_user( $this->admin );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'USER', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'user', absint( $this->user ) ),
				'thumb' => $this->get_avatar_image( 'thumb', 'user', absint( $this->user ) ),
			] );

		// Confirm that the default avatar IS NOT present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );
	}

	public function test_member_avatar_upload_when_member_avatar_upload_is_disabled() {
		// Disabling avatar upload.
		add_filter( 'bp_disable_avatar_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_avatar( 'USER', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, member avatar upload is disabled.' );
	}

	public function test_member_avatar_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_avatar( 'USER', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_member_can_not_upload_another_member_avatar() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->upload_avatar( 'USER', absint( $this->random_user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_member_avatar_upload_with_an_invalid_member_id() {
		$this->assertQueryFailed( $this->upload_avatar( 'USER', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}
}
