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

	public function tearDown() {
		parent::tearDown();
	}

	public function test_xprofile_field_by_query() {
		$query = "
			query {
				xprofileFieldBy(fieldId: {$this->xprofile_field_id}) {
					value
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
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileFieldBy' => [
						'value'           => null,
						'fieldId'         => $this->xprofile_field_id,
						'groupId'         => $this->xprofile_group_id,
						'parent'          => null,
						'canDelete'       => true,
						'type'            => 'TEXTBOX',
						'isRequired'      => false,
						'isDefaultOption' => false,
						'visibilityLevel' => 'PUBLIC',
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
		$this->assertSame( 'No XProfile field was found with ID: 111', $response['errors'][0]['message'] );
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
