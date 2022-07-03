<?php

/**
 * Test_Notification_deleteNotification_Mutation Class.
 *
 * @group notification
 */
class Test_Notification_deleteNotification_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_notification_delete() {
		$n = $this->create_notification_id( [ 'component_name' => 'messages', 'user_id' => $this->random_user ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->delete_notification( $n ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $n );
	}

	public function test_admin_can_delete_notification() {
		$n = $this->create_notification_id( [ 'component_name' => 'messages', 'user_id' => $this->random_user ] );

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->delete_notification( $n ) )
			->hasField( 'deleted', true )
			->hasField( 'databaseId', $n );
	}

	public function test_delete_notification_with_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_notification( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This notification does not exist.' );
	}

	public function test_delete_notification_user_unauthenticated() {
		$u = $this->bp_factory->user->create();
		$n = $this->create_notification_id( [ 'component_name' => 'messages', 'user_id' => $u ] );

		$this->assertQueryFailed( $this->delete_notification( $n ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_delete_notification_user_without_permission() {
		$u = $this->bp_factory->user->create();
		$n = $this->create_notification_id( [ 'component_name' => 'messages', 'user_id' => $u ] );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_notification( $n ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete notification mutation.
	 *
	 * @param int|null $notification_id Notification ID.
	 * @return array
	 */
	protected function delete_notification( $notification_id = null ): array {
		$query = '
			mutation deleteNotificationTest(
				$clientMutationId:String!
				$databaseId:Int
			) {
				deleteNotification(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					deleted
					notification {
						id
						databaseId
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $notification_id,
		];

		$operation_name = 'deleteNotificationTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
