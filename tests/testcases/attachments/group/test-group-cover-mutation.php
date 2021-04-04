<?php

/**
 * Test_Attachment_Group_Cover_Mutation Class.
 *
 * @group attachment-cover
 * @group attachment-group-cover
 */
class Test_Attachment_Group_Cover_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @group group-cover
	 */
	public function test_group_upload_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', $this->group );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentCover' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_cover_image( 'groups', $this->group ),
							'thumb' => null,
						],
					],
				],
			],
			$response
		);

		// Confirm the group path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'buddypress/groups' ) );
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentCover']['attachment']['full'], 'cover-image' ) );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_with_upload_disabled() {
		// Disabling group cover upload.
		add_filter( 'bp_disable_group_cover_image_uploads', '__return_true' );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', $this->group ) )
			->expectedErrorMessage( 'Sorry, group cover upload is disabled.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_without_a_loggin_in_user() {
		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_with_an_invalid_group_id() {
		$this->assertQueryFailed( $this->upload_cover( 'MEMBERS', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->upload_cover( 'GROUPS', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', $this->group ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_delete_without_uploaded_covers() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_cover( 'GROUPS', $this->group ) )
			->expectedErrorMessage( 'Sorry, there are no uploaded covers to delete.' );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_upload_cover_delete_cover() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

		$response = $this->upload_cover( 'GROUPS', $this->group );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$cover = $this->get_cover_image( 'groups', $this->group );

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
			$this->delete_cover( 'GROUPS', $this->group )
		);
	}
}
