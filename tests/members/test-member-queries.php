<?php

/**
 * Test_Member_Queries Class.
 *
 * @group members
 */
class Test_Member_Queries extends WPGraphQL_BuddyPress_UnitTestCase  {

	public $bp_factory;
	public $bp;
	public $admin;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->user               = $this->factory->user->create();
		$this->admin              = $this->factory->user->create( [ 'role' => 'administrator' ] );
		$this->client_mutation_id = 'someUniqueId';

		bp_register_member_type( 'foo' );
		bp_register_member_type( 'bar' );
	}

	public function test_member_query_as_unauthenticated_user() {
		$user_id   = self::factory()->user->create( [ 'user_email' => 'test@test.com' ] );
		$global_id = $this->toRelayId( 'user', $user_id );

		// Set member types.
		bp_set_member_type( $user_id, 'foo' );

		// Get the user object.
		$user = get_user_by( 'id', $user_id );

		// Create the query.
		$query = "
			query {
				user(id: \"{$global_id}\") {
					link
					memberTypes
					mentionName
					avatar {
						size
					}
					capKey
					capabilities
					comments {
						edges {
							node {
								commentId
							}
						}
					}
					description
					email
					extraCapabilities
					firstName
					id
					lastName
					locale
					mediaItems {
						edges {
							node {
								mediaItemId
							}
						}
					}
					name
					nickname
					pages {
						edges {
							node {
								pageId
							}
						}
					}
					posts {
						edges {
							node {
								postId
							}
						}
					}
					registeredDate
					roles {
						nodes {
							name
						}
					}
					slug
					url
					userId
					username
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'user' => [
						'link'        => bp_core_get_user_domain( $user_id ),
						'memberTypes' => [ 'foo' ],
						'mentionName' => bp_activity_get_user_mentionname( $user_id ),
						'avatar'            => [
							'size' => 96,
						],
						'capKey'            => null,
						'capabilities'      => null,
						'comments'          => [
							'edges' => [],
						],
						'description'       => null,
						'email'             => null,
						'extraCapabilities' => null,
						'firstName'         => null,
						'id'                => $global_id,
						'lastName'          => null,
						'locale'            => null,
						'mediaItems'        => [
							'edges' => [],
						],
						'name'              => $user->data->display_name,
						'nickname'          => null,
						'pages'             => [
							'edges' => [],
						],
						'posts'             => [
							'edges' => [],
						],
						'registeredDate'    => null,
						'roles'             => [
							'nodes' => [],
						],
						'slug'              => $user->data->user_nicename,
						'url'               => null,
						'userId'            => $user_id,
						'username'          => null,
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
	}

	public function test_member_query_as_authenticated_user() {
		$user_id   = self::factory()->user->create( [ 'user_email' => 'test@test.com' ] );
		$global_id = $this->toRelayId( 'user', $user_id );

		// Set member types.
		bp_set_member_type( $user_id, 'foo' );

		// Login the user.
        self::$bp->set_current_user( $user_id );

		// Get the user object.
		$user = get_user_by( 'id', $user_id );

		// Create the query.
		$query = "
			query {
				user(id: \"{$global_id}\") {
					link
					memberTypes
					mentionName

					avatar {
						size
					}
					capKey
					capabilities
					comments {
						edges {
							node {
								commentId
							}
						}
					}
					description
					email
					extraCapabilities
					firstName
					id
					lastName
					locale
					mediaItems {
						edges {
							node {
								mediaItemId
							}
						}
					}
					name
					nickname
					pages {
						edges {
							node {
								pageId
							}
						}
					}
					posts {
						edges {
							node {
								postId
							}
						}
					}
					registeredDate
					roles {
						nodes {
							name
						}
					}
					slug
					url
					userId
					username
				}
			}
		";

		$this->assertEquals(
			[
				'data' => [
					'user' => [
						'link'        => bp_core_get_user_domain( $user_id ),
						'memberTypes' => [ 'foo' ],
						'mentionName' => bp_activity_get_user_mentionname( $user_id ),
						'avatar'            => [
							'size' => 96,
						],
						'capKey'            => 'wp_capabilities',
						'capabilities'      => [ 'read', 'level_0', 'subscriber' ],
						'comments'          => [
							'edges' => [],
						],
						'description'       => null,
						'email'             => 'test@test.com',
						'extraCapabilities' => [ 'read', 'level_0', 'subscriber' ],
						'firstName'         => null,
						'id'                => $global_id,
						'lastName'          => null,
						'locale'            => 'en_US',
						'mediaItems'        => [
							'edges' => [],
						],
						'name'              => $user->data->display_name,
						'nickname'          => $user->nickname,
						'pages'             => [
							'edges' => [],
						],
						'posts'             => [
							'edges' => [],
						],
						'registeredDate'    => date( 'c', strtotime( $user->user_registered ) ),
						'roles'             => [
							'nodes' => [
								[
									'name' => 'subscriber'
								]
							],
						],
						'slug'              => $user->data->user_nicename,
						'url'               => null,
						'userId'            => $user_id,
						'username'          => $user->data->user_login,
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
	}

	public function test_members_query() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		$u3 = self::$bp_factory->user->create();
		$u4 = self::$bp_factory->user->create();

		self::$bp->set_current_user( self::$admin );

		// Query.
		$results = $this->membersQuery();

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );

		$ids = wp_list_pluck(
			$results['data']['members']['nodes'],
			'userId'
		);

		// Check our four members.
		$this->assertTrue( count( $ids ) === 4 );
		$this->assertTrue( in_array( $u1, $ids, true ) );
		$this->assertTrue( in_array( $u2, $ids, true ) );
		$this->assertTrue( in_array( $u3, $ids, true ) );
		$this->assertTrue( in_array( $u4, $ids, true ) );
	}

	public function test_members_query_paginated() {
		self::$bp_factory->user->create_many( 4 );

		// Query members.
		$results = $this->membersQuery( [ 'first' => 2 ] );

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );
		$this->assertTrue( $results['data']['members']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasPreviousPage'] );
	}

	public function test_members_query_paginated_logged_in() {
		self::$bp_factory->user->create_many( 4 );

		// Try logged in.
		self::$bp->set_current_user( self::$admin );

		// Query members.
		$results = $this->membersQuery( [ 'first' => 2 ] );

		// Make sure the query didn't return any errors
		$this->assertQuerySuccessful( $results );
		$this->assertTrue( $results['data']['members']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasPreviousPage'] );
	}
}
