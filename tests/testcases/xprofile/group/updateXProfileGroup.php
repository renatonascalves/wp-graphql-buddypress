<?php

/**
 * Test_XProfile_updateXProfileGroup_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_updateXProfileGroup_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * XProfile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create(
			[ 'name' => 'XProfile Group Name' ]
		);
	}

	public function test_admins_can_update_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_xprofile_group() )
			->hasField( 'name', 'Updated XProfile Group' )
			->hasField( 'canDelete', true );
	}

	public function test_admins_can_update_xprofile_group_can_delete() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_xprofile_group( [ 'canDelete' => false ] ) )
			->hasField( 'canDelete', true );
	}

	public function test_update_xprofile_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->update_xprofile_group( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This XProfile group does not exist.' );
	}

	public function test_update_xprofile_group_without_logged_in_user() {
		$this->assertQueryFailed( $this->update_xprofile_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_xprofile_group( [ 'name' => 'Updated XProfile Group' ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_admins_can_update_order_of_the_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$xprofile_group_id = $this->bp_factory->xprofile_group->create();

		$this->assertQuerySuccessful(
			$this->update_xprofile_group(
				[
					'databaseId' => $xprofile_group_id,
					'groupOrder' => 1,
				]
			)
		)
			->hasField( 'groupOrder', 1 )
			->hasField( 'databaseId', $xprofile_group_id );

		// Update order.
		$this->assertQuerySuccessful( $this->update_xprofile_group( [ 'groupOrder' => 1 ] ) )
			->hasField( 'groupOrder', 1 )
			->hasField( 'databaseId', $this->xprofile_group_id );
	}

	/**
	 * Update XProfile group.
	 *
	 * @param array $args Variables.
	 * @return array
	 */
	protected function update_xprofile_group( array $args = [] ): array {
		$query = '
			mutation updateXProfileGroupTest(
				$clientMutationId: String!
				$databaseId: Int
				$name: String
				$groupOrder: Int
				$canDelete:Boolean
			) {
				updateXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
						databaseId: $databaseId
						name: $name
						groupOrder: $groupOrder
						canDelete: $canDelete
					}
				)
				{
					clientMutationId
					group {
						name
						groupOrder
						databaseId
						canDelete
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'       => $this->xprofile_group_id,
				'name'             => 'Updated XProfile Group',
				'groupOrder'       => null,
				'canDelete'        => null,
			]
		);

		$operation_name = 'updateXProfileGroupTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
