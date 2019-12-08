<?php

/**
 * Test_XProfile_Group_Queries Class.
 *
 * @group xprofile-group
 */
class Test_XProfile_Group_Queries extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create( [
			'role'       => 'administrator',
			'user_email' => 'admin@example.com',
		] );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_xprofile_group_by_query() {

		$u1 = $this->bp_factory->xprofile_group->create();

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'xprofile_group_object', $u1 );

		/**
		 * Create the query string to pass to the $query.
		 */
		$query = "
		query {
			xprofileGroupBy(id: \"{$global_id}\") {
				id,
				groupId
				groupOrder
				canDelete
			}
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileGroupBy' => [
						'id'          => $global_id,
						'groupId'     => $u1,
						'groupOrder'  => 0,
						'canDelete'   => true,
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	protected function xprofileGroupsQuery( $variables ) {
		$query = 'query xprofileGroupsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToXProfileGroupConnectionWhereArgs) {
			xprofileGroups( first:$first last:$last after:$after before:$before where:$where ) {
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
						groupId
						name
					}
				}
				nodes {
				  id
				  groupId
				}
			}
		}';

		return do_graphql_request( $query, 'xprofileGroupsQuery', $variables );
	}
}
