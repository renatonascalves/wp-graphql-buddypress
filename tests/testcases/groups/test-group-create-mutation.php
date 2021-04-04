<?php

use WPGraphQL\Utils\Utils;
/**
 * Test_Groups_Create_Mutation Class.
 *
 * @group groups
 */
class Test_Groups_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_create_group() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->create_group();

		$this->assertQuerySuccessful( $response );

		$group = groups_get_group( $response['data']['createGroup']['group']['groupId'] );

		$this->assertEquals(
			[
				'data' => [
					'createGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group' => [
							'id'               => $this->toRelayId( 'group', $group->id ),
							'groupId'          => $group->id,
							'name'             => 'Group Test',
							'slug'             => 'group-slug',
							'description'      => bp_get_group_description( $group ),
							'status'           => 'PUBLIC',
							'link'             => bp_get_group_permalink( $group ),
							'hasForum'         => false,
							'dateCreated'      => Utils::prepare_date_response(
								$group->date_created,
								$response['data']['createGroup']['group']['dateCreated']
							),
							'parent'           => null,
							'creator'          => [
								'id'     => $this->toRelayId( 'user', $this->admin ),
								'userId' => $this->admin,
							],
							'admins'           => [
								[
									'id'     => $this->toRelayId( 'user', $this->admin ),
									'userId' => $this->admin,
								],
							],
							'mods'             => null,
							'totalMemberCount' => 1,
							'lastActivity'     => Utils::prepare_date_response(
								$group->last_activity,
								$response['data']['createGroup']['group']['lastActivity']
							),
							'types'            => null,
							'attachmentAvatar' => [
								'full'  => $this->get_avatar_image( 'full', 'group', $group->id ),
							],
							'attachmentCover'  => null,
						],
					],
				],
			],
			$response
		);
	}

	/**
	 * Group creation is open by default to regular users.
	 */
	public function test_create_group_as_a_regular_user() {
		$this->bp->set_current_user( $this->user );

		$response = $this->create_group();

		$this->assertQuerySuccessful( $response );
		$this->assertSame( 'Group Test', $response['data']['createGroup']['group']['name'] );
	}

	public function test_create_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->create_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_group_when_disabled() {
		$this->bp->set_current_user( $this->user );

		add_filter( 'bp_user_can_create_groups', '__return_false' );

		$this->assertQueryFailed( $this->create_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_group_with_valid_status() {
		$this->bp->set_current_user( $this->user );

		$response = $this->create_group( [ 'status' => 'PRIVATE' ] );

		$this->assertQuerySuccessful( $response );
		$this->assertSame( 'PRIVATE', $response['data']['createGroup']['group']['status'] );
	}

	public function test_create_group_with_invalid_status() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_group( [ 'status' => 'random-status' ] ) )
			->expectedErrorMessage( 'Variable "$status" got invalid value "random-status"; Expected type GroupStatusEnum.' );
	}

	public function test_create_group_with_null_name_field() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_group( [ 'name' => null ] ) )
			->expectedErrorMessage( 'Variable "$name" of non-null type "String!" must not be null.' );
	}

	public function test_create_group_with_empty_name_field() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_group( [ 'name' => '' ] ) )
			->expectedErrorMessage( 'Please, enter the name of the group.' );
	}

	public function test_create_group_with_types() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->create_group_type( [ 'types' => [ 'FOO' ] ] );

		$this->assertQuerySuccessful( $response );
		$this->assertTrue( in_array( 'FOO', $response['data']['createGroup']['group']['types'], true ) );
	}

	public function test_create_group_with_invalid_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_group_type( [ 'types' => [ 'DOOR' ] ] ) )
			->expectedErrorMessage( 'Variable "$types" got invalid value ["DOOR"]; Expected type GroupTypeEnum at value[0]; did you mean FOO?' );
	}

	/**
	 * Create group mutation helper method.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_group( $args = [] ) {
		$query = '
			mutation createGroupTest(
				$clientMutationId:String!,
				$name:String!,
				$description:String,
				$slug:String,
				$status:GroupStatusEnum
			) {
				createGroup(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						slug: $slug
						description: $description
						status: $status
					}
				)
				{
					clientMutationId
					group {
						id
						groupId
						name
						slug
						description
						status
						link
						hasForum
						dateCreated
						parent {
							name
						}
						creator {
							id
							userId
						}
						admins {
							id
							userId
						}
						mods {
							id
							userId
						}
						totalMemberCount
						lastActivity
						types
						attachmentAvatar {
							full
						}
						attachmentCover {
							full
						}
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'slug'             => 'group-slug',
				'description'      => 'Group Description',
				'status'           => 'PUBLIC',
			]
		);

		$operation_name = 'createGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Create group mutation with type helper method.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_group_type( $args = [] ) {
		$query = '
			mutation createGroupTest(
				$clientMutationId:String!,
				$name:String!,
				$types:[GroupTypeEnum]
			) {
				createGroup(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						types: $types
					}
				)
				{
					clientMutationId
					group {
						types
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'Group Test',
				'types'            => [],
			]
		);

		$operation_name = 'createGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
