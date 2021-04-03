<?php

/**
 * Test_Attachment_Avatar_Mutation Class.
 *
 * @group attachment-avatar
 */
class Test_Attachment_Avatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

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

		$response = $this->upload_avatar( 'USER', $this->user );

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

	/**
	 * @group group-avatar
	 */
	public function test_group_upload_avatar() {
		if ( 4.9 > (float) $GLOBALS['wp_version'] ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->user );

		add_filter( 'pre_move_uploaded_file', array( $this, 'copy_file' ), 10, 3 );

		$response = $this->upload_avatar( 'GROUP', $this->group );

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

		$response = $this->upload_avatar( 'BLOG', $blog );

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

		// Disabling blog avatar upload.
		buddypress()->avatar->show_avatars = false;

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, blog avatar upload is disabled.' );

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

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_an_invalid_blog_id() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', 99999999 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @group blog-avatar
	 */
	public function test_blog_avatar_upload_with_member_without_permissions() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped();
		}

		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->upload_avatar( 'BLOG', $this->bp_factory->blog->create() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
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
