<?php

/**
 * Test_Attachment_deleteGroupCover_Mutation Class.
 *
 * @group attachment-cover
 * @group group-cover
 */
class Test_Attachment_deleteGroupCover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_regular_admins_can_delete_any_group_cover() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'groups', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);

		// Switch to the admin user here.
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);
	}

	public function test_group_creator_can_delete_cover() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'groups', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);

		$this->assertQuerySuccessful( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);
	}

	public function test_group_admins_can_delete_cover() {
		// Add user to group as an admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'groups', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);

		// Switch to the group admin user here.
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			]);
	}

	public function test_group_mods_can_not_delete_cover() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_can_not_delete_group_cover_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_users_can_not_delete_group_cover() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_members_can_not_delete_group_cover() {
		// Add regular group member.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_cover_without_uploaded_covers() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}

	public function test_delete_group_cover_with_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}
}
