<?php

/**
 * Test_Attachment_Delete_Group_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group group-avatar
 */
class Test_Attachment_Delete_Group_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_delete_group_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', absint( $this->group ) );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'group', absint( $this->group ) );
		$thumb = $this->get_avatar_image( 'thumb', 'group', absint( $this->group ) );

		$this->assertQuerySuccessful( $response )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		$this->assertQuerySuccessful( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->hasField( 'deleted', true )
			->hasField( 'attachment', [
				'full'  => $full,
				'thumb' => $thumb,
			] );

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', absint( $this->group ) ), 'mystery-group.png' ) );
	}

	public function test_delete_group_avatar_without_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', absint( $this->group ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
