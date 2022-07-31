<?php

/**
 * Test_Attachment_deleteGroupAvatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group group-avatar
 */
class Test_Attachment_deleteGroupAvatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_regular_admins_can_delete_any_group_avatar() {
		$this->bp->set_current_user( $this->user_id );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'group', absint( $this->group ) );
		$thumb = $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		// Switch to the admin user here.
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', absint( $this->group ) ), 'mystery-group' ) );
	}

	public function test_group_creator_can_delete_avatar() {
		$this->bp->set_current_user( $this->user_id );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'group', absint( $this->group ) );
		$thumb = $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		$this->assertQuerySuccessful( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', absint( $this->group ) ), 'mystery-group' ) );
	}

	public function test_group_admins_can_delete_avatar() {
		// Add user to group as an admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->user_id );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'group', absint( $this->group ) );
		$thumb = $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		// Switch to the group admin user here.
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField(
				'attachment',
				[
					'full'  => $full,
					'thumb' => $thumb,
				]
			);

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', absint( $this->group ) ), 'mystery-group' ) );
	}

	public function test_group_mods_can_not_delete_avatar() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_members_can_not_delete_avatar() {
		// Add regular user to group.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_users_can_not_delete_group_avatar() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_can_not_delete_group_avatar_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_avatar_with_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}
}
