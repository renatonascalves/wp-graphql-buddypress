<?php

/**
 * Test_XProfile_updateXProfileField_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_updateXProfileField_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

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
	public function setUp() : void {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
		$this->xprofile_field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );
	}

	public function test_admins_can_update_xprofile_field() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_xprofile_field( [ 'name' => 'Updated' ] ) )
			->hasField( 'databaseId', $this->xprofile_field_id )
			->hasField( 'name', 'Updated' );
	}

	public function test_update_order_of_the_xprofile_field() {
		$field_id = $this->bp_factory->xprofile_field->create( [ 'field_group_id' => $this->xprofile_group_id ] );

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful(
			$this->update_xprofile_field(
				[
					'databaseId' => $field_id,
					'fieldOrder' => 1,
				]
			)
		)
			->hasField( 'fieldOrder', 1 )
			->hasField( 'databaseId', $field_id );

		// Update Order.
		$this->assertQuerySuccessful(
			$this->update_xprofile_field(
				[
					'databaseId' => $this->xprofile_field_id,
					'fieldOrder' => 1,
				]
			)
		)
			->hasField( 'fieldOrder', 1 )
			->hasField( 'databaseId', $this->xprofile_field_id );
	}

	public function test_update_using_invalid_xprofile_field_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->update_xprofile_field( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This XProfile field does not exist.' );
	}

	public function test_update_xprofile_field_without_logged_in_user() {
		$this->assertQueryFailed( $this->update_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_xprofile_field_with_user_without_permission() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->update_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * XProfile field.
	 *
	 * @param array $args Variables.
	 * @return array
	 */
	protected function update_xprofile_field( array $args = [] ): array {
		$query = '
			mutation updateXProfileFieldTest(
				$clientMutationId: String!
				$databaseId: Int
				$name: String
				$fieldOrder: Int
			) {
				updateXProfileField(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
                        name: $name
                        fieldOrder: $fieldOrder
					}
				)
				{
					clientMutationId
					field {
						databaseId
                        name
						fieldOrder
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'       => $this->xprofile_field_id,
				'name'             => 'Updated XProfile Group',
				'fieldOrder'       => null,
			]
		);

		$operation_name = 'updateXProfileFieldTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
