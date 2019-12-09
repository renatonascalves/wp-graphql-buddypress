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

	public function test_xprofile_field_by_query() {
		$u1       = $this->bp_factory->xprofile_group->create();
		$field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $u1 ] );

		/**
		 * Create the query string to pass to the $query.
		 */
		$query = "
		query {
			xprofileFieldBy(fieldId: {$field_id}) {
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
		}";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'xprofileFieldBy' => [
						'value'           => null,
						'fieldId'         => $field_id,
						'groupId'         => $u1,
						'parent'          => null,
						'canDelete'       => true,
						'type'            => 'textbox',
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
			xprofileFieldBy(fieldId: {REST_TESTS_IMPOSSIBLY_HIGH_NUMBER}) {
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
		}";

		$this->assertArrayHasKey(
			'errors',
			do_graphql_request( $query )
		);
	}
}
