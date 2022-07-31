<?php

/**
 * Test_Notification_updateNotification_Mutation Class.
 *
 * @group notification
 */
class Test_Notification_updateNotification_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_notification_update() {
		$n = $this->create_notification_id(
			[
				'is_new'         => false,
				'component_name' => 'messages',
				'user_id'        => $this->random_user,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->update_notification( $n, true ) )
			->hasField( 'isNew', true )
			->hasField( 'databaseId', $n );
	}

	public function test_notification_update_false() {
		$n = $this->create_notification_id(
			[
				'is_new'         => true,
				'component_name' => 'messages',
				'user_id'        => $this->random_user,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->update_notification( $n ) )
			->hasField( 'isNew', false )
			->hasField( 'databaseId', $n );
	}

	public function test_admin_can_update_notification() {
		$n = $this->create_notification_id(
			[
				'is_new'         => false,
				'component_name' => 'messages',
				'user_id'        => $this->random_user,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_notification( $n, true ) )
			->hasField( 'isNew', true )
			->hasField( 'databaseId', $n );
	}

	public function test_update_notification_with_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_notification( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This notification does not exist.' );
	}

	public function test_update_notification_user_unauthenticated() {
		$u = $this->bp_factory->user->create();
		$n = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);

		$this->assertQueryFailed( $this->update_notification( $n ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_notification_user_without_permission() {
		$u = $this->bp_factory->user->create();
		$n = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_notification( $n ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Delete notification mutation.
	 *
	 * @param int|null $notification_id Notification ID.
	 * @param bool     $is_new
	 * @return array
	 */
	protected function update_notification( $notification_id = null, bool $is_new = false ): array {
		$query = '
			mutation updateNotificationTest(
				$clientMutationId:String!
				$databaseId:Int
				$isNew:Boolean!
			) {
				updateNotification(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
						isNew: $isNew
					}
				)
				{
					clientMutationId
					notification {
						databaseId
						isNew
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'databaseId'       => $notification_id,
			'isNew'            => $is_new,
		];

		$operation_name = 'updateNotificationTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
