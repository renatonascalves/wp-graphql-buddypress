<?php

/**
 * Test_Attachment_deleteMemberCover_Mutation Class.
 *
 * @group attachment-cover
 * @group member-cover
 */
class Test_Attachment_deleteMemberCover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_member_can_delete_his_own_cover() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$cover = $this->get_cover_image( 'members', absint( $this->user ) );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $cover,
					'thumb' => null,
				]
			);

		$this->assertQuerySuccessful( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->hasField( 'deleted', true )
			->hasField(
				'attachment',
				[
					'full'  => $cover,
					'thumb' => null,
				]
			);

		// There is no default member cover.
		$this->assertTrue( empty( $this->get_cover_image( 'members', absint( $this->user ) ) ) );
	}

	public function test_regular_admins_can_delete_other_member_cover() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$cover = $this->get_cover_image( 'members', absint( $this->user ) );

		$this->assertQuerySuccessful( $response )
			->hasField(
				'attachment',
				[
					'full'  => $cover,
					'thumb' => null,
				]
			);

		// Switch to the admin user here.
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->hasField( 'deleted', true )
			->hasField(
				'attachment',
				[
					'full'  => $cover,
					'thumb' => null,
				]
			);

		// There is no default member cover.
		$this->assertTrue( empty( $this->get_cover_image( 'members', absint( $this->user ) ) ) );
	}

	public function test_member_can_not_delete_other_member_cover() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_can_not_delete_member_cover_without_logged_in_user() {
		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_member_cover_without_uploaded_covers() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}

	public function test_delete_member_cover_with_invalid_member_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}
}
