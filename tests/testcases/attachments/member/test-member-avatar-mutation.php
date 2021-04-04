<?php

/**
 * Test_Attachment_Member_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group attachment-member-avatar
 */
class Test_Attachment_Member_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_upload_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'USER', $this->user );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertQuerySuccessful( $response );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_avatar_image( 'full', 'user', $this->user ),
							'thumb' => $this->get_avatar_image( 'thumb', 'user', $this->user ),
						],
					],
				],
			],
			$response
		);

		// Confirm that the default avatar is not present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_with_upload_disabled() {
		// Disabling avatar upload.
		add_filter( 'bp_disable_avatar_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_avatar( 'USER', $this->user ) )
			->expectedErrorMessage( 'Sorry, member avatar upload is disabled.' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_without_a_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_avatar( 'USER', $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_with_an_invalid_member_id() {
		$this->assertQueryFailed( $this->upload_avatar( 'USER', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->upload_avatar( 'USER', $this->bp_factory->user->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->delete_avatar( 'USER', $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_delete_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$u = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_avatar( 'USER', $u );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$this->assertQuerySuccessful( $response );

		$full  = $this->get_avatar_image( 'full', 'user', $u );
		$thumb = $this->get_avatar_image( 'thumb', 'user', $u );

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
			$this->delete_avatar( 'USER', $u )
		);

		// Confirm that the default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'user', $u ), 'mystery-man.jpg' ) );
	}
}
