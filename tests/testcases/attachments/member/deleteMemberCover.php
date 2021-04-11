<?php

/**
 * Test_Attachment_deleteMemberCover_Mutation Class.
 *
 * @group attachment-cover
 * @group member-cover
 */
class Test_Attachment_deleteMemberCover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_delete_uploaded_member_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', absint( $this->user ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'members', absint( $this->user ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			] );

		$this->assertQuerySuccessful( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $cover,
				'thumb' => null,
			] );
	}

	public function test_delete_member_cover_without_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_member_cover_without_uploaded_covers() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}
}
