<?php

/**
 * Test_Member_Queries Class.
 *
 * @group members
 */
class Test_Member_Queries extends WP_UnitTestCase {

	public static $admin;
	public static $user;
	public static $bp;
	public static $bp_factory;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp         = new BP_UnitTestCase();
		self::$bp_factory = new BP_UnitTest_Factory();
		self::$user       = self::factory()->user->create();
		self::$admin      = self::factory()->user->create( [ 'role' => 'administrator' ] );
	}

	public function test_member_query_as_unauthenticated_user() {
		$user_id   = self::factory()->user->create( [ 'user_email' => 'test@test.com' ] );
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'user', $user_id );

		// Register and set member types.
		bp_register_member_type( 'foo' );
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
						'roles'             => null,
						'slug'              => $user->data->user_nicename,
						'url'               => null,
						'userId'            => $user_id,
						'username'          => null,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_member_query_as_authenticated_user() {
		$user_id   = self::factory()->user->create( [ 'user_email' => 'test@test.com' ] );
		$global_id = \GraphQLRelay\Relay::toGlobalId( 'user', $user_id );

		// Register and set member types.
		bp_register_member_type( 'foo' );
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
						'capKey'            => 'wptests_capabilities',
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
			do_graphql_request( $query )
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
		$this->assertArrayNotHasKey( 'errors', $results );

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

	public function test_members_query_with_where_params() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		self::$bp_factory->user->create();
		self::$bp_factory->user->create();

		// Register and set member types.
		bp_register_member_type( 'foo' );
		bp_set_member_type( $u1, 'foo' );

		// Register and set member types.
		bp_register_member_type( 'bar' );
		bp_set_member_type( $u2, 'bar' );

		// Query FOO members.
		$results = $this->membersQuery(
			[
				'where' => [
					'memberType' => 'FOO'
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $u1, $results['data']['members']['nodes'][0]['userId'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasNextPage'] );

		// Query FOO members.
		$results = $this->membersQuery(
			[
				'where' => [
					'memberTypeIn' => 'FOO'
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $u1, $results['data']['members']['nodes'][0]['userId'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasNextPage'] );

		// Query BAR members.
		$results = $this->membersQuery(
			[
				'where' => [
					'memberType' => 'BAR'
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $u2, $results['data']['members']['nodes'][0]['userId'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasNextPage'] );

		// Query BAR members.
		$results = $this->membersQuery(
			[
				'where' => [
					'memberTypeIn' => 'BAR'
				]
			]
		);

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );

		$this->assertEquals( $u2, $results['data']['members']['nodes'][0]['userId'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasNextPage'] );
	}

	public function test_members_query_paginated() {
		self::$bp_factory->user->create();
		self::$bp_factory->user->create();
		self::$bp_factory->user->create();
		self::$bp_factory->user->create();

		// Query members.
		$results = $this->membersQuery( [ 'first' => 2 ] );

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );
		$this->assertEquals( 1, $results['data']['members']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasPreviousPage'] );

		// Try logged in.
		self::$bp->set_current_user( self::$admin );

		// Query members.
		$results = $this->membersQuery( [ 'first' => 2 ] );

		// Make sure the query didn't return any errors
		$this->assertArrayNotHasKey( 'errors', $results );
		$this->assertEquals( 1, $results['data']['members']['pageInfo']['hasNextPage'] );
		$this->assertFalse( $results['data']['members']['pageInfo']['hasPreviousPage'] );
	}

	protected function membersQuery( $variables =[] ) {
		$query = 'query membersQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToMembersConnectionWhereArgs) {
			members( first:$first last:$last after:$after before:$before where:$where ) {
				pageInfo {
					hasNextPage
					hasPreviousPage
					startCursor
					endCursor
				}
				edges {
					cursor
					node {
						userId
					}
				}
				nodes {
				  userId
				}
			}
		}';

		return do_graphql_request( $query, 'membersQuery', $variables );
	}
}
