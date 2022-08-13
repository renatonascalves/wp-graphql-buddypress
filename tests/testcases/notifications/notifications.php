<?php

/**
 * Test_Notification_notificationQuery_Query Class.
 *
 * @group notification
 */
class Test_Notifications_notificationQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_get_notifications_authenticated() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->notificationQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );
	}

	public function test_get_new_notifications_only() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'is_new'         => false,
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'groups',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->notificationQuery( [ 'where' => [ 'isNew' => true ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n3, $ids, true ) );
		$this->assertFalse( in_array( $n2, $ids, true ) );
	}

	public function test_get_notifications_by_component_name() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'is_new'         => false,
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'groups',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->notificationQuery( [ 'where' => [ 'componentName' => [ 'GROUPS' ] ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 1, $ids );
		$this->assertTrue( in_array( $n3, $ids, true ) );
		$this->assertFalse( in_array( $n1, $ids, true ) );
		$this->assertFalse( in_array( $n2, $ids, true ) );
	}

	public function test_get_notifications_by_item_id() {
		$g  = $this->bp_factory->group->create();
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'item_id'        => $g,
				'component_name' => 'groups',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'item_id'        => $g,
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->notificationQuery( [ 'where' => [ 'itemIds' => [ $g ] ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );
		$this->assertFalse( in_array( $n3, $ids, true ) );
	}

	public function test_get_notifications_unauthenticated() {
		$this->assertQuerySuccessful( $this->notificationQuery() )
			->notHasNodes();
	}

	public function test_admin_can_get_notifications_from_multiple_users() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u1,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u2,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$results = $this->notificationQuery( [ 'where' => [ 'userIds' => [ $u1, $u2 ] ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 2, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
		$this->assertTrue( in_array( $n2, $ids, true ) );

		// Check the users.
		$user_ids = wp_list_pluck( wp_list_pluck( $results['data']['notifications']['nodes'], 'user' ), 'databaseId' );

		$this->assertCount( 2, $user_ids );
		$this->assertEqualSets( [ $u1, $u2 ], $user_ids );

		// Pass the user ID directly.
		$results = $this->notificationQuery( [ 'where' => [ 'userIds' => [ $u1 ] ] ] );

		$ids = wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' );

		// Check notifications.
		$this->assertCount( 1, $ids );
		$this->assertTrue( in_array( $n1, $ids, true ) );
	}

	public function test_regular_user_can_not_get_notifications_from_multiple_users() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u1,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u2,
			]
		);

		$this->bp->set_current_user( $u3 );

		$results = $this->notificationQuery( [ 'where' => [ 'userIds' => [ $u1, $u2 ] ] ] );

		$this->assertQuerySuccessful( $results )
			->notHasNodes();

		// Even passing another user ID directly won't return their notifications.
		$this->assertQuerySuccessful( $this->notificationQuery( [ 'where' => [ 'userIds' => [ $u1 ] ] ] ) )
			->notHasNodes();
	}

	public function test_get_notifications_with_invalid_order_type() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->notificationQuery( [ 'where' => [ 'orderBy' => 'random-status' ] ] ) )
			->expectedErrorMessage( 'Variable "$where" got invalid value {"orderBy":"random-status"}; Expected type NotificationOrderByEnum at value.orderBy.' );
	}

	public function test_get_notifications_sorted() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'groups',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		// ASC.
		$this->assertQuerySuccessful( $this->notificationQuery( [ 'where' => [ 'order' => 'ASC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n1 );

		// DESC.
		$this->assertQuerySuccessful( $this->notificationQuery( [ 'where' => [ 'order' => 'DESC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n3 );

		// Default: DESC.
		$this->assertQuerySuccessful( $this->notificationQuery() )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n3 );
	}

	public function test_get_notifications_ordered_by_component_name() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'groups',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->notificationQuery( [ 'where' => [ 'orderBy' => 'COMPONENT_NAME' ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$this->assertSame(
			[ $n3, $n2, $n1 ],
			wp_list_pluck( $results['data']['notifications']['nodes'], 'databaseId' )
		);
	}

	public function test_get_first_notification() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		// The first here is the last one created. The latest notification.
		$this->assertQuerySuccessful(
			$this->notificationQuery(
				[
					'first' => 1,
					'after' => '',
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n2 )
			->hasNextPage();
	}

	public function test_get_notifications_after() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful(
			$this->notificationQuery( [ 'after' => $this->key_to_cursor( $n1 ) ] )
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n2 )
			->hasPreviousPage();
	}

	public function test_get_notifications_before() {
		$u  = $this->bp_factory->user->create();
		$n1 = $this->create_notification_id(
			[
				'component_name' => 'messages',
				'user_id'        => $u,
			]
		);
		$n2 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);
		$n3 = $this->create_notification_id(
			[
				'component_name' => 'activity',
				'user_id'        => $u,
			]
		);

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful(
			$this->notificationQuery(
				[
					'last'   => 1,
					'before' => $this->key_to_cursor( $n2 ),
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $n1 )
			->hasNextPage();
	}

	/**
	 * Notification query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function notificationQuery( array $variables = [] ): array {
		$query = 'query notificationQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToNotificationConnectionWhereArgs
		) {
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
		}';

		$operation_name = 'notificationQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
