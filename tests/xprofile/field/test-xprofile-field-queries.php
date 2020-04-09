<?php

/**
 * Test_XProfile_Field_Queries Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_Field_Queries extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $xprofile_group_id;
	public $xprofile_field_id;

	public function setUp() {
		parent::setUp();

		$this->bp_factory = new BP_UnitTest_Factory();
		$this->bp         = new BP_UnitTestCase();
		$this->admin      = $this->factory->user->create( [
			'role'       => 'administrator',
			'user_email' => 'admin@example.com',
		] );

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create();
		$this->xprofile_field_id  = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
	}

	public function test_xprofile_field_by_query() {
		$query = "
			query {
				xprofileFieldBy(fieldId: {$this->xprofile_field_id}) {
					fieldId
					groupId
					parent {
						name
					}
					type
					canDelete
					isRequired
					isDefaultOption
					visibilityLevel
					value {
						raw
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileFieldBy' => [
						'fieldId'         => $this->xprofile_field_id,
						'groupId'         => $this->xprofile_group_id,
						'parent'          => null,
						'canDelete'       => true,
						'type'            => 'TEXTBOX',
						'isRequired'      => false,
						'isDefaultOption' => false,
						'visibilityLevel' => 'PUBLIC',
						'value'           => [
							'raw' => null,
						],
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_get_xprofile_field_value_from_logged_in_user() {
		$this->bp->set_current_user( $this->admin );

		xprofile_set_field_data( $this->xprofile_field_id, $this->admin, 'foo' );

		$query = "
			query {
				xprofileFieldBy(fieldId: {$this->xprofile_field_id}) {
					fieldId
					groupId
					parent {
						name
					}
					type
					canDelete
					isRequired
					isDefaultOption
					visibilityLevel
					value {
						raw
            			unserialized
					}
				}
			}
		";

		// print_r( do_graphql_request( $query ) );
		// return;

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileFieldBy' => [
						'fieldId'         => $this->xprofile_field_id,
						'groupId'         => $this->xprofile_group_id,
						'parent'          => null,
						'canDelete'       => true,
						'type'            => 'TEXTBOX',
						'isRequired'      => false,
						'isDefaultOption' => false,
						'visibilityLevel' => 'PUBLIC',
						'value'           => [
							'raw'          => 'foo',
							'unserialized' => [
								'foo',
							],
						]
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_get_xprofile_field_options() {
		$this->bp->set_current_user( $this->admin );

		$xprofile_field_id  = $this->bp_factory->xprofile_field->create(
			[
				'field_group_id' => $this->xprofile_group_id,
				'type'           => 'checkbox',
			]
		);

		$option1 = xprofile_insert_field(
			[
				'field_group_id' => $this->xprofile_group_id,
				'parent_id'      => $xprofile_field_id,
				'type'           => 'option',
				'name'           => 'Option 1',
			]
		);

		$option2 = xprofile_insert_field(
			[
				'field_group_id' => $this->xprofile_group_id,
				'parent_id'      => $xprofile_field_id,
				'type'           => 'option',
				'name'           => 'Option 2',
				'is_required'    => true,
			]
		);

		$option3 = xprofile_insert_field(
			[
				'field_group_id' => $this->xprofile_group_id,
				'parent_id'      => $xprofile_field_id,
				'type'           => 'option',
				'name'           => 'Option 3',
			]
		);

		xprofile_set_field_data( $xprofile_field_id, $this->admin, 'foo' );
		xprofile_set_field_data( $xprofile_field_id, $this->admin, [ 'Option 1', 'Option 3' ] );

		$query = "
			query {
				xprofileFieldBy(fieldId: {$xprofile_field_id}) {
					fieldId
					groupId
					options {
						nodes {
							name
							fieldId
							groupId
							isRequired
						}
					}
					value {
						raw
            			unserialized
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileFieldBy' => [
						'fieldId'         => $xprofile_field_id,
						'groupId'         => $this->xprofile_group_id,
						'options'         => [
							'nodes' => [
								[
									'name'       => 'Option 1',
									'fieldId'    => $option1,
									'groupId'    => $this->xprofile_group_id,
									'isRequired' => false,
								],
								[
									'name'       => 'Option 2',
									'fieldId'    => $option2,
									'groupId'    => $this->xprofile_group_id,
									'isRequired' => true,
								],
								[
									'name'       => 'Option 3',
									'fieldId'    => $option3,
									'groupId'    => $this->xprofile_group_id,
									'isRequired' => false,
								],
							],
						],
						'value'           => [
							'raw'          => 'a:2:{i:0;s:8:"Option 1";i:1;s:8:"Option 3";}',
							'unserialized' => [
								'Option 1',
								'Option 3'
							],
						]
					],
				],
			],
			do_graphql_request( $query )
		);
	}

	public function test_xprofile_field_by_invalid_id() {
		$query = "
			query {
				xprofileFieldBy(fieldId: 111) {
					fieldId
				}
			}
		";

		$response = do_graphql_request( $query  );

		$this->assertArrayHasKey( 'errors', $response );
		$this->assertSame( 'This XProfile field does not exist.', $response['errors'][0]['message'] );
	}

	/**
	 * @todo
	 */
	protected function xprofileFieldsQuery( $variables ) {
		$query = 'query xprofileFieldsQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToXProfileFieldsConnectionWhereArgs) {
			xprofileFields( first:$first last:$last after:$after before:$before where:$where ) {
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
						name
					}
				}
				nodes {
				  id
				}
			}
		}';

		return do_graphql_request( $query, 'xprofileFieldsQuery', $variables );
	}
}
