<?php
/**
 * WPGraphQL BuddyPress test utility functions/assertions.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

use Mantle\Testing\Concerns\Core_Shim;
use Mantle\Testing\Concerns\Refresh_Database;
use Pest\PestPluginWordPress\FrameworkTestCase as Test_Case;

/**
 * WPGraphQL_BuddyPress_UnitTestCase Class.
 */
class WPGraphQL_BuddyPress_UnitTestCase extends Test_Case {

	use Core_Shim, Refresh_Database;

	/**
	 * BuddyPress unit test factory class.
	 *
	 * @var BP_UnitTest_Factory
	 */
	public $bp_factory;

	/**
	 * BuddyPress unit test case.
	 *
	 * @var BP_UnitTestCase
	 */
	public $bp;

	/**
	 * Query response.
	 *
	 * @var array
	 */
	public $response = [];

	/**
	 * Mutation ID.
	 *
	 * @var string
	 */
	public $client_mutation_id;

	/**
	 * Image file.
	 *
	 * @var string
	 */
	public $image_file;

	/**
	 * Regular user.
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * Random regular user.
	 *
	 * @var int
	 */
	public $random_user;

	/**
	 * Admin user.
	 *
	 * @var int
	 */
	public $admin;

	/**
	 * Group object.
	 *
	 * @var BP_Groups_Group
	 */
	public $group;

	/**
	 * Thread object.
	 *
	 * @var BP_Messages_Thread
	 */
	public $thread;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		/**
		 * Reset the WPGraphQL schema before each test.
		 * Lazy loading types only loads part of the schema,
		 * so we refresh for each test.
		 */
		WPGraphQL::clear_schema();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->client_mutation_id = 'someUniqueId';
		$this->image_file         = dirname( dirname( __FILE__ ) ) . '/assets/test-image.jpeg';
		$this->user_id            = $this->bp_factory->user->create();
		$this->random_user        = $this->bp_factory->user->create();
		$this->admin              = $this->bp_factory->user->create( [ 'role' => 'administrator' ] );
		$this->thread             = $this->bp_factory->message->create_and_get(
			[
				'sender_id'  => $this->random_user,
				'recipients' => [ $this->user_id ],
				'subject'    => 'Threat Test',
				'content'    => 'Bar',
			]
		);
		$this->group              = $this->bp_factory->group->create(
			[
				'name'        => 'Group Test',
				'description' => 'Group Description',
				'creator_id'  => $this->user_id,
			]
		);

		// Add group type.
		bp_groups_register_group_type( 'foo' );
		bp_groups_register_group_type( 'bar' );

