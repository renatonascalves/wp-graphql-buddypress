<?php

/**
 * Test_Attachment_uploadMemberCover_Mutation Class.
 *
 * @group attachment-cover
 * @group member-cover
 */
class Test_Attachment_uploadMemberCover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_member_can_upload_his_own_cover() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_cover_image( 'members', absint( $this->user ) ),
				'thumb' => null,
			] );

		// Confirm the member path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'buddypress/members' ) );
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'cover-image' ) );
	}

	public function test_regular_admins_can_upload_any_member_cover() {
		$this->bp->set_current_user( $this->admin );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_cover_image( 'members', absint( $this->user ) ),
				'thumb' => null,
			] );

		// Confirm the member path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'buddypress/members' ) );
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'cover-image' ) );
	}

	public function test_member_cover_upload_when_member_cover_upload_is_disabled() {
		add_filter( 'bp_disable_cover_image_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, member cover upload is disabled.' );
	}

	public function test_member_cover_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_member_can_not_upload_another_member_cover() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_member_cover_upload_with_an_invalid_member_id() {
		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}
}
