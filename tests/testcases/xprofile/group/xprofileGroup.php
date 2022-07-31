<?php

/**
 * Test_xprofileGroup_Queries Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_xprofileGroup_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * XProfile Group description.
	 *
	 * @var int
	 */
	public $desc;

	/**
	 * XProfile Field name.
	 *
	 * @var string
	 */
	public $field_name;

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
		$xprofile_group_id = $this->bp_factory->xprofile_group->create(
			[
				'name'        => $this->name,
				'description' => $this->desc,
			]
		);

		$field_id_1 = $this->bp_factory->xprofile_field->create(
			[
				'name'           => $this->field_name,
				'field_group_id' => $xprofile_group_id,
			]
		);

		$field_id_2 = $this->bp_factory->xprofile_field->create(
			[
				'name'           => 'Another field.',
				'field_group_id' => $xprofile_group_id,
			]
		);

		$this->assertQuerySuccessful( $this->get_xprofile_group( $xprofile_group_id ) )
			->hasField( 'databaseId', $xprofile_group_id )
			->hasField( 'groupOrder', 0 )
			->hasField( 'canDelete', true )
			->hasField( 'name', $this->name )
			->hasField( 'description', $this->desc )
			->hasField(
				'fields',
				[
					'nodes' => [
						0 => [
							'name'       => $this->field_name,
							'databaseId' => $field_id_1,
							'groupId'    => $xprofile_group_id,
						],
						1 => [
							'name'       => 'Another field.',
							'databaseId' => $field_id_2,
							'groupId'    => $xprofile_group_id,
						],
					],
				]
			);
	}

	public function test_xprofile_group_by_invalid_id() {
		$this->assertQueryFailed( $this->get_xprofile_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This XProfile group does not exist.' );
	}

	/**
	 * Get XProfile ID.
	 *
	 * @param int         $xprofile_group_id XProfile Group ID.
	 * @param string|null $type              Type.
	 * @return array
	 */
	protected function get_xprofile_group( int $xprofile_group_id, $type = 'DATABASE_ID' ): array {
		$query = "
			query {
				xprofileGroup(id: \"{$xprofile_group_id}\", idType: {$type}) {
					id,
					databaseId
					groupOrder
					canDelete
					name
					description
					fields {
						nodes {
							name
							databaseId
							groupId
						}
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
