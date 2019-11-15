<?php

/**
 * GroupBy Queries Tests.
 *
 * @group group
 */
class GroupByQueriesTest extends WP_UnitTestCase {

	public $admin;
	public $group_id;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create( [
			'role'       => 'administrator',
			'user_email' => 'admin@example.com',
		] );

		$this->group_id = $this->bp_factory->group->create( array(
			'slug'        => 'group-test',
			'name'        => 'Group Test',
			'description' => 'Group Description',
			'creator_id'  => $this->admin,
		) );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_group_by_query() {

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'group', $this->group_id );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(id: \"{$global_id}\") {
				id,
				groupId
				name
				slug
				status
				totalMemberCount
				lastActivity
				hasForum
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'               => $global_id,
						'groupId'          => $this->group_id,
						'name'             => 'Group Test',
						'slug'             => 'group-test',
						'status'           => 'PUBLIC',
						'totalMemberCount' => null,
						'lastActivity'     => null,
						'hasForum'         => true,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_id_param() {

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'group', $this->group_id );

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(id: \"{$global_id}\") {
				id,
				groupId
				name
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'      => $global_id,
						'groupId' => $this->group_id,
						'name'    => 'Group Test',
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_groupid_param() {

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(groupId: {$this->group_id}) {
				groupId
				name
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'groupId' => $this->group_id,
						'name'    => 'Group Test',
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_with_slug_param() {

		$slug = 'group-test';

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(slug: \"{$slug}\") {
				groupId
				name
				slug
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'groupId' => $this->group_id,
						'name'    => 'Group Test',
						'slug'    => $slug,
					],
				],
			],
			do_graphql_request( $query )
		);
	}
}
