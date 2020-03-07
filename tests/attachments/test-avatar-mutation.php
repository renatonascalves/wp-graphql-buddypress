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
	 * @group member-avatar
	 */
	public function test_member_upload_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );
		add_filter( 'bp_core_avatar_dimension', array( $this, 'return_100' ), 10, 1 );

		$mutation = $this->upload_avatar( 'USER', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );
		remove_filter( 'bp_core_avatar_dimension', array( $this, 'return_100' ), 10, 1 );

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
		$mutation = $this->upload_avatar( 'USER', $this->user );

		// Disabling avatar upload.
		add_filter( 'bp_disable_avatar_uploads', '__return_true' );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, member avatar upload is disabled.', $response['errors'][0]['message'] );

		// Enabling again.
		add_filter( 'bp_disable_avatar_uploads', '__return_false' );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_without_a_loggin_in_user() {
		$mutation = $this->upload_avatar( 'USER', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_with_an_invalid_member_id() {
		$mutation = $this->upload_avatar( 'USER', 99999999 );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group member-avatar
	 */
	public function test_member_avatar_upload_with_member_without_permissions() {
		$this->bp->set_current_user( $this->user );

		$mutation = $this->upload_avatar( 'USER', $this->bp_factory->user->create() );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_upload_avatar() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );
		add_filter( 'bp_core_avatar_dimension', array( $this, 'return_100' ), 10, 1 );

		$mutation = $this->upload_avatar( 'GROUP', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		print_r( $response );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );
		remove_filter( 'bp_core_avatar_dimension', array( $this, 'return_100' ), 10, 1 );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_avatar_image( 'full', 'group', $this->group ),
							'thumb' => $this->get_avatar_image( 'thumb', 'group', $this->group ),
						],
					],
				],
			],
			$response
		);

		// Confirm that the default avatar is not present.
		$this->assertTrue( false === strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'mistery-man' ) );

		// Confirm the group avatars path.
		$this->assertTrue( false !== strpos( $response['data']['uploadAttachmentAvatar']['attachment']['full'], 'group-avatars' ) );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_upload_disabled() {
		$mutation = $this->upload_avatar( 'GROUP', $this->user );

		// Disabling avatar upload.
		add_filter( 'bp_disable_group_avatar_uploads', '__return_true' );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, group avatar upload is disabled.', $response['errors'][0]['message'] );

		// Enabling again.
		add_filter( 'bp_disable_group_avatar_uploads', '__return_false' );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_without_a_loggin_in_user() {
		$mutation = $this->upload_avatar( 'GROUP', $this->group );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_an_invalid_member_id() {
		$mutation = $this->upload_avatar( 'GROUP', 99999999 );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_upload_with_member_without_group_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->upload_avatar( 'GROUP', $this->group );

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	protected function upload_avatar( string $object, int $objectId ): array {
		$mutation = '
		mutation uploadAvatarTest( $clientMutationId: String!, $file: Upload!, $object: AttachmentAvatarEnum!, $objectId: Int! ) {
			uploadAttachmentAvatar(
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

	protected function get_avatar_image( $size, $object, $item_id ) {
		return bp_core_fetch_avatar(
			[
				'object'  => $object,
				'type'    => $size,
				'item_id' => $item_id,
				'html'    => false,
				'no_grav' => false,
			]
		);
	}

	public function copy_file( $return = null, $file, $new_file ) {
		return @copy( $file['tmp_name'], $new_file );
	}

	public function return_100() {
		return 100;
	}
}
