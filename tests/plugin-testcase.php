<?php
/**
 * WPGraphQL BuddyPress test utility functions/assertions.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

/**
 * Traits
 */
class WPGraphQL_BuddyPress_UnitTestCase extends WP_UnitTestCase {

	public $response;

	/**
	 * Wrapper for the "graphql()" function.
	 *
	 * @return array
	 */
	public function graphql(): array {
		$results = graphql( ...func_get_args() );

		return $results;
	}

	/**
	 * Wrapper for the "GraphQLRelay\Relay::toGlobalId()" function.
	 *
	 * @return string
	 */
	public function toRelayId() {
		return \GraphQLRelay\Relay::toGlobalId( ...func_get_args() );
	}

	public function assertQuerySuccessful( $response ) {
		$this->response = $response;
		$this->assertNotEmpty( $response );
		$this->assertQuerySuccessful( $response );
		$this->assertTrue( ! empty( $response['data'] ) );

		return $this;
	}

	public function assertQueryFailed( $response ) {
		$this->response = $response;
		$this->assertArrayHasKey( 'errors', $response );

		return $this;
	}

	public function expectedErrorMessage( $message ) {
		$this->assertSame( $message, $this->response['errors'][0]['message'] );

		return $this;
	}

	protected function membersQuery( $variables = [] ) {
		$query = 'query membersQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToMembersConnectionWhereArgs) {
			members( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						userId
					}
				}
				nodes {
					userId
				}
			}
		}';

		$operation_name = 'membersQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_member( $u = 0 ) {
		$query = '
			mutation deleteUserTest( $clientMutationId: String!, $id: ID! ) {
				deleteUser(
					input: {
						clientMutationId: $clientMutationId
						id: $id
					}
				) {
					clientMutationId
					deletedId
					user {
						userId
						id
					}
				}
			}
        ';

		$variables = [
			'id'               => $this->toRelayId( 'user', $u ),
			'clientMutationId' => $this->client_mutation_id,
		];

		$operation_name = 'deleteUserTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function groupsQuery( $variables = [] ) {
		$query = 'query groupsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToGroupConnectionWhereArgs) {
			groups( first:$first last:$last after:$after before:$before where:$where ) {
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

		$operation_name = 'groupsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function update_group( $status = null, $group_id = null, $name = null ) {
		$query = '
			mutation updateGroupTest( $clientMutationId: String!, $name: String, $groupId: Int, $status:GroupStatusEnum ) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						groupId: $groupId
						name: $name
						status: $status
					}
				)
				{
					clientMutationId
					group {
						name
						status
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $group_id ?? $this->group_id,
			'status'           => $status ?? 'PUBLIC',
			'name'             => $name ?? 'Group',
		];

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function create_group( $slug = null ) {
		$query = '
			mutation createGroupTest(
				$clientMutationId:String!,
				$name:String!
				$slug:String
				$status:GroupStatusEnum
			) {
				createGroup(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						slug: $slug
						status: $status
					}
				)
				{
					clientMutationId
					group {
						name
						slug
						status
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'name'             => 'Group Test',
			'slug'             => 'group-slug',
			'status'           => $slug ?? 'PUBLIC',
		];

		$operation_name = 'createGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_group( $group_id = null ) {
		$query = '
			mutation deleteGroupTest( $clientMutationId: String!, $groupId: Int ) {
				deleteGroup(
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
			'groupId'          => $group_id ?? $this->group_id,
		];

		$operation_name = 'deleteGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
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

	protected function blogsQuery( $variables = [] ) {
		$query = 'query blogsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToBlogConnectionWhereArgs) {
			blogs( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						blogId
					}
				}
				nodes {
					blogId
				}
			}
		}';

		$operation_name = 'blogsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_cover( string $object, int $objectId ): array {
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'object'           => $object,
			'objectId'         => $objectId,
		];

		$operation_name = 'deleteCoverTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function upload_cover( string $object, int $objectId ) {
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

	protected function upload_avatar( string $object, int $objectId ): array {
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

	protected function delete_avatar( string $object, int $objectId ) {
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

	protected function create_friendship( $initiator, $friend ) {
		$query = '
			mutation createFriendshipTest( $clientMutationId: String!, $initiatorId: Int, $friendId: Int! ) {
				createFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
					}
				)
				{
					clientMutationId
					friendship {
						isConfirmed
						initiator {
							userId
						}
						friend {
							userId
						}
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'initiatorId'      => $initiator,
			'friendId'         => $friend,
		];

		$operation_name = 'createFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function delete_friendship( $initiator, $friend ) {
		$query = '
			mutation deleteFriendshipTest( $clientMutationId: String!, $initiatorId: Int!, $friendId: Int! ) {
				deleteFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
					}
				)
				{
					clientMutationId
					deleted
					friendship {
						initiator {
							userId
						}
						friend {
							userId
						}
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'initiatorId'      => $initiator,
			'friendId'         => $friend,
		];

		$operation_name = 'deleteFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	protected function update_friendship( $initiator, $friend ) {
		$query = '
			mutation updateFriendshipTest( $clientMutationId: String!, $initiatorId: Int!, $friendId: Int! ) {
				updateFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
					}
				)
				{
					clientMutationId
					friendship {
						isConfirmed
						initiator {
							userId
						}
						friend {
							userId
						}
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'initiatorId'      => $initiator,
			'friendId'         => $friend,
		];

		$operation_name = 'updateFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) ) ;
	}
}
