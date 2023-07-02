<?php

/**
 * Test_Activity_deleteActivity_Mutation Class.
 *
 * @group activity
 */
class Test_Activity_deleteActivity_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_activity_creator_can_delete() {
		$a = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_activity( $a ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $a );
	}

	public function test_admin_can_delete_activity_comment() {
		$a = $this->create_activity_id();
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_activity( $c ) )
			->hasField( 'deleted', true )
			->hasField( 'primaryItemId', $a )
			->hasField( 'parentDatabaseId', $a )
			->hasField( 'databaseId', $c );
	}

	public function test_comment_creator_delete_activity_comment() {
		$a = $this->create_activity_id();
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->delete_activity( $c ) )
			->hasField( 'deleted', true )
			->hasField( 'primaryItemId', $a )
			->hasField( 'parentDatabaseId', $a )
			->hasField( 'databaseId', $c );
	}

	public function test_activity_creator_can_not_delete_activity_comment() {
		$a = $this->create_activity_id( [ 'user_id' => $this->random_user ] );
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_activity( $c ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_activity_with_invalid_activity_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_activity( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This activity does not exist.' );
	}

	public function test_delete_activity_user_not_logged_in() {
		$this->assertQueryFailed( $this->delete_activity( $this->create_activity_id() ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_activity_user_without_permission() {
		$a = $this->create_activity_id();

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_activity( $a ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete activity mutation.
	 *
	 * @param int|null $activity_id Activity ID.
	 * @return array
	 */
	protected function delete_activity( $activity_id = null ): array {
		$query = '
			mutation deleteActivityTest(
				$clientMutationId:String!
				$databaseId:Int
			) {
				deleteActivity(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					deleted
					activity {
						id
						databaseId
						parentDatabaseId
						primaryItemId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $activity_id,
		];

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
