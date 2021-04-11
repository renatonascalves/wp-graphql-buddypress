<?php
/**
 * WPGraphQL BuddyPress test utility functions/assertions.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

/**
 * WPGraphQL_BuddyPress_UnitTestCase Class.
 */
class WPGraphQL_BuddyPress_UnitTestCase extends WP_UnitTestCase {

	/**
	 * Query response.
	 *
	 * @var array
	 */
	public $response;

	public $bp_factory;
	public $bp;
	public $client_mutation_id;
	public $image_file;

	/**
	 * Regular user.
	 *
	 * @var WP_User
	 */
	public $user;

	/**
	 * Random regular user.
	 *
	 * @var WP_User
	 */
	public $random_user;

	/**
	 * Admin user.
	 *
	 * @var WP_User
	 */
	public $admin;

	/**
	 * Group object.
	 *
	 * @var BP_Groups_Group
	 */
	public $group;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->client_mutation_id = 'someUniqueId';
		$this->image_file         = __DIR__ . '/assets/test-image.jpeg';
		$this->user               = $this->bp_factory->user->create();
		$this->random_user        = $this->bp_factory->user->create();
		$this->admin              = $this->bp_factory->user->create( [ 'role' => 'administrator' ] );
		$this->group              = $this->bp_factory->group->create(
			[
				'name'        => 'Group Test',
				'description' => 'Group Description',
				'creator_id'  => $this->user,
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
	 * @todo add per query debug log.
	 *
	 * @return array
	 */
	public function graphql() {
		return graphql( ...func_get_args() );
	}

	/**
	 * Wrapper for the "GraphQLRelay\Relay::toGlobalId()" function.
	 *
	 * @return string
	 */
	public function toRelayId() {
		return \GraphQLRelay\Relay::toGlobalId( ...func_get_args() );
	}

	/**
	 * Assert query was successfull.
	 *
	 * @param array $response Query response.
	 * @return self
	 */
	public function assertQuerySuccessful( array $response ) {
		$this->response = $response;
		$this->assertIsArray( $this->response );
		$this->assertNotEmpty( $this->response );
		$this->assertArrayHasKey( 'data', $this->response );

		return $this;
	}

	/**
	 * Assert query failed.
	 *
	 * @param array $response Query response.
	 * @return self
	 */
	public function assertQueryFailed( array $response ) {
		$this->response = $response;
		$this->assertIsArray( $this->response );
		$this->assertNotEmpty( $this->response );
		$this->assertArrayHasKey( 'errors', $this->response );

		return $this;
	}

	/**
	 * Check the expected error message.
	 *
	 * @param string $message Error Message.
	 * @return self
	 */
	public function expectedErrorMessage( string $message ) {
		$this->assertNotEmpty( $this->response );
		$this->assertSame( $message, $this->response['errors'][0]['message'] );

		return $this;
	}

	/**
	 * Log response.
	 *
	 * @return self
	 */
	public function debug() {
		var_dump( $this->response );

		return $this;
	}

	/**
	 * Check if field exists in the response.
	 *
	 * @param string $field Field.
	 * @param mixed  $field_content Field Content.
	 * @return self
	 */
	public function hasField( string $field, $field_content ) {
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
	public function notHasField( string $field ) {
		$object = $this->get_field_value_from_response( $field );

		$this->assertTrue( empty( $object[ $field ] ) );

		return $this;
	}

	/**
	 * Get a field value from response.
	 *
	 * @param string $object_field Object field.
	 * @return mixed
	 */
	protected function get_field_value_from_response( string $object_field ) {
		foreach( $this->response['data'] as $operationName ) {
			foreach( $operationName as $field => $value ) {
				if ($object_field === $field) {
					$object = [ $field => $value ];
					break;
				}

				$object = $value;
			}
		}

		return $object ?? '';
	}

	/**
	 * Create group object.
	 *
	 * @param array $args Arguments.
	 * @return int
	 */
	protected function create_group_object( array $args = [] ): int {
		return $this->bp_factory->group->create(
			array_merge(
				[
					'slug'         => 'group-test',
					'name'         => 'Group Test',
					'description'  => 'Group Description',
					'creator_id'   => $this->admin,
				],
				$args
			)
		);
	}

	protected function create_xprofile_field( $xprofile_group_id = null ) {
		$query = '
			mutation createXProfileFieldTest(
				$clientMutationId:String!,
				$name:String!,
				$description:String,
				$groupId:Int!,
				$type:XProfileFieldTypesEnum!,
			) {
				createXProfileField(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						description: $description
						groupId: $groupId
						type: $type
					}
				)
				{
					clientMutationId
					field {
						name
						description
					}
				}
			}
        ';

		$variables = wp_json_encode(
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Field Test',
                'description'      => 'Description',
                'groupId'          => $xprofile_group_id ?? (int) $this->xprofile_group_id,
                'type'             => 'TEXTBOX',
			]
        );

		$operation_name = 'createXProfileFieldTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_xprofile_field( $field_id = null ) {
		$query = '
			mutation deleteXProfileFieldTest( $clientMutationId: String!, $fieldId: Int ) {
				deleteXProfileField(
					input: {
						clientMutationId: $clientMutationId
						fieldId: $fieldId
					}
				)
				{
					clientMutationId
					deleted
					field {
						fieldId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'fieldId'          => $field_id,
		];

		$operation_name = 'deleteXProfileFieldTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function update_xprofile_field( $field_id = null, $name = null ) {
		$query = '
			mutation updateXProfileFieldTest( $clientMutationId: String!, $fieldId: Int, $name: String ) {
				updateXProfileField(
					input: {
						clientMutationId: $clientMutationId
						fieldId: $fieldId
                        name: $name
					}
				)
				{
					clientMutationId
					field {
						fieldId
                        name
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'fieldId'          => $field_id,
            'name'             => $name ?? 'Updated XProfile Group',
		];

		$operation_name = 'updateXProfileFieldTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function xprofileFieldsQuery( $variables = [] ) {
		$query = 'query xprofileFieldsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToXProfileFieldsConnectionWhereArgs) {
			xprofileFields( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						id
						name
					}
				}
				nodes {
					id
				}
			}
		}';

		$operation_name = 'xprofileFieldsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function xprofileGroupsQuery( $variables = [] ) {
		$query = 'query xprofileGroupsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToXProfileGroupConnectionWhereArgs) {
			xprofileGroups( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						id
						groupId
						name
					}
				}
				nodes {
				  id
				  groupId
				}
			}
		}';

