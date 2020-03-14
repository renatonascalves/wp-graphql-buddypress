<?php

/**
 * Test_XProfile_Group_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
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
		$name       = 'XProfile Group Name';
		$field_name = 'XProfile Field name';
		$desc       = 'XProfile Group Desc';

		$u1 = $this->bp_factory->xprofile_group->create(
			[
				'name'        => $name,
				'description' => $desc,
			]
		);

		$field_id = $this->bp_factory->xprofile_field->create(
			[
				'name'           => $field_name,
				'field_group_id' => $u1
			]
		);

		$global_id = \GraphQLRelay\Relay::toGlobalId( 'xprofile_group_object', $u1 );

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
							value
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
						'name'        => $name,
						'description' => $desc,
						'fields' => [
							'nodes' => [
								0 => [
									'name' => $field_name,
									'fieldId' => $field_id,
									'value' => null,
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
		$query = '
			query {
				xprofileGroupBy(groupId: {1111}) {
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
							value
						}
					}
				}
			}
		';

		$this->assertArrayHasKey( 'errors', do_graphql_request( $query ) );
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