		// Add member type.
		bp_register_member_type( 'foo' );
	}

	/**
	 * Wrapper for the "graphql()" function.
	 *
	 * @param array $args Query arguments.
	 * @return array
	 */
	public function graphql( ...$args ): array {
		return graphql( ...$args );
	}

	/**
	 * Wrapper for the "GraphQLRelay\Relay::toGlobalId()" function.
	 *
	 * @param string $type Type.
	 * @param string $id   ID.
	 * @return string
	 */
	public function toRelayId( $type, $id ): string {
		return \GraphQLRelay\Relay::toGlobalId( $type, $id );
	}

	/**
	 * Wrapper for the "\WPGraphQL\Utils\Utils::get_database_id_from_id" function.
	 *
	 * @return int
	 */
	public function toDatabaseId( $id ): int {
		return \WPGraphQL\Utils\Utils::get_database_id_from_id( $id );
	}

	/**
	 * Assert query was successfull.
	 *
	 * @param array $response Query response.
	 * @return self
	 */
	public function assertQuerySuccessful( array $response ): self {
		$this->response = $response;
		$this->assertArrayHasKey( 'data', $this->response );

		return $this;
	}

	/**
	 * Assert query failed.
	 *
	 * @param array $response Query response.
	 * @return self
	 */
	public function assertQueryFailed( array $response ): self {
		$this->response = $response;
		$this->assertArrayHasKey( 'errors', $this->response );

		return $this;
	}

	/**
	 * Check the expected error message.
	 *
	 * @param string $message Error Message.
	 * @return self
	 */
	public function expectedErrorMessage( string $message ): self {
		$this->assertSame( $message, $this->response['errors'][0]['message'] ?: '' );

		return $this;
	}

	/**
	 * Log response.
	 *
	 * @return self
	 */
	public function debug(): self {
		var_dump( $this->response );

		return $this;
	}

	/**
	 * Check if field, and its content, exists in the response.
	 *
	 * @param string $field Field.
	 * @param mixed  $field_content Field Content.
	 * @return self
	 */
	public function hasField( string $field, $field_content ): self {
		$object = $this->get_field_value_from_response( $field );

		$this->assertEquals( $field_content, $object[ $field ] );

		return $this;
	}

	/**
	 * Check if field does not exist in a response.
	 *
	 * @param string $field Response Field.
	 * @return self
	 */
	public function notHasField( string $field ): self {
		$object = $this->get_field_value_from_response( $field );

		$this->assertTrue( empty( $object[ $field ] ) );

		return $this;
	}

	/**
	 * Get a field value from the response.
	 *
	 * @param string $object_field Object field.
	 * @return mixed
	 */
	protected function get_field_value_from_response( string $object_field ) {
		foreach ( $this->response['data'] as $fields ) {
			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field => $value ) {
				if ( $object_field === $field ) {
					$object = [ $field => $value ];
					break;
				}

				$object = $value;
			}
		}

		return $object ?? '';
	}

	/**
	 * Key to cursor.
	 *
	 * @param int $object_id Object ID.
	 * @return string
	 */
	protected function key_to_cursor( int $object_id ): string {
		return \GraphQLRelay\Connection\ArrayConnection::offsetToCursor( $object_id );
	}

	/**
	 * Check if field, and its content, exists in the first node of an edge.
	 *
	 * @param string $field Field.
	 * @param mixed  $field_content Field Content.
	 * @return self
	 */
	protected function firstEdgeNodeField( string $field, $field_content ): self {
		$edges = current( $this->get_field_value_from_response( 'edges' ) );

		$this->assertEquals( $field_content, $edges[0]['node'][ $field ] ?? '' );

		return $this;
	}

	/**
	 * Check if field, and its content, exists in the first node of an Node.
	 *
	 * @param string $field Field.
	 * @param mixed  $field_content Field Content.
	 * @return self
	 */
	protected function firstNodesNodeField( string $field, $field_content ): self {
		$nodes = current( $this->get_field_value_from_response( 'nodes' ) );

		$this->assertEquals( $field_content, $nodes[0][ $field ] ?? '' );

		return $this;
	}

	/**
	 * Check if Edges exist in a response.
	 *
	 * @return self
	 */
	protected function hasEdges(): self {
		$this->assertTrue( ! empty( current( $this->get_field_value_from_response( 'edges' ) ) ) );

		return $this;
	}

	/**
	 * Check if Edges does not exist in a response.
	 *
	 * @return self
	 */
	protected function notHasEdges(): self {
		$this->assertTrue( empty( current( $this->get_field_value_from_response( 'edges' ) ) ) );

		return $this;
	}

	/**
	 * Check if Nodes exist in a response.
	 *
	 * @return self
	 */
	protected function hasNodes(): self {
		$this->assertTrue(
			! empty( current( $this->get_field_value_from_response( 'nodes' ) ) ),
			'Response with empty `nodes`.'
		);

		return $this;
	}

	/**
	 * Check if Nodes does not exist in a response.
	 *
	 * @return self
	 */
	protected function notHasNodes(): self {
		$this->assertTrue( empty( current( $this->get_field_value_from_response( 'nodes' ) ) ) );

		return $this;
	}

	/**
	 * Does query has a next page?
	 *
	 * @return self
	 */
	protected function hasNextPage(): self {
		$page_info = current( $this->get_field_value_from_response( 'pageInfo' ) );

		$this->assertTrue( $page_info['hasNextPage'] );

		return $this;
	}

	/**
	 * Does query not have a next page?
	 *
	 * @return self
	 */
	protected function notHasNextPage(): self {
		$page_info = current( $this->get_field_value_from_response( 'pageInfo' ) );

		$this->assertFalse( $page_info['hasNextPage'] );

		return $this;
	}

	/**
	 * Does query has a previous page?
	 *
	 * @return self
	 */
	protected function hasPreviousPage(): self {
		$page_info = current( $this->get_field_value_from_response( 'pageInfo' ) );

		$this->assertTrue( (bool) $page_info['hasPreviousPage'] ?? false );

		return $this;
	}

	/**
	 * Does query not have a previous page?
	 *
	 * @return self
	 */
	protected function notHasPreviousPage(): self {
		$page_info = current( $this->get_field_value_from_response( 'pageInfo' ) );

		$this->assertFalse( (bool) $page_info['hasPreviousPage'] ?? true );

		return $this;
	}

	/**
	 * Create message/thread object.
	 *
	 * @param array $args Arguments.
	 * @return BP_Messages_Message
	 */
	protected function create_thread_object( array $args = [] ): BP_Messages_Message {
		return $this->bp_factory->message->create_and_get(
			wp_parse_args(
				$args,
				[
					'sender_id'  => $this->admin,
					'recipients' => [ $this->random_user ],
					'subject'    => 'Thread  Subject',
					'content'    => 'Foo',
				],
			)
		);
	}

	/**
	 * Populate group with invites.
	 *
	 * @param int[]    $users      Array of user ids.
	 * @param int|null $group_id   Optional. Group ID.
	 * @param int|null $inviter_id Optional. Inviter ID.
	 */
	protected function populate_group_with_invites( $users, $group_id = null, $inviter_id = null ): void {
		foreach ( $users as $user_id ) {
			groups_invite_user(
				[
					'user_id'     => $user_id,
					'group_id'    => $group_id ?? $this->group,
					'inviter_id'  => $inviter_id ?? $this->user_id,
					'send_invite' => 1,
				]
			);
		}
	}

	/**
	 * Set current user.
	 *
	 * @param int $user_id ID of the user to set as current/active.
	 */
	protected function set_user( int $user_id = 0 ): void {

		if ( ! empty( $user_id ) ) {
			$this->bp->set_current_user( $user_id );
			return;
		}

		if ( is_multisite() ) {
			$this->bp->grant_super_admin( $this->user_id );
			$this->bp->set_current_user( $this->user_id );
		} else {
			$this->bp->set_current_user( $this->admin );
		}
	}

	/**
	 * Create group id.
	 *
	 * @param array $args Arguments.
	 * @return int
	 */
	protected function create_group_id( array $args = [] ): int {
		return $this->bp_factory->group->create(
			wp_parse_args(
				$args,
				[
					'creator_id'  => $this->admin,
					'description' => 'Group Description',
					'name'        => 'Group Test',
					'slug'        => 'group-test',
				],
			)
		);
	}

	/**
	 * Create activity id.
	 *
	 * @param array $args Arguments.
	 * @return int
	 */
	protected function create_activity_id( array $args = [] ): int {
		return $this->bp_factory->activity->create(
			wp_parse_args(
				$args,
				[ 'user_id' => $this->admin ],
			)
		);
	}

	/**
	 * Create notification id.
	 *
	 * @param array $args Arguments.
	 * @return int
	 */
	protected function create_notification_id( array $args = [] ): int {
		return $this->bp_factory->notification->create(
			wp_parse_args(
				$args,
				[
					'user_id' => $this->user_id,
					'is_new'  => 1,
				],
			)
		);
	}

	/**
	 * Create signup id.
	 *
	 * @param array $args Arguments.
	 * @return int
	 */
	protected function create_signup_id( array $args = [] ): int {
		return $this->bp_factory->signup->create(
			wp_parse_args(
				$args,
				[
					'user_login'     => 'user' . wp_rand( 1, 20 ),
					'user_email'     => sprintf( 'user%d@example.com', wp_rand( 1, 20 ) ),
					'registered'     => bp_core_current_time(),
					'activation_key' => wp_generate_password( 32, false ),
					'meta'           => [
						'field_1'  => 'Foo Bar',
						'meta1'    => 'meta2',
						'password' => wp_generate_password( 12, false ),
					],
				]
			)
		);
	}

	/**
	 * Delete cover.
	 *
	 * @param string $object Object.
	 * @param int    $objectId Object ID.
	 * @return array
	 */
	protected function delete_cover( string $object, int $objectId ): array {
		$query = '
			mutation deleteCoverTest(
				$clientMutationId: String!
				$object: AttachmentCoverEnum!
				$objectId: Int!
			) {
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
		];

		$operation_name = 'deleteCoverTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Upload avatar mutation.
	 *
	 * @param string $object   Object.
	 * @param int    $objectId Object ID.
	 * @return array
	 */
	protected function upload_avatar( string $object, int $objectId ): array {
		$query = '
			mutation uploadAvatarTest(
				$clientMutationId: String!
				$file: Upload!
				$object: AttachmentAvatarEnum!
				$objectId: Int!
			) {
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
			'file'             => [
				'fileName' => $this->image_file,
				'mimeType' => 'IMAGE_JPEG',
			],
		];

		$operation_name = 'uploadAvatarTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Upload cover mutation.
	 *
	 * @param string $object   Object.
	 * @param int    $objectId Object ID.
	 * @return array
	 */
	protected function upload_cover( string $object, int $objectId ): array {
		$query = '
			mutation uploadCoverTest(
				$clientMutationId: String!
				$file: Upload!
				$object: AttachmentCoverEnum!
				$objectId: Int!
			) {
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
			'file'             => [
				'fileName' => $this->image_file,
				'mimeType' => 'IMAGE_JPEG',
			],
		];

		$operation_name = 'uploadCoverTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Delete avatar mutation.
	 *
	 * @param string $object   Object name.
	 * @param int    $objectId Object ID.
	 * @return array
	 */
	protected function delete_avatar( string $object, int $objectId ): array {
		$query = '
			mutation deleteAvatarTest(
				$clientMutationId: String!
				$object: AttachmentAvatarEnum!
				$objectId: Int!
			) {
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
		];

		$operation_name = 'deleteAvatarTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Create a friendship object.
	 *
	 * @param int $initiator_id Initiator ID.
	 * @param int $friend_id    Friend ID.
	 * @return int
	 */
	protected function create_friendship_object( int $initiator_id = 0, int $friend_id = 0 ): int {
		if ( empty( $initiator_id ) ) {
			$initiator_id = $this->factory->user->create();
		}

		if ( empty( $friend_id ) ) {
			$friend_id = $this->factory->user->create();
		}

		$friendship                    = new BP_Friends_Friendship();
		$friendship->initiator_user_id = $initiator_id;
		$friendship->friend_user_id    = $friend_id;
		$friendship->is_confirmed      = 0;
		$friendship->date_created      = bp_core_current_time();
		$friendship->save();

		return $friendship->id;
	}

	/**
	 * Get avatar image.
	 *
	 * @param string $size Image size.
	 * @param string $object Object (group/blog/user).
	 * @param int    $item_id Item ID.
	 * @return string
	 */
	protected function get_avatar_image( string $size, string $object, int $item_id ): string {
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

	/**
	 * Get cover image.
	 *
	 * @param string $object Object (members/groups).
	 * @param int    $item_id Item ID.
	 * @return string
	 */
	protected function get_cover_image( string $object, int $item_id ): string {
		return bp_attachments_get_attachment(
			'url',
			[
				'object_dir' => $object,
				'item_id'    => $item_id,
			]
		);
	}

	/**
	 * Copy file.
	 *
	 * @source https://core.trac.wordpress.org/browser/tags/5.9/src/wp-admin/includes/file.php#L979
	 */
	public function copy_file( $return, $file, $new_file ) {
		return @copy( $file['tmp_name'], $new_file );
	}
}
