<?php

/**
 * Test_Attachment_Delete_Group_Cover_Mutation Class.
 *
 * @group attachment-cover
 * @group group-cover
 */
class Test_Attachment_Delete_Group_Cover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_delete_group_cover_without_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_group_cover_without_uploaded_covers() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}

	public function test_delete_group_upload_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

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
}
