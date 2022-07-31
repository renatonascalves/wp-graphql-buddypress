<?php

/**
 * Test_Group_updateGroup_Mutation Class.
 *
 * @group groups
 */
class Test_Group_updateGroup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_update_group() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group( [ 'name' => 'Updated Group' ] ) )
			->hasField( 'name', 'Updated Group' );
	}

	public function test_update_group_invalid_group_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_group( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_update_group_without_a_logged_in() {
		$this->assertQueryFailed( $this->update_group( [ 'name' => 'Updated Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_without_permission() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_group( [ 'name' => 'Updated Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_regular_group_member_can_not_update_group() {
		// Add user to group.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_group( [ 'name' => 'Regular User Updated Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_banned_group_members_can_not_update() {
		// Add user to group.
		$this->bp->add_user_to_group( $this->random_user, $this->group );

		// Ban member.
		( new BP_Groups_Member( $this->random_user, $this->group ) )->ban();

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_group( [ 'name' => 'Regular User Updated Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_non_member_group_but_admin_site_admin_can_update() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_group( [ 'name' => 'Updated by Site Admin' ] ) )
			->hasField( 'name', 'Updated by Site Admin' );
	}

	public function test_update_group_admins_can_update() {
		// Add user to group as an admin.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->update_group( [ 'name' => 'Updated by Admin' ] ) )
			->hasField( 'name', 'Updated by Admin' );
	}

	public function test_update_group_moderators_can_not_update() {
		// Add user to group as a moderator.
		$this->bp->add_user_to_group( $this->random_user, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_group( [ 'name' => 'Moderator Updated Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_group_with_valid_status() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group( [ 'status' => 'PRIVATE' ] ) )
			->hasField( 'status', 'PRIVATE' );
	}

	public function test_update_group_with_invalid_status() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_group( [ 'status' => 'random-status' ] ) )
			->expectedErrorMessage( 'Variable "$status" got invalid value "random-status"; Expected type GroupStatusEnum.' );
	}

	public function test_update_group_with_new_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'foo'
						]
					]
				]
			);
	}

	public function test_update_group_remove_nonexist_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->remove_group_type() )
			->hasField( 'types', null );
	}

	public function test_update_group_remove_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'foo'
						]
					]
				]
			);

		$this->assertQuerySuccessful( $this->remove_group_type() )
			->hasField( 'types', null );
	}

	public function test_update_group_append_type() {
		$this->bp->set_current_user( $this->user );

		// Add type.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'foo'
						]
					]
				]
			);

		// Append new one.
		$this->assertQuerySuccessful( $this->append_group_type( [ 'appendTypes' => [ 'BAR' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'bar'
						],
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'foo'
						]
					]
				]
			);
	}

	public function test_update_group_overwrite_types() {
		$this->bp->set_current_user( $this->user );

		// Add type.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'foo'
						]
					]
				]
			);

		// This overwrites the old one.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'BAR' ] ] ) )
			->hasField(
				'types',
				[
					'nodes' => [
						[
							'__typename' => 'GroupTypeTerm',
							'name'       => 'bar'
						]
					]
				]
			);
	}

	/**
	 * Update group mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function update_group( array $args = [] ): array {
		$query = '
			mutation updateGroupTest(
				$clientMutationId:String!
				$name:String
				$databaseId:Int
				$status:GroupStatusEnum
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
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

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'          => $this->group,
				'status'           => 'PUBLIC',
				'name'             => 'Group',
			]
		);

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Update group type mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function update_group_type( array $args = [] ): array {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!
				$types:[GroupTypeEnum]
				$databaseId: Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						types: $types
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					group {
						types {
							nodes {
								__typename
								name
							}
						}
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'          => $this->group,
				'types'            => [ 'FOO' ],
			]
		);

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Append group type mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function append_group_type( array $args = [] ): array {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!
				$appendTypes:[GroupTypeEnum]
				$databaseId: Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						appendTypes: $appendTypes
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					group {
						types {
							nodes {
								__typename
								name
							}
						}
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'       => $this->group,
				'appendTypes'      => [],
			]
		);

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}

	/**
	 * Remove group type mutation.
	 *
	 * @return array
	 */
	protected function remove_group_type(): array {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!
				$removeTypes:[GroupTypeEnum]
				$databaseId:Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						removeTypes: $removeTypes
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					group {
						types {
							nodes {
								__typename
								name
							}
						}
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $this->group,
			'removeTypes'      => [ 'FOO' ],
		];

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
