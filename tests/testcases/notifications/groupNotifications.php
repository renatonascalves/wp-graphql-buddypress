<?php

/**
 * Test_Notification_groupNotificationsQuery_Query Class.
 *
 * @group groups
 * @group notification
 */
class Test_Notifications_groupNotificationsQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_group_notifications() {
		$u  = $this->bp_factory->user->create();
		$g  = $this->bp_factory->group->create();
		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u ] );

		// Make user group admin.
		$this->bp->add_user_to_group( $u, $g );

		$this->bp->set_current_user( $u );

		$response = $this->groupNotificationsQuery( [ 'id' => $g ] );

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );
	}

	public function test_group_mod_can_not_get_group_notifications() {
		$u1  = $this->bp_factory->user->create();
		$u2  = $this->bp_factory->user->create();
		$g   = $this->bp_factory->group->create();

		$this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u1 ] );
		$this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u1 ] );

		$this->bp->add_user_to_group( $u1, $g );

		// Make user group moderator.
		$this->bp->add_user_to_group( $u2, $g, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u2 );

		$response = $this->groupNotificationsQuery( [ 'id' => $g ] );

		$this->assertQuerySuccessful( $response );

		$nodes = $response['data']['groupBy']['notifications']['nodes'];

		$this->assertEmpty( $nodes );
		$this->assertCount( 0, $nodes );
	}

	public function test_get_new_group_notifications_only() {
		$u  = $this->bp_factory->user->create();
		$g  = $this->bp_factory->group->create();
		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => true, 'component_name' => 'messages', 'user_id' => $u ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => false, 'user_id' => $u ] );
		$n3 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => true, 'component_name' => 'activity', 'user_id' => $u ] );
		$this->create_notification_id( [ 'item_id' => $g, 'is_new' => false, 'component_name' => 'activity', 'user_id' => $u ] );

		// Make user group admin.
		$this->bp->add_user_to_group( $u, $g, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'isNew' => true ] ] );

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n3, $ids, true ) );
		$this->assertFalse( in_array( $n2, $ids, true ) );
	}

	public function test_group_member_can_get_his_notifications() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$g  = $this->bp_factory->group->create();

		$this->bp->add_user_to_group( $u1, $g );
		$this->bp->add_user_to_group( $u2, $g );

		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => true, 'component_name' => 'messages', 'user_id' => $u1 ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => true, 'component_name' => 'activity', 'user_id' => $u1 ] );
		$n3 = $this->create_notification_id( [ 'item_id' => $g, 'is_new' => true, 'component_name' => 'activity', 'user_id' => $u2 ] );

		$this->bp->set_current_user( $u1 );

		$response = $this->groupNotificationsQuery( [ 'id' => $g ] );

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );
		$this->assertFalse( in_array( $n3, $ids, true ) );
	}

	public function test_admin_can_get_group_notifications_from_multiple_users() {
		$g  = $this->bp_factory->group->create();
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u1 ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u2 ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'userIds' => [ $u1, $u2 ] ] ] );

		$this->assertQuerySuccessful( $response );

		$ids = wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );

		// Check the users.
		$user_ids = wp_list_pluck( wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'user' ), 'databaseId' );

		$this->assertCount( 2, $user_ids );
		$this->assertEqualSets( [ $u1, $u2 ], $user_ids );
		$this->assertFalse( in_array( $this->admin, $ids, true ) );
	}

	public function test_non_group_member_can_not_get_group_members_notifications() {
		$g  = $this->bp_factory->group->create();
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u1, $g );
		$this->bp->add_user_to_group( $u2, $g );

		$this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u1 ] );
		$this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u2 ] );

		$this->bp->set_current_user( $u3 );

		$response = $this->groupNotificationsQuery( [ 'id' => $g ] );

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy']['notifications']['nodes'] );

		// Even passing another user ID directly won't return their notifications.
		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'userIds' => [ $u1 ] ] ] );

		$this->assertQuerySuccessful( $response );
		$this->assertEmpty( $response['data']['groupBy']['notifications']['nodes'] );
	}

	public function test_get_group_notifications_sorted() {
		$g = $this->bp_factory->group->create();
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $g );

		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u ] );

		$this->bp->set_current_user( $u );

		// ASC.
		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'order' => 'ASC' ] ] );

		$this->assertQuerySuccessful( $response );

		$this->assertSame(
			[ $n1, $n2 ],
			wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' )
		);

		// DESC.
		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'order' => 'DESC' ] ] );

		$this->assertQuerySuccessful( $response );

		$this->assertSame(
			[ $n2, $n1 ],
			wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' )
		);
	}

	public function test_get_group_notifications_ordered_by_component_name() {
		$g = $this->bp_factory->group->create();
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $g );

		$n1 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'messages', 'user_id' => $u ] );
		$n2 = $this->create_notification_id( [ 'item_id' => $g, 'component_name' => 'activity', 'user_id' => $u ] );

		$this->bp->set_current_user( $u );

		$response = $this->groupNotificationsQuery( [ 'id' => $g, 'where' => [ 'orderBy' => 'COMPONENT_NAME' ] ] );

		$this->assertQuerySuccessful( $response );

		$this->assertSame(
			[ $n2, $n1 ],
			wp_list_pluck( $response['data']['groupBy']['notifications']['nodes'], 'databaseId' )
		);
	}

	/**
	 * Group Notifications query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function groupNotificationsQuery( array $variables = [] ): array {
		$query = 'query groupNotificationsQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:GroupToNotificationConnectionWhereArgs
			$id:Int
		) {
			groupBy(groupId: $id) {
				id
				databaseId
				notifications(
					first:$first
					last:$last
					after:$after
					before:$before
					where:$where
				) {
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
							databaseId
						}
					}
					nodes {
						id
						databaseId
						user {
							__typename
							databaseId
						}
					}
				}
			}
		}';

		$operation_name = 'groupNotificationsQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
