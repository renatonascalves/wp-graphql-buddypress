<?php

/**
 * Test_Attachment_Group_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group attachment-group-avatar
 */
class Test_Attachment_Group_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_upload_disabled() {
		// Disabling avatar upload.
		add_filter( 'bp_disable_group_avatar_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', $this->user ) )
			->expectedErrorMessage( 'Sorry, group avatar upload is disabled.' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_without_a_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_an_invalid_group_id() {
		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_member_without_group_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->upload_avatar( 'GROUP', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->delete_avatar( 'GROUP', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_delete_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', $this->group );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$full  = $this->get_avatar_image( 'full', 'group', $this->group );
		$thumb = $this->get_avatar_image( 'thumb', 'group', $this->group );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $full,
							'thumb' => $thumb,
						],
					],
				],
			],
			$response
		);

		$this->assertEquals(
			[
				'data' => [
					'deleteAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'attachment'       => [
							'full'  => $full,
							'thumb' => $thumb,
						],
					],
				],
			],
			$this->delete_avatar( 'GROUP', $this->group )
		);

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', $this->group ), 'mystery-group.png' ) );
	}
}
