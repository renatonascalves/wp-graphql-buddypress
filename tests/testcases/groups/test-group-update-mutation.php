<?php

/**
 * Test_Group_Update_Mutation Class.
 *
 * @group groups
 */
class Test_Group_Update_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_update_group() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group( [ 'name' => 'Updated Group' ] ) )
			->hasField( 'name', 'Updated Group' );
	}

	public function test_update_group_invalid_group_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_group( [ 'groupId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
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

	public function test_update_group_moderators_can_update() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_group( [ 'name' => 'Updated' ] ) )
			->hasField( 'name', 'Updated' );
	}

	public function test_update_group_with_valid_status() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group( [  'status' => 'PRIVATE' ] ) )
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
			->hasField( 'types', [ 'FOO' ] );
	}

	public function test_update_group_remove_nonexist_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->remove_group_type() )
			->hasField( 'types', null );
	}

	public function test_update_group_remove_type() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField( 'types', [ 'FOO' ] );

		$this->assertQuerySuccessful( $this->remove_group_type() )
			->hasField( 'types', null );
	}

	public function test_update_group_append_type() {
		$this->bp->set_current_user( $this->user );

		// Add type.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField( 'types', [ 'FOO' ] );

		// Append new one.
		$this->assertQuerySuccessful( $this->append_group_type( [ 'appendTypes' => [ 'BAR' ] ] ) )
			->hasField( 'types', [ 'BAR', 'FOO' ] );
	}

	public function test_update_group_overwrite_types() {
		$this->bp->set_current_user( $this->user );

		// Add type.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'FOO' ] ] ) )
			->hasField( 'types', [ 'FOO' ] );

		// This overwrites the old one.
		$this->assertQuerySuccessful( $this->update_group_type( [ 'types' => [ 'BAR' ] ] ) )
			->hasField( 'types', [ 'BAR' ] );
	}

	/**
	 * Update group mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function update_group( $args = [] ) {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!,
				$name: String,
				$groupId: Int,
				$status:GroupStatusEnum
			) {
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

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'groupId'          => $this->group,
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
	protected function update_group_type( $args = [] ) {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!,
				$types:[GroupTypeEnum],
				$groupId: Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						types: $types
						groupId: $groupId
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
				'groupId'          => $this->group,
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
	protected function append_group_type( $args = [] ) {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!,
				$appendTypes:[GroupTypeEnum],
				$groupId: Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						appendTypes: $appendTypes
						groupId: $groupId
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
				'groupId'          => $this->group,
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
	protected function remove_group_type() {
		$query = '
			mutation updateGroupTest(
				$clientMutationId: String!,
				$removeTypes:[GroupTypeEnum],
				$groupId: Int
			) {
				updateGroup(
					input: {
						clientMutationId: $clientMutationId
						removeTypes: $removeTypes
						groupId: $groupId
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

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'groupId'          => $this->group,
			'removeTypes'      => [ 'FOO' ],
		];

		$operation_name = 'updateGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
