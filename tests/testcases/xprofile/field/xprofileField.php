<?php

/**
 * Test_XProfile_xprofileFieldBy_Queries Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_xprofileFieldBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * XProfile Field ID.
	 *
	 * @var int
	 */
	public $xprofile_field_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create();
		$this->xprofile_field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
	}

	public function test_xprofile_field_by_query() {
		$this->assertQuerySuccessful( $this->get_xprofile_field( $this->xprofile_field_id, 'DATABASE_ID' ) )
			->hasField( 'databaseId', $this->xprofile_field_id )
			->hasField( 'groupId', $this->xprofile_group_id )
			->hasField( 'parent', null )
			->hasField( 'canDelete', true )
			->hasField( 'type', 'TEXTBOX' )
			->hasField( 'isRequired', false )
			->hasField( 'isDefaultOption', false )
			->hasField( 'visibilityLevel', 'PUBLIC' )
			->hasField( 'value', null );
	}

	public function test_get_xprofile_field_value_with_logged_in_user() {
		$this->bp->set_current_user( $this->admin );

		xprofile_set_field_data( $this->xprofile_field_id, $this->admin, 'foo' );

		$this->assertQuerySuccessful( $this->get_xprofile_field( $this->xprofile_field_id, 'DATABASE_ID' ) )
			->hasField( 'databaseId', $this->xprofile_field_id )
			->hasField( 'groupId', $this->xprofile_group_id )
			->hasField( 'parent', null )
			->hasField( 'canDelete', true )
			->hasField( 'type', 'TEXTBOX' )
			->hasField( 'isRequired', false )
			->hasField( 'isDefaultOption', false )
			->hasField( 'visibilityLevel', 'PUBLIC' )
			->hasField(
				'value',
				[
					'raw'          => 'foo',
					'unserialized' => [
						'foo',
					],
				]
			);
	}

	public function test_get_xprofile_field_with_invalid_id() {
		$this->assertQueryFailed( $this->get_xprofile_field(
				GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER,
				'DATABASE_ID'
			) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_get_xprofile_field_options() {
		$this->bp->set_current_user( $this->admin );

		$xprofile_field_id = $this->bp_factory->xprofile_field->create(
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

		$this->assertQuerySuccessful( $this->get_xprofile_field( $xprofile_field_id, 'DATABASE_ID' ) )
			->hasField( 'databaseId', $xprofile_field_id )
			->hasField( 'groupId', $this->xprofile_group_id )
			->hasField(
				'value',
				[
					'raw'          => 'a:2:{i:0;s:8:"Option 1";i:1;s:8:"Option 3";}',
					'unserialized' => [
						'Option 1',
						'Option 3',
					],
				]
			)
			->hasField(
				'options',
				[
					'nodes' => [
						[
							'name'       => 'Option 1',
							'databaseId' => $option1,
							'groupId'    => $this->xprofile_group_id,
							'isRequired' => false,
						],
						[
							'name'       => 'Option 2',
							'databaseId' => $option2,
							'groupId'    => $this->xprofile_group_id,
							'isRequired' => true,
						],
						[
							'name'       => 'Option 3',
							'databaseId' => $option3,
							'groupId'    => $this->xprofile_group_id,
							'isRequired' => false,
						],
					],
				]
			);
	}

	/**
	 * Get XProfile field.
	 *
	 * @param int $xprofile_field_id XProfile ID.
	 * @return array
	 */
	protected function get_xprofile_field( $xprofile_field_id = null, $type = null ): array {
		$xprofile_field_id = $xprofile_field_id ?? $this->xprofile_field_id;
		$query             = "
			query {
				xprofileField(id: {$xprofile_field_id}, idType: {$type} ) {
					databaseId
					groupId
					parent {
						name
					}
					options {
						nodes {
							name
							databaseId
							groupId
							isRequired
						}
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

		return $this->graphql( compact( 'query' ) );
	}
}
