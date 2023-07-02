<?php

/**
 * Test_Attachment_uploadGroupCover_Mutation Class.
 *
 * @group attachment-cover
 * @group group-cover
 */
class Test_Attachment_uploadGroupCover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_group_creator_can_upload_cover() {
		$this->bp->set_current_user( $this->user_id );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $this->get_cover_image( 'groups', absint( $this->group ) ),
					'thumb' => null,
				]
			);

		// Confirm the group path.
		$attachment = $response['data']['uploadAttachmentCover']['attachment']['full'];
		$this->assertTrue( false !== strpos( $attachment, 'buddypress/groups' ) );
		$this->assertTrue( false !== strpos( $attachment, 'cover-image' ) );
	}

	public function test_regular_admins_can_upload_any_group_cover() {
		$this->bp->set_current_user( $this->admin );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $this->get_cover_image( 'groups', absint( $this->group ) ),
					'thumb' => null,
				]
			);

		// Confirm the group path.
		$attachment = $response['data']['uploadAttachmentCover']['attachment']['full'];
		$this->assertTrue( false !== strpos( $attachment, 'buddypress/groups' ) );
		$this->assertTrue( false !== strpos( $attachment, 'cover-image' ) );
	}

	public function test_group_admins_can_upload_cover() {
		// Add user to group as an admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->random_user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $this->get_cover_image( 'groups', absint( $this->group ) ),
					'thumb' => null,
				]
			);

		// Confirm the group path.
		$attachment = $response['data']['uploadAttachmentCover']['attachment']['full'];
		$this->assertTrue( false !== strpos( $attachment, 'buddypress/groups' ) );
		$this->assertTrue( false !== strpos( $attachment, 'cover-image' ) );
	}

	public function test_group_mods_can_not_delete_cover() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_cover_upload_when_group_cover_upload_is_disabled() {
		add_filter( 'bp_disable_group_cover_image_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, group cover upload is disabled.' );

		remove_filter( 'bp_disable_group_cover_image_uploads', '__return_true' );
	}

	public function test_group_cover_image_feature_is_disabled() {
		// Disabling group cover feature.
		add_filter( 'bp_is_groups_cover_image_active', '__return_false' );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );

		remove_filter( 'bp_is_groups_cover_image_active', '__return_false' );
	}

	public function test_group_members_can_not_upload_group_cover() {
		// Add regular group member.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_cover_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_regular_members_can_not_upload_group_cover() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_cover_upload_with_an_invalid_group_id() {
		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}
}
