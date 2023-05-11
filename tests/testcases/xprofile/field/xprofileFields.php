<?php

/**
 * Test_xprofileFields_Queries Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_xprofileFields_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create();
	}

	public function test_get_xprofile_group_fields() {
		$field_id_1 = $this->bp_factory->xprofile_field->create(
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		$field_id_2 = $this->bp_factory->xprofile_field->create(
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		$global_id = $this->toRelayId( 'bp_xprofile_group', $this->xprofile_group_id );
		$response  = $this->get_xprofile_group(
			[
				'id'     => $global_id,
				'idType' => 'ID',
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->xprofile_group_id )
			->hasField(
				'fields',
				[
					'nodes' => [
						0 => [
							'databaseId' => $field_id_1,
							'groupId'    => $this->xprofile_group_id,
						],
						1 => [
							'databaseId' => $field_id_2,
							'groupId'    => $this->xprofile_group_id,
						],
					],
				]
			);
	}

	public function test_get_xprofile_group_fields_excluding_ids() {
		$field_id_1 = $this->bp_factory->xprofile_field->create(
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		$field_id_2 = $this->bp_factory->xprofile_field->create(
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		$response = $this->get_xprofile_group(
			[
				'id'     => $this->toRelayId( 'bp_xprofile_group', $this->xprofile_group_id ),
				'idType' => 'ID',
				'where'  => [ 'excludeFields' => [ $field_id_1 ] ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->xprofile_group_id )
			->hasField(
				'fields',
				[
					'nodes' => [
						0 => [
							'databaseId' => $field_id_2,
							'groupId'    => $this->xprofile_group_id,
						],
					],
				]
			);
	}

	public function test_get_xprofile_group_fields_with_member_type() {
		$field_id_1 = $this->bp_factory->xprofile_field->create(
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		$field_1 = new BP_XProfile_Field( $field_id_1 );
		$field_1->set_member_types( [ 'foo' ] );

		$response = $this->get_xprofile_group(
			[
				'id'     => $this->toRelayId( 'bp_xprofile_group', $this->xprofile_group_id ),
				'idType' => 'ID',
				'where'  => [ 'memberType' => [ 'FOO' ] ],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->xprofile_group_id )
			->hasField(
				'fields',
				[
					'nodes' => [
						0 => [
							'databaseId' => $field_id_1,
							'groupId'    => $this->xprofile_group_id,
						],
					],
				]
			);
	}

	public function test_get_xprofile_group_fields_with_empty_fields_hidden() {
		$field_id_1 = $this->bp_factory->xprofile_field->create(
			[
				'field_group_id' => $this->xprofile_group_id,
				'type'           => 'checkbox',
			]
		);

		$this->bp_factory->xprofile_field->create_many(
			5,
			[ 'field_group_id' => $this->xprofile_group_id ]
		);

		xprofile_set_field_data( $field_id_1, $this->user_id, 'foo' );

		$response = $this->get_xprofile_group(
			[
				'id'     => $this->toRelayId( 'bp_xprofile_group', $this->xprofile_group_id ),
				'idType' => 'ID',
				'where'  => [
					'userId'          => $this->user_id,
					'hideEmptyFields' => true,
				],
			]
		);

		$this->assertQuerySuccessful( $response )
			->hasField( 'databaseId', $this->xprofile_group_id )
			->hasField(
				'fields',
				[
					'nodes' => [
						0 => [
							'databaseId' => $field_id_1,
							'groupId'    => $this->xprofile_group_id,
						],
					],
				]
			);
	}

	/**
	 * Get XProfile Group ID.
	 *
	 * @param array $variables Variables.
	 * @return array
	 */
	protected function get_xprofile_group( array $variables = [] ): array {
		$query = 'query groupFieldsTest(
			$id:ID!
			$idType:XProfileGroupIdTypeEnum
			$first:Int
			$last:Int
			$after:String
			$before:String
			$where:XProfileGroupToXProfileFieldConnectionWhereArgs
		) {
			xprofileGroup(id: $id, idType: $idType) {
				databaseId
				fields(
					first:$first
					last:$last
					after:$after
					before:$before
					where:$where
				) {
					nodes {
						databaseId
						groupId
					}
				}
			}
		}';

		$operation_name = 'groupFieldsTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
