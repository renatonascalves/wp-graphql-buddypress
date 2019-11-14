<?php

/**
 * Group Tests.
 *
 * @package BuddyPress
 * @subpackage BP_GRAPHQL
 * @group group
 */
class GroupObjectQueriesTest extends WP_UnitTestCase {

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
			'creator_id'  => $this->user,
		) );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_group_by_query_groupid_param() {

		$global_id = $this->group_id;

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(groupId: {$global_id}) {
				groupId
				name
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'groupId' => $global_id,
						'name'    => 'Group Test',
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_slug_param() {

		$slug = 'group-test';

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(slug: \"{$slug}\") {
				id
				name
				slug
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'   => $this->group_id,
						'name' => 'Group Test',
						'slug' => $slug,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_group_by_query_previous_slug_param() {

		$previous_slug = 'group-test';
		$slug          = 'new-group-slug';

		/**
		 * Create the query string to pass to the $query
		 */
		$query = "
		query {
			groupBy(previousSlug: \"{$previous_slug}\") {
				id
				name
				slug
			}
		}";

		/* // Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'   => $this->group_id,
						'name' => 'Group Test',
						'slug' => $slug,
					],
				],
			],
			do_graphql_request( $query )
		); */
	}
}
