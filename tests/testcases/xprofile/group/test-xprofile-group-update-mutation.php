<?php

/**
 * Test_XProfile_Group_Update_Mutation Class.
 *
 * @group xprofile-group
 * @group xprofile
 */
class Test_XProfile_Group_Update_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create(
			[
				'name' => 'XProfile Group Name',
			]
		);
	}

	public function test_update_xprofile_group() {
		$this->bp->set_current_user( $this->admin );

		$this->assertEquals(
			[
				'data' => [
					'updateXProfileGroup' => [
						'clientMutationId' => $this->client_mutation_id,
						'group'            => [
							'name' => 'Updated XProfile Group',
						],
					],
				],
			],
			$this->update_xprofile_group( $this->xprofile_group_id )
		);
	}

	public function test_update_xprofile_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->update_xprofile_group( 99999999 ) )
			->expectedErrorMessage( 'This XProfile group does not exist.' );
	}

	public function test_update_xprofile_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->update_xprofile_group( $this->xprofile_group_id ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_xprofile_group_without_permission() {
		$this->bp->set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

		$this->assertQueryFailed( $this->update_xprofile_group( $this->xprofile_group_id, 'Updated XProfile Group' ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * @todo test group_order update
	 */
	public function test_update_xprofile_group_group_order() {
		return $this->assertTrue( true );
	}

	public function test_update_xprofile_group_with_no_input() {
		$query = '
			mutation updateXProfileGroupTest( $clientMutationId: String! ) {
				updateXProfileGroup(
					input: {
						clientMutationId: $clientMutationId
					}
				)
				{
					clientMutationId
					group {
						name
					}
				}
			}
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
		];

		$operation_name = 'updateXProfileGroupTest';

		$this->assertQueryFailed( $this->graphql( compact( 'query', 'operation_name', 'variables' ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
