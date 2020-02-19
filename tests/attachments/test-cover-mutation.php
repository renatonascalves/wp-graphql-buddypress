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

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_upload_user_cover() {
		$u = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u );

		$mutation = $this->upload_cover( 'members', $u );

		$this->assertEquals(
			[
				'data' => [
					'createCoverTest' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_cover_image( 'members', $u ),
							'thumb' => null,
						],
					],
				],
			],
			do_graphql_request( $mutation[0], 'createCoverTest', $mutation[1] )
		);
	}

	protected function upload_cover( string $object, int $objectId ) {
		$mutation = '
		mutation createCoverTest( $clientMutationId: String!, $file: String!, $object: AttachmentCoverEnum!, $objectId: Int! ) {
			createAttachmentCover(
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
				'file'             => $this->image_file,
				'object'           => $object,
				'objectId'         => $objectId,
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
