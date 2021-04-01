<?php

/**
 * Test_Attachment_Cover_Mutation Class.
 *
 * @group attachment-cover
 */
class Test_Attachment_Cover_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

	public $bp_factory;
	public $bp;
	public $client_mutation_id;
	public $image_file;
	public $user;
	public $group;

	public function setUp() {
		parent::setUp();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->client_mutation_id = 'someUniqueId';
		$this->image_file         = __DIR__ . '/assets/test-image.jpeg';
		$this->user               = $this->bp_factory->user->create();
		$this->group              = $this->bp_factory->group->create(
			array(
				'name'        => 'Group Test',
				'description' => 'Group Description',
				'creator_id'  => $this->user,
			)
		);
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

		$mutation = $this->upload_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

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
		$mutation = $this->upload_cover( 'MEMBERS', $this->user );

		// Disabling avatar upload.
		add_filter( 'bp_disable_cover_image_uploads', '__return_true' );

		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, member cover upload is disabled.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_without_a_loggin_in_user() {
		$mutation = $this->upload_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_an_invalid_member_id() {
		$mutation = $this->upload_cover( 'MEMBERS', 99999999 );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->upload_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->delete_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_delete_without_uploaded_covers() {
		$this->bp->set_current_user( $this->user );

		$mutation = $this->delete_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, there are no uploaded covers to delete.', $response['errors'][0]['message'] );
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

		$mutation = $this->upload_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

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

		$mutation = $this->delete_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

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
			$response
		);
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

		$mutation = $this->upload_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

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

		$mutation = $this->upload_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, group cover upload is disabled.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_without_a_loggin_in_user() {
		$mutation = $this->upload_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_with_an_invalid_group_id() {
		$mutation = $this->upload_cover( 'MEMBERS', 99999999 );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->upload_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->delete_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-cover
	 */
	public function test_group_cover_delete_without_uploaded_covers() {
		$this->bp->set_current_user( $this->user );

		$mutation = $this->delete_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, there are no uploaded covers to delete.', $response['errors'][0]['message'] );
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

		$mutation = $this->upload_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

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

		$mutation = $this->delete_cover( 'GROUPS', $this->group );
		$response = do_graphql_request( $mutation[0], 'deleteCoverTest', $mutation[1] );

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
			$response
		);
	}

	protected function delete_cover( string $object, int $objectId ): array {
		$mutation = '
		mutation deleteCoverTest( $clientMutationId: String!, $object: AttachmentCoverEnum!, $objectId: Int! ) {
			deleteAttachmentCover(
				input: {
					clientMutationId: $clientMutationId
					object: $object
					objectId: $objectId
				}
			)
		  	{
				clientMutationId
				deleted
				attachment {
					full
					thumb
				}
		  	}
		}
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'object'           => $object,
				'objectId'         => $objectId,
			]
		);

		return [ $mutation, $variables ];
	}

	protected function upload_cover( string $object, int $objectId ) {
		$mutation = '
		mutation uploadCoverTest( $clientMutationId: String!, $file: Upload!, $object: AttachmentCoverEnum!, $objectId: Int! ) {
			uploadAttachmentCover(
				input: {
					clientMutationId: $clientMutationId
					file: $file
					object: $object
					objectId: $objectId
				}
			)
		  	{
				clientMutationId
				attachment {
					full
					thumb
				}
		  	}
		}
		';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'object'           => $object,
				'objectId'         => $objectId,
				'file'              => [
					'fileName'  => $this->image_file,
					'mimeType' => 'IMAGE_JPEG'
				],
			]
		);

		return [ $mutation, $variables ];
	}

	protected function get_cover_image( $object, $item_id ): string {
		return bp_attachments_get_attachment(
			'url',
			[
				'object_dir' => $object,
				'item_id'    => $item_id,
			]
		);
	}

	public function copy_file( $return = null, $file, $new_file ) {
		return @copy( $file['tmp_name'], $new_file );
	}
}
