<?php

/**
 * Test_Attachment_deleteMemberAvatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group member-avatar
 */
class Test_Attachment_deleteMemberAvatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_member_can_delete_his_own_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'USER', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'user', absint( $this->user ) );
		$thumb = $this->get_avatar_image( 'thumb', 'user', absint( $this->user ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		$this->assertQuerySuccessful( $this->delete_avatar( 'USER', absint( $this->user ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'user', absint( $this->user ) ), 'mystery-man' ) );
	}

	public function test_regular_admins_can_delete_other_member_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'USER', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'user', absint( $this->user ) );
		$thumb = $this->get_avatar_image( 'thumb', 'user', absint( $this->user ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		// Switch to the admin user here.
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_avatar( 'USER', absint( $this->user ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'user', absint( $this->user ) ), 'mystery-man' ) );
	}

	public function test_member_can_not_delete_other_member_avatar() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'USER', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_member_can_not_delete_avatar_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_avatar( 'USER', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_member_avatar_with_invalid_member_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'USER', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}
}
