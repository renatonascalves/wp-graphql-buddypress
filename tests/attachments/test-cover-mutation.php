<?php

/**
 * Test_Attachment_Cover_Mutation Class.
 *
 * @group attachment-cover
 */
class Test_Attachment_Cover_Mutation extends WP_UnitTestCase {

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

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @group member-cover
	 */
	public function test_member_cover_upload_with_upload_disabled() {
		$mutation = $this->upload_cover( 'MEMBERS', $this->user );

		// Disabling avatar upload.
		add_filter( 'bp_disable_cover_image_uploads', '__return_true' );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, member cover upload is disabled.', $response['errors'][0]['message'] );

		// Enabling again.
		add_filter( 'bp_disable_cover_image_uploads', '__return_false' );
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
		$this->bp->set_current_user( $this->user );

		$mutation = $this->upload_cover( 'MEMBERS', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadCoverTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
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

	protected function get_cover_image( $object, $item_id ) {
		return bp_attachments_get_attachment(
			'url',
			[
				'object_dir' => $object,
				'item_id'    => $item_id,
			]
		);
	}
}
