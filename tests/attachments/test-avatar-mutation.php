<?php

/**
 * Test_Attachment_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 */
class Test_Attachment_Avatar_Mutation extends WP_UnitTestCase {

	public $bp_factory;
	public $bp;
	public $client_mutation_id;
	public $image_file;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->image_file         = '';
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_upload_user_avatar() {
		$u = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u );

		$mutation = $this->upload_avatar( 'USER', $u );

		$this->assertEquals(
			[
				'data' => [
					'createAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_avatar_image( 'full', 'user', $u ),
							'thumb' => $this->get_avatar_image( 'thumb', 'user', $u ),
						],
					],
				],
			],
			do_graphql_request( $mutation[0], 'createAvatarTest', $mutation[1] )
		);
	}

	protected function upload_avatar( string $object, int $objectId ) {
		$mutation = '
		mutation createAvatarTest( $clientMutationId: String!, $file: String!, $object: AttachmentAvatarEnum!, $objectId: Int! ) {
			createAttachmentAvatar(
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

	protected function get_avatar_image( $size, $object, $item_id ) {
		return bp_core_fetch_avatar(
			[
				'object'  => $object,
				'type'    => $size,
				'item_id' => $item_id,
				'html'    => false,
			]
		);
	}
}
