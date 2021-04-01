<?php

/**
 * Test_Attachment_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 */
class Test_Attachment_Avatar_Mutation extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

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
			[
				'name'        => 'Group Test',
				'description' => 'Group Description',
				'creator_id'  => $this->user,
			]
		);
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

		$mutation = $this->upload_avatar( 'USER', $this->user );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

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
	 * @group member-avatar
	 */
	public function test_member_avatar_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->delete_avatar( 'USER', $this->user );
		$response = do_graphql_request( $mutation[0], 'deleteAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
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

		$mutation = $this->upload_avatar( 'USER', $u );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		remove_filter( 'pre_move_uploaded_file', [ $this, 'copy_file' ], 10, 3 );

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

		$mutation = $this->delete_avatar( 'USER', $u );
		$response = do_graphql_request( $mutation[0], 'deleteAvatarTest', $mutation[1] );

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
			$response
		);

		// Confirm that the default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'user', $u ), 'mystery-man.jpg' ) );
	}

	/**
	 * @group group-avatar
	 */
	public function test_group_upload_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$mutation = $this->upload_avatar( 'GROUP', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

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
	public function test_group_avatar_upload_with_an_invalid_group_id() {
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

	/**
	 * @group group-avatar
	 */
	public function test_group_avatar_delete_without_permissions() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->delete_avatar( 'GROUP', $this->group );
		$response = do_graphql_request( $mutation[0], 'deleteAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
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

		$mutation = $this->upload_avatar( 'GROUP', $this->group );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

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

		$mutation = $this->delete_avatar( 'GROUP', $this->group );
		$response = do_graphql_request( $mutation[0], 'deleteAvatarTest', $mutation[1] );

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
			$response
		);

		// Confirm that the group default avatar IS present.
		$this->assertTrue( false !== strpos( $this->get_avatar_image( 'full', 'group', $this->group ), 'mystery-group.png' ) );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_upload_avatar() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		if ( function_exists( 'wp_initialize_site' ) ) {
			$this->setExpectedDeprecated( 'wpmu_new_blog' );
		}

		// Skip until BuddyPress core supports it.
		$this->markTestSkipped();

		$blog = $this->bp_factory->blog->create();

		$this->bp->set_current_user(
			$this->bp_factory->user->create(
				[ 'role' => 'administrator' ]
			)
		);

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$mutation = $this->upload_avatar( 'BLOG', $blog );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		remove_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$this->assertEquals(
			[
				'data' => [
					'uploadAttachmentAvatar' => [
						'clientMutationId' => $this->client_mutation_id,
						'attachment'       => [
							'full'  => $this->get_avatar_image( 'full', 'blog', $blog ),
							'thumb' => $this->get_avatar_image( 'thumb', 'blog', $blog ),
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
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_upload_disabled() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		$mutation = $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() );

		// Disabling blog avatar upload.
		buddypress()->avatar->show_avatars = false;

		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, blog avatar upload is disabled.', $response['errors'][0]['message'] );

		// Enable it.
		buddypress()->avatar->show_avatars = true;
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_without_a_loggin_in_user() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$mutation = $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_an_invalid_blog_id() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$mutation = $this->upload_avatar( 'BLOG', 99999999 );
		$response = do_graphql_request( $mutation[0], 'uploadAvatarTest', $mutation[1] );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Sorry, you are not allowed to perform this action.', $response['errors'][0]['message'] );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_member_without_permissions() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$mutation = $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() );
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

	protected function delete_avatar( string $object, int $objectId ): array {
		$mutation = '
		mutation deleteAvatarTest( $clientMutationId: String!, $object: AttachmentAvatarEnum!, $objectId: Int! ) {
			deleteAttachmentAvatar(
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

	protected function get_avatar_image( $size, $object, $item_id ): string {
		return bp_core_fetch_avatar(
			[
				'object'  => $object,
				'type'    => $size,
				'item_id' => $item_id,
				'html'    => false,
				'no_grav' => true,
			]
		);
	}

	public function copy_file( $return = null, $file, $new_file ) {
		return @copy( $file['tmp_name'], $new_file );
	}
}