		$operation_name = 'xprofileGroupsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function create_xprofile_group( $name = null, $desc = null ) {
		$query = '
			mutation createXProfileGroupTest(
				$clientMutationId:String!,
				$name:String!,
				$description:String
			) {
				createXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						description: $description
					}
				)
				{
					clientMutationId
					group {
						name
						description
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'name'             => $name ?? 'XProfile Group Test',
			'description'      => $desc ?? 'Description',
		];

		$operation_name = 'createXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_xprofile_group( $xprofile_group_id = null ) {
		$query = '
            mutation deleteXProfileGroupTest( $clientMutationId: String!, $groupId: Int ) {
                deleteXProfileGroup(
                    input: {
                        clientMutationId: $clientMutationId
                        groupId: $groupId
                    }
                )
                {
                    clientMutationId
                    deleted
                }
            }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $xprofile_group_id ?? $this->xprofile_group_id,
		];

		$operation_name = 'deleteXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function update_xprofile_group( $group_id = 0, $name = null ) {
		$query = '
			mutation updateXProfileGroupTest( $clientMutationId: String!, $groupId: Int, $name: String ) {
				updateXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
						groupId: $groupId
						name: $name
					}
				)
				{
					clientMutationId
					group {
						name
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id,
			'name'             => $name ?? 'Updated XProfile Group',
		];

		$operation_name = 'updateXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_cover( $object, $objectId ) {
		$query = '
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

		$operation_name = 'deleteCoverTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function upload_avatar( $object, $objectId ) {
		$query = '
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
			'file'              => [
				'fileName'  => $this->image_file,
				'mimeType' => 'IMAGE_JPEG'
			],
		];

		$operation_name = 'uploadAvatarTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function upload_cover( $object, $objectId ) {
		$query = '
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
			'file'              => [
				'fileName'  => $this->image_file,
				'mimeType' => 'IMAGE_JPEG'
			],
		];

		$operation_name = 'uploadCoverTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_avatar( $object, $objectId ) {
		$query = '
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
	 * @param int $user_1 Initiator ID.
	 * @param int $user_2 Friend ID.
	 * @return int
	 */
	protected function create_friendship_object( $user_1 = 0, $user_2 = 0 ): int {
		if ( empty( $user_1 ) ) {
			$user_1 = $this->factory->user->create();
		}

		if ( empty( $user_2 ) ) {
			$user_2 = $this->factory->user->create();
		}

		$friendship                    = new BP_Friends_Friendship();
		$friendship->initiator_user_id = $user_1;
		$friendship->friend_user_id    = $user_2;
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
	 * @param int $item_id Item ID.
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
	 * @param int $item_id Item ID.
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

	public function copy_file( $return = null, $file, $new_file ) {
		return @copy( $file['tmp_name'], $new_file );
	}
}
