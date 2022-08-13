<?php

/**
 * Test_Activity_activityQuery_Query Class.
 *
 * @group activity
 */
class Test_Activity_activityQuery_Query extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_public_activities_authenticated() {
		$a1 = $this->create_activity_id();
		$a2 = $this->create_activity_id();
		$a3 = $this->create_activity_id();

		$this->bp->set_current_user( $this->random_user );

		$results = $this->activityQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$ids = wp_list_pluck( $results['data']['activities']['nodes'], 'databaseId' );

		// Check activities.
		$this->assertTrue( in_array( $a1, $ids, true ) );
		$this->assertTrue( in_array( $a2, $ids, true ) );
		$this->assertTrue( in_array( $a3, $ids, true ) );
	}

	public function test_public_activities_unauthenticated() {
		$a1 = $this->create_activity_id();
		$a2 = $this->create_activity_id();
		$a3 = $this->create_activity_id();
		$a4 = $this->create_activity_id( [ 'hide_sitewide' => true ] );

		$results = $this->activityQuery();

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 3, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		// Check activities.
		$this->assertTrue( in_array( $a1, $ids, true ) );
		$this->assertTrue( in_array( $a2, $ids, true ) );
		$this->assertTrue( in_array( $a3, $ids, true ) );
		$this->assertFalse( in_array( $a4, $ids, true ) );
	}

	public function test_get_hidden_activities_authenticated_and_with_access() {
		$a1 = $this->create_activity_id( [ 'hide_sitewide' => true ] );

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->activityQuery() )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a1 );
	}

	public function test_get_hidden_activities_unauthenticated() {
		$this->create_activity_id( [ 'hide_sitewide' => true ] );

		$this->assertQuerySuccessful( $this->activityQuery() )
			->notHasEdges()
			->notHasNodes();
	}

	public function test_get_spammed_activities() {
		$a1 = $this->create_activity_id( [ 'is_spam' => true ] );
		$a2 = $this->create_activity_id();
		$a3 = $this->create_activity_id( [ 'is_spam' => true ] );

		$this->bp->set_current_user( $this->admin );

		$results = $this->activityQuery( [ 'where' => [ 'status' => 'SPAM_ONLY' ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		// Check activities.
		$this->assertTrue( in_array( $a1, $ids, true ) );
		$this->assertFalse( in_array( $a2, $ids, true ) );
		$this->assertTrue( in_array( $a3, $ids, true ) );

		$results = $this->activityQuery( [ 'where' => [ 'status' => 'HAM_ONLY' ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 1, $nodes );
		$this->assertTrue( in_array( $a2, wp_list_pluck( $nodes, 'databaseId' ), true ) );
	}

	public function test_get_activities_from_multiple_types() {
		$g1 = $this->create_group_id( [ 'status' => 'public' ] );
		$a1 = $this->create_activity_id();
		$a2 = $this->create_activity_id(
			[
				'component'     => buddypress()->groups->id,
				'type'          => 'created_group',
				'user_id'       => $this->admin,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$results = $this->activityQuery(
			[ 'where' => [ 'type' => [ 'ACTIVITY_UPDATE', 'CREATED_GROUP' ] ] ]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		// Check activities.
		$this->assertTrue( in_array( $a1, $ids, true ) );
		$this->assertTrue( in_array( $a2, $ids, true ) );
	}

	public function test_get_public_group_activities() {
		$component = buddypress()->groups->id;
		$g1        = $this->create_group_id(
			[
				'status'  => 'private',
				'user_id' => $this->user_id,
			]
		);
		$g2        = $this->create_group_id(
			[
				'status'  => 'public',
				'user_id' => $this->user_id,
			]
		);

		$a1 = $this->create_activity_id(
			[
				'component'     => $component,
				'type'          => 'created_group',
				'user_id'       => $this->user_id,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$results = $this->activityQuery( [ 'where' => [ 'component' => 'GROUPS' ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 1, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertNotContains( $a1, $ids );
		$this->assertContains( $a2, $ids );
	}

	public function test_get_activities_from_a_specific_group() {
		$component = buddypress()->groups->id;
		$g1        = $this->create_group_id();
		$g2        = $this->create_group_id();

		$a1 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a3 = $this->create_activity_id(
			[
				'component'     => $component,
				'type'          => 'created_group',
				'user_id'       => $this->user_id,
				'item_id'       => $g2,
				'hide_sitewide' => true,
			]
		);

		$a4 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g1,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$results = $this->activityQuery( [ 'where' => [ 'groupId' => $g2 ] ] );

		$this->assertQuerySuccessful( $results )
			->hasEdges()
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertEqualSets( [ $a1, $a2 ], $ids );
		$this->assertNotContains( $a3, $ids );
		$this->assertNotContains( $a4, $ids );
	}

	public function test_get_activities_from_private_group_without_access() {
		$g1 = $this->create_group_id(
			[
				'status'     => 'private',
				'creator_id' => $this->user_id,
			]
		);

		$this->create_activity_id(
			[
				'component'     => buddypress()->groups->id,
				'type'          => 'created_group',
				'user_id'       => $this->user_id,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful(
			$this->activityQuery(
				[
					'where' => [
						'component' => 'GROUPS',
						'primaryId' => $g1,
					],
				]
			)
		)
			->notHasEdges()
			->notHasNodes();

		// Using the groupId param.
		$this->assertQuerySuccessful(
			$this->activityQuery( [ 'where' => [ 'groupId' => $g1 ] ] )
		)
			->notHasEdges()
			->notHasNodes();
	}

	public function test_get_activities_from_a_specific_group_with_primary_id() {
		$component = buddypress()->groups->id;
		$g1        = $this->create_group_id();
		$g2        = $this->create_group_id();

		$a1 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a3 = $this->create_activity_id(
			[
				'component'     => $component,
				'type'          => 'created_group',
				'user_id'       => $this->user_id,
				'item_id'       => $g2,
				'hide_sitewide' => true,
			]
		);

		$a4 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g1,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$results = $this->activityQuery(
			[
				'where' => [
					'component' => 'GROUPS',
					'primaryId' => $g2,
				],
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertEqualSets( [ $a1, $a2 ], $ids );
		$this->assertNotContains( $a3, $ids );
		$this->assertNotContains( $a4, $ids );
	}

	public function test_get_activities_from_privte_group() {
		$component = buddypress()->groups->id;
		$u         = $this->factory->user->create();

		$g1 = $this->create_group_id(
			[
				'status'     => 'private',
				'creator_id' => $u,
			]
		);

		$g2 = $this->create_group_id(
			[
				'status'     => 'public',
				'creator_id' => $this->user_id,
			]
		);

		$a1 = $this->create_activity_id(
			[
				'component'     => $component,
				'type'          => 'created_group',
				'user_id'       => $u,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$this->bp->set_current_user( $u );

		$results = $this->activityQuery( [ 'where' => [ 'groupId' => $g1 ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 1, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertNotContains( $a2, $ids );
		$this->assertContains( $a1, $ids );
	}

	public function test_get_activities_from_a_specific_group_using_different_component() {
		$component = buddypress()->groups->id;
		$g1        = $this->create_group_id();
		$g2        = $this->create_group_id();

		$a1 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g2,
			]
		);

		$a3 = $this->create_activity_id(
			[
				'component'     => $component,
				'type'          => 'created_group',
				'user_id'       => $this->user_id,
				'item_id'       => $g2,
				'hide_sitewide' => true,
			]
		);

		$a4 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $this->user_id,
				'item_id'   => $g1,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$results = $this->activityQuery(
			[
				'where' => [
					'component' => 'ACTIVITY',
					'groupId'   => $g2,
				],
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertEqualSets( [ $a1, $a2 ], $ids );
		$this->assertNotContains( $a3, $ids );
		$this->assertNotContains( $a4, $ids );
	}

	public function test_get_activities_with_invalid_type() {
		$this->assertQueryFailed( $this->activityQuery( [ 'where' => [ 'type' => [ 'random-status' ] ] ] ) )
			->expectedErrorMessage( 'Variable "$where" got invalid value {"type":["random-status"]}; Expected type ActivityTypeEnum at value.type[0].' );
	}

	public function test_get_activities_sorted() {
		$this->bp->set_current_user( $this->admin );

		$a1 = $this->create_activity_id();
		$this->create_activity_id();
		$a3 = $this->create_activity_id();

		// ASC.
		$this->assertQuerySuccessful( $this->activityQuery( [ 'where' => [ 'order' => 'ASC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a1 );

		// DESC.
		$this->assertQuerySuccessful( $this->activityQuery( [ 'where' => [ 'order' => 'DESC' ] ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a3 );
	}

	public function test_get_activities_with_exclude() {
		$this->bp->set_current_user( $this->admin );

		$a1 = $this->create_activity_id();
		$a2 = $this->create_activity_id();
		$a3 = $this->create_activity_id();

		$results = $this->activityQuery( [ 'where' => [ 'exclude' => [ $a1 ] ] ] );

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		// Check activities.
		$this->assertFalse( in_array( $a1, $ids, true ) );
		$this->assertTrue( in_array( $a2, $ids, true ) );
		$this->assertTrue( in_array( $a3, $ids, true ) );
	}

	public function test_get_activities_by_scope() {
		$u         = $this->random_user;
		$component = buddypress()->groups->id;

		$this->bp->set_current_user( $u );

		$g1 = $this->bp_factory->group->create();

		$a1 = $this->create_activity_id( [ 'user_id' => $u ] );

		$a2 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $u,
				'item_id'   => $g1,
			]
		);

		$a3 = $this->create_activity_id(
			[
				'component' => $component,
				'type'      => 'created_group',
				'user_id'   => $u,
				'item_id'   => $g1,
			]
		);

		$results = $this->activityQuery(
			[
				'where' => [
					'userId' => $u,
					'scope'  => [ 'GROUPS' ],
				],
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasNodes();

		$nodes = $results['data']['activities']['nodes'];

		$this->assertCount( 2, $nodes );

		$ids = wp_list_pluck( $nodes, 'databaseId' );

		$this->assertNotContains( $a1, $ids );
		$this->assertContains( $a2, $ids );
		$this->assertContains( $a3, $ids );
	}

	public function test_get_first_activity() {
		$this->create_activity_id();
		$this->create_activity_id();
		$a1 = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		// The first here is the last one created. The latest activity.
		$this->assertQuerySuccessful(
			$this->activityQuery(
				[
					'first' => 1,
					'after' => '',
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a1 )
			->hasNextPage();
	}

	public function test_get_activities_after() {
		$a1 = $this->create_activity_id();
		$a2 = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->activityQuery( [ 'after' => $this->key_to_cursor( $a1 ) ] ) )
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a2 )
			->hasPreviousPage();
	}

	public function test_get_activities_before() {
		$this->create_activity_id();
		$a2 = $this->create_activity_id();
		$a3 = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful(
			$this->activityQuery(
				[
					'last'   => 1,
					'before' => $this->key_to_cursor( $a3 ),
				]
			)
		)
			->HasEdges()
			->firstEdgeNodeField( 'databaseId', $a2 )
			->hasNextPage();
	}

	public function test_public_activity_comments_thread() {
		$a = $this->create_activity_id(
			[
				'component' => 'activity',
				'content'   => 'Foo',
				'type'      => 'activity_update',
			]
		);

		$c1 = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$c2 = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->random_user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->assertQuerySuccessful( $this->activityQuery() )
			->hasEdges()
			->hasNodes()
			->firstNodesNodeField( 'id', $this->toRelayId( 'activity', (string) $a ) )
			->firstNodesNodeField( 'databaseId', $a )
			->firstNodesNodeField(
				'comments',
				[
					'nodes' => [
						0 => [
							'id'               => $this->toRelayId( 'activity', (string) $c1 ),
							'databaseId'       => $c1,
							'parentDatabaseId' => $a,
							'parentId'         => $this->toRelayId( 'activity', (string) $a ),
							'type'             => 'ACTIVITY_COMMENT',
							'primaryItemId'    => $a,
							'secondaryItemId'  => $a,
						],
						1 => [
							'id'               => $this->toRelayId( 'activity', (string) $c2 ),
							'databaseId'       => $c2,
							'parentDatabaseId' => $a,
							'parentId'         => $this->toRelayId( 'activity', (string) $a ),
							'type'             => 'ACTIVITY_COMMENT',
							'primaryItemId'    => $a,
							'secondaryItemId'  => $a,
						],
					],
				]
			);
	}

	public function test_public_activity_comments_stream() {
		$a = $this->create_activity_id(
			[
				'component' => 'activity',
				'content'   => 'Foo',
				'type'      => 'activity_update',
			]
		);

		$c1 = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$results = $this->activityQuery( [ 'where' => [ 'displayComments' => true ] ] );

		$this->assertQuerySuccessful( $results )
			->hasEdges()
			->hasNodes();

		$this->assertEquals(
			[
				0 => [
					'id'              => $this->toRelayId( 'activity', (string) $c1 ),
					'databaseId'      => $c1,
					'primaryItemId'   => $a,
					'secondaryItemId' => $a,
					'comments'        => [
						'nodes' => [],
					],
				],
				1 => [
					'id'              => $this->toRelayId( 'activity', (string) $a ),
					'databaseId'      => $a,
					'primaryItemId'   => 0,
					'secondaryItemId' => 0,
					'comments'        => [
						'nodes' => [
							0 => [
								'id'               => $this->toRelayId( 'activity', (string) $c1 ),
								'databaseId'       => $c1,
								'parentDatabaseId' => $a,
								'parentId'         => $this->toRelayId( 'activity', (string) $a ),
								'type'             => 'ACTIVITY_COMMENT',
								'primaryItemId'    => $a,
								'secondaryItemId'  => $a,
							],
						],
					],
				],
			],
			$results['data']['activities']['nodes']
		);
	}

	public function test_get_activities_thread_comments_from_a_specific_group() {
		$g = $this->create_group_id();
		$a = $this->create_activity_id(
			[
				'component' => buddypress()->groups->id,
				'content'   => 'Foo',
				'item_id'   => $g,
				'user_id'   => $this->user_id,
				'type'      => 'activity_update',
			]
		);

		$c1 = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user_id,
				'item_id'     => $g,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$c2 = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->random_user,
				'item_id'     => $g,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->assertQuerySuccessful( $this->activityQuery( [ 'where' => [ 'groupId' => $g ] ] ) )
			->hasEdges()
			->hasNodes()
			->firstNodesNodeField( 'id', $this->toRelayId( 'activity', (string) $a ) )
			->firstNodesNodeField( 'databaseId', $a )
			->firstNodesNodeField( 'primaryItemId', $g )
			->firstNodesNodeField( 'secondaryItemId', 0 )
			->firstNodesNodeField(
				'comments',
				[
					'nodes' => [
						0 => [
							'id'               => $this->toRelayId( 'activity', (string) $c1 ),
							'databaseId'       => $c1,
							'parentDatabaseId' => $a,
							'parentId'         => $this->toRelayId( 'activity', (string) $a ),
							'type'             => 'ACTIVITY_COMMENT',
							'primaryItemId'    => $a,
							'secondaryItemId'  => $a,
						],
						1 => [
							'id'               => $this->toRelayId( 'activity', (string) $c2 ),
							'databaseId'       => $c2,
							'parentDatabaseId' => $a,
							'parentId'         => $this->toRelayId( 'activity', (string) $a ),
							'type'             => 'ACTIVITY_COMMENT',
							'primaryItemId'    => $a,
							'secondaryItemId'  => $a,
						],
					],
				]
			);
	}

	/**
	 * @todo Pending implementation.
	 *
	 * @see https://wordpress.slack.com/archives/C02RQBYUG/p1644186007153489
	 */
	public function test_get_activities_stream_comments_from_a_specific_group() {
		$this->markTestIncomplete( 'Pending implementation' );

		$g = $this->create_group_id();
		$a = $this->create_activity_id(
			[
				'component' => buddypress()->groups->id,
				'content'   => 'Foo',
				'item_id'   => $g,
				'user_id'   => $this->user_id,
				'type'      => 'activity_update',
			]
		);

		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->random_user,
				'item_id'     => $g,
				'activity_id' => $a, // Root activity.
				'content'     => 'Activity comment',
				'component'   => buddypress()->groups->id,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$results = $this->activityQuery(
			[
				'where' => [
					'groupId'         => $g,
					'displayComments' => true,
				],
			]
		);

		$this->assertQuerySuccessful( $results )
			->hasEdges()
			->hasNodes();

		$this->assertEquals(
			[
				0 => [
					'id'         => $this->toRelayId( 'activity', (string) $c ),
					'databaseId' => $c,
					'comments'   => [
						'nodes' => [],
					],
				],
				1 => [
					'id'         => $this->toRelayId( 'activity', (string) $a ),
					'databaseId' => $a,
					'comments'   => [
						'nodes' => [
							0 => [
								'id'               => $this->toRelayId( 'activity', (string) $c ),
								'databaseId'       => $c,
								'parentDatabaseId' => $a,
								'parentId'         => $this->toRelayId( 'activity', (string) $a ),
								'type'             => 'ACTIVITY_COMMENT',
								'primaryItemId'    => $a,
								'secondaryItemId'  => $a,
							],
						],
					],
				],
			],
			$results['data']['activities']['nodes']
		);
	}

	/**
	 * Activity query.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function activityQuery( array $variables = [] ): array {
		$query = 'query activityQuery(
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:RootQueryToActivityConnectionWhereArgs
		) {
			activities(
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
					primaryItemId
					secondaryItemId
					comments {
						nodes {
							id
							databaseId
							parentDatabaseId
							parentId
							type
							primaryItemId
							secondaryItemId
						}
					}
				}
			}
		}';

		$operation_name = 'activityQuery';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
