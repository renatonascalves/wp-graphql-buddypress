<?php
/**
 * Test_Notification_getNotification_Queries Class.
 *
 * @group notification
 */
class Test_Notification_getNotification_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

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
			->hasField( 'user', [ 'databaseId' => $this->user ] )
			->hasField( 'object', [
					'__typename' => 'Group',
					'databaseId' => $group_id,
				]
			);
	}

	public function test_get_group_notification_as_admin() {
		$group_id        = $this->bp_factory->group->create();
		$notification_id = $this->bp_factory->notification->create(
				[
					'component_name' => buddypress()->groups->id,
					'item_id'        => $group_id,
					'user_id'        => $this->admin,
				]
		);

		$this->set_user();

		$this->assertQuerySuccessful( $this->get_a_notification( $notification_id ) )
			->hasField( 'id', $this->toRelayId( 'notification', (string) $notification_id ) )
			->hasField( 'databaseId', $notification_id )
			->hasField( 'primaryItemId', $group_id )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'isNew', true )
			->hasField( 'componentName', 'groups' )
			->hasField( 'componentAction', '' )
			->hasField( 'user', [ 'databaseId' => $this->admin ] )
			->hasField( 'object', [
					'__typename' => 'Group',
					'databaseId' => $group_id,
				]
			);
	}

	public function test_get_activity_notification() {
		$component       = buddypress()->activity->id;
		$activity_id     = $this->bp_factory->activity->create();
		$notification_id = $this->bp_factory->notification->create(
			[
				'component_name' => $component,
				'item_id'        => $activity_id,
				'user_id'        => $this->admin,
			]
		);

		$this->set_user();

		$this->assertQuerySuccessful( $this->get_a_notification( $notification_id ) )
			->hasField( 'id', $this->toRelayId( 'notification', (string) $notification_id ) )
			->hasField( 'databaseId', $notification_id )
			->hasField( 'primaryItemId', $activity_id )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'isNew', true )
			->hasField( 'componentName', $component )
			->hasField( 'componentAction', '' )
			->hasField( 'user', [ 'databaseId' => $this->admin ] )
			->hasField( 'object', [
					'__typename' => 'Activity',
					'databaseId' => $activity_id,
				]
			);
	}

	public function get_blog_notification() {
		$this->skipWithoutMultisite();

		$blog_title = 'The Foo Bar Blog';
		$component  = buddypress()->blogs->id;

		$this->set_user();

		$blog_id = $this->bp_factory->blog->create(
			[ 'title' => $blog_title ]
		);

		$notification_id = $this->bp_factory->notification->create(
			[
				'component_name' => $component,
				'item_id'        => $blog_id,
			]
		);

		$this->assertQuerySuccessful( $this->get_a_notification( $notification_id ) )
			->hasField( 'id', $this->toRelayId( 'notification', (string) $notification_id ) )
			->hasField( 'databaseId', $notification_id )
			->hasField( 'primaryItemId', $blog_id )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'isNew', true )
			->hasField( 'componentName', $component )
			->hasField( 'componentAction', '' )
			->hasField( 'user', [ 'databaseId' => $this->user ] )
			->hasField( 'object', [
					'__typename' => 'Blog',
					'databaseId' => $blog_id,
				]
			);
	}

	public function test_get_notification_with_unauthenticated_user() {
		$group_id        = $this->bp_factory->group->create();
		$notification_id = $this->bp_factory->notification->create(
				[
					'component_name' => buddypress()->groups->id,
					'item_id'        => $group_id,
					'user_id'        => $this->user,
				]
		);

		$response = $this->get_a_notification( $notification_id );

		$this->assertEmpty( $response['data']['notification'] );
	}

	public function test_get_notification_with_unauthorized_user() {
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

		$this->assertEmpty( $response['data']['notification'] );
	}

	public function test_get_notification_with_invalid_id() {
		$this->set_user();

		$this->assertQueryFailed( $this->get_a_notification( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This notification does not exist.' );
	}

	/**
	 * Get a notification.
	 *
	 * @param int|null $notification_id Notification ID.
	 * @param string   $type            Type.
	 * @return array
	 */
	protected function get_a_notification( $notification_id = null, $type = 'DATABASE_ID' ): array {
		$query = "
			query {
				notification(id: {$notification_id}, idType: {$type}) {
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
					object {
						__typename
						... on User {
							databaseId
						}
						... on Group {
							databaseId
						}
						... on Activity {
							databaseId
						}
						... on Blog {
							databaseId
						}
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
