<?php

/**
 * Test_XProfile_Group_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public $name;
	public $field_name;
	public $desc;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

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
			$this->graphql( compact( 'query' ) )
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

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'The "id" is invalid.' );

		$query = "
			query {
				xprofileGroupBy(groupId: 111) {
					id
				}
			}
		";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'Internal server error' );
	}
}
