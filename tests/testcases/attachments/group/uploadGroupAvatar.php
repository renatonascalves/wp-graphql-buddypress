<?php

/**
 * Test_Attachment_Group_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group group-avatar
 */
class Test_Attachment_Group_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_group_avatar_upload_with_upload_disabled() {
		// Disabling avatar upload.
		add_filter( 'bp_disable_group_avatar_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, group avatar upload is disabled.' );
	}

	public function test_group_avatar_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_avatar_upload_with_an_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_group_avatar_upload_with_member_without_group_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
