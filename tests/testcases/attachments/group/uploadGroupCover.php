<?php

/**
 * Test_Attachment_Group_Cover_Mutation Class.
 *
 * @group attachment-cover
 * @group group-cover
 */
class Test_Attachment_Group_Cover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_group_upload_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $this->get_cover_image( 'groups', absint( $this->group ) ),
				'thumb' => null,
			]);

		// Confirm the group path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'buddypress/groups' ) );
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'cover-image' ) );
	}

	public function test_group_cover_upload_with_upload_disabled() {
		// Disabling group cover upload.
		add_filter( 'bp_disable_group_cover_image_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, group cover upload is disabled.' );
	}

	public function test_group_cover_upload_without_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_cover_upload_with_an_invalid_group_id() {
		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_group_cover_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
