<?php

/**
 * Test_Attachment_Member_Cover_Mutation Class.
 *
 * @group attachment-cover
 * @group attachment-member-cover
 */
class Test_Attachment_Member_Cover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @group member-cover
	 */
	public function test_member_upload_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', $this->user );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentCover' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_cover_image( 'members', $this->user ),
							'thumb' => null,
						],
					],
				],
			],
			$response
		);

		// Confirm the member path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'buddypress/members' ) );
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'cover-image' ) );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_upload_disabled() {
		add_filter( 'bp_disable_cover_image_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', $this->user ) )
			->expectedErrorMessage( 'Sorry, member cover upload is disabled.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_without_a_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_an_invalid_member_id() {
		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', $this->user ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_delete_without_uploaded_covers() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->delete_cover( 'MEMBERS', $this->user ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_upload_cover_delete_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'MEMBERS', $this->user );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'members', $this->user );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentCover' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $cover,
							'thumb' => null,
						],
					],
				],
			],
			$response
		);

		$this->assertEquals(
			[
				'data' => [
					'deleteAttachmentCover' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
						'attachment'       => [
							'full'  => $cover,
							'thumb' => null,
						],
					],
				],
			],
			$this->delete_cover( 'MEMBERS', $this->user )
		);
	}
}
