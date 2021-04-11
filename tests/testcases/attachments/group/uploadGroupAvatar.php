<?php

/**
 * Test_Attachment_uploadGroupAvatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group group-avatar
 */
class Test_Attachment_uploadGroupAvatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_group_creator_can_upload_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'group', absint( $this->group ) ),
				'thumb' => $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) ),
			] );
	}

	public function test_regular_admins_can_upload_any_group_avatar() {
		$this->bp->set_current_user( $this->admin );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'group', absint( $this->group ) ),
				'thumb' => $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) ),
			] );
	}

	public function test_group_admins_can_upload_avatar() {
		// Add user to group as an admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->random_user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_avatar_image( 'full', 'group', absint( $this->group ) ),
				'thumb' => $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) ),
			] );
	}

	public function test_group_mods_can_not_delete_avatar() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_avatar_upload_when_group_avatar_upload_is_disabled() {
		// Disabling avatar upload.
		add_filter( 'bp_disable_group_avatar_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, group avatar upload is disabled.' );
	}

	public function test_group_avatar_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_members_can_not_upload_group_avatar() {
		// Add regular group member.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_members_can_not_upload_group_avatar() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_avatar_upload_with_an_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}
}
