<?php
/**
 * Test_Notification_getNotificationBy_Queries Class.
 *
 * @group notification
 */
class Test_Notification_getNotificationBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_group_notification() {
		$group_id        = $this->bp_factory->group->create();
		$notification_id = $this->bp_factory->notification->create(
				[
					'component_name' => buddypress()->groups->id,
					'item_id'        => $group_id,
					'user_id'        => $this->user,
				]
		);

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_a_notification( $notification_id ) )
			->hasField( 'id', $this->toRelayId( 'notification', (string) $notification_id ) )
			->hasField( 'databaseId', $notification_id )
			->hasField( 'primaryItemId', $group_id )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'isNew', true )
			->hasField( 'componentName', 'groups' )
			->hasField( 'componentAction', '' )
			->hasField( 'user', [ 'databaseId' => $this->user ] );
	}

	public function test_get_activity_notification() {
		$activity_id     = $this->bp_factory->activity->create();
		$notification_id = $this->bp_factory->notification->create(
			[
				'component_name' => buddypress()->activity->id,
				'item_id'        => $activity_id,
				'user_id'        => $this->user,
			]
		);

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_a_notification( $notification_id ) )
			->hasField( 'id', $this->toRelayId( 'notification', (string) $notification_id ) )
			->hasField( 'databaseId', $notification_id )
			->hasField( 'primaryItemId', $activity_id )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'isNew', true )
			->hasField( 'componentName', 'activity' )
			->hasField( 'componentAction', '' )
			->hasField( 'user', [ 'databaseId' => $this->user ] );
	}

	public function test_notification_with_user_not_logged_in() {
		$group_id        = $this->bp_factory->group->create();
		$notification_id = $this->bp_factory->notification->create(
				[
					'component_name' => buddypress()->groups->id,
					'item_id'        => $group_id,
					'user_id'        => $this->user,
				]
		);

		$response = $this->get_a_notification( $notification_id );

		$this->assertEmpty( $response['data']['notificationBy'] );
	}

	public function test_notification_with_unauthorized_user() {
		$group_id        = $this->bp_factory->group->create();
		$notification_id = $this->bp_factory->notification->create(
				[
					'component_name' => buddypress()->groups->id,
					'item_id'        => $group_id,
					'user_id'        => $this->user,
				]
		);

		$this->bp->set_current_user( $this->random_user );

		$response = $this->get_a_notification( $notification_id );

		$this->assertEmpty( $response['data']['notificationBy'] );
	}

	public function test_notification_with_invalid_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->get_a_notification( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This notification does not exist.' );
	}

	/**
	 * Get a notification.
	 *
	 * @param int|null $notification_id Notification ID.
	 * @return array
	 */
	protected function get_a_notification( $notification_id = null ): array {
		$query = "
			query {
				notificationBy(databaseId: {$notification_id}) {
					id
					user {
						databaseId
					}
					databaseId
					primaryItemId
					secondaryItemId
					componentName
					componentAction
					date
					dateGmt
					isNew
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
