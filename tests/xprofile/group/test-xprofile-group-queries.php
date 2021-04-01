<?php

/**
 * Test_XProfile_Group_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Queries extends \Tests\WPGraphQL\TestCase\WPGraphQLUnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $name;
	public $field_name;
	public $desc;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create( [
			'role'       => 'administrator',
			'user_email' => 'admin@example.com',
		] );

		$this->name       = 'XProfile Group Name';
		$this->field_name = 'XProfile Field name';
		$this->desc       = 'XProfile Group Desc';
	}

	public function test_xprofile_group_by_query() {
		$u1 = $this->bp_factory->xprofile_group->create(
			[
				'name'        => $this->name,
				'description' => $this->desc,
			]
		);

		$field_id = $this->bp_factory->xprofile_field->create(
			[
				'name'           => $this->field_name,
				'field_group_id' => $u1
			]
		);

		$global_id = $this->toRelayId( 'bp_xprofile_group', $u1 );

		$query = "
			query {
				xprofileGroupBy(id: \"{$global_id}\") {
					id,
					groupId
					groupOrder
					canDelete
					name
					description
					fields {
						nodes {
							name
							fieldId
						}
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileGroupBy' => [
						'id'          => $global_id,
						'groupId'     => $u1,
						'groupOrder'  => 0,
						'canDelete'   => true,
						'name'        => $this->name,
						'description' => $this->desc,
						'fields' => [
							'nodes' => [
								0 => [
									'name' => $this->field_name,
									'fieldId' => $field_id,
								]
							]
						],
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_xprofile_group_by_invalid_id() {
		$global_id = 1111;
		$query = "
			query {
				xprofileGroupBy(id: \"{$global_id}\") {
					id
				}
			}
		";

		$response = do_graphql_request( $query );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'The "id" is invalid.', $response['errors'][0]['message'] );

		$query = "
			query {
				xprofileGroupBy(groupId: 111) {
					id
				}
			}
		";

		$response = do_graphql_request( $query );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'Internal server error', $response['errors'][0]['message'] );
	}

	/**
	 * @todo
	 */
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
