<?php

/**
 * Test_XProfile_createXProfileField_Mutation Class.
 *
 * @group xprofile-field
 * @group xprofile
 */
class Test_XProfile_createXProfileField_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Profile Group ID.
	 *
	 * @var int
	 */
	public $xprofile_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->xprofile_group_id = $this->bp_factory->xprofile_group->create( [ 'name' => 'XProfile Group' ] );
	}

	public function test_admins_can_create_xprofile_field() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->create_xprofile_field() )
			->hasField(
				'field',
				[
					'name'            => 'XProfile Field Test',
					'description'     => 'Description',
					'visibilityLevel' => 'PUBLIC',
					'doAutolink'      => 'off',
					'canDelete'       => false,
				]
			);
	}

	public function test_admins_can_create_xprofile_field_with_fields() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful(
			$this->create_xprofile_field(
				[
					'doAutolink' => true,
					'canDelete'  => true,
				]
			)
		)
			->hasField(
				'field',
				[
					'name'            => 'XProfile Field Test',
					'description'     => 'Description',
					'visibilityLevel' => 'PUBLIC',
					'doAutolink'      => 'on',
					'canDelete'       => true,
				]
			);
	}

	public function test_create_xprofile_field_without_required_field_field() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->create_xprofile_field( [ 'type' => '' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value (empty string); Expected type XProfileFieldTypesEnum.' );
	}

	public function test_create_xprofile_field_with_invalid_type() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->create_xprofile_field( [ 'type' => 'group' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value "group"; Expected type XProfileFieldTypesEnum.' );
	}

	public function test_create_xprofile_field_without_logged_in_user() {
		$this->assertQueryFailed( $this->create_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_xprofile_field_without_permission() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->create_xprofile_field() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Create XProfile Field.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_xprofile_field( array $args = [] ): array {
		$query = '
			mutation createXProfileFieldTest(
				$clientMutationId:String!
				$name:String!
				$description:String
				$groupId:Int!
				$type:XProfileFieldTypesEnum!
				$doAutolink:Boolean
				$canDelete:Boolean
			) {
				createXProfileField(
					input: {
						clientMutationId: $clientMutationId
						name: $name
						description: $description
						groupId: $groupId
						type: $type
						doAutolink: $doAutolink
						canDelete: $canDelete
					}
				)
				{
					clientMutationId
					field {
						name
						description
						visibilityLevel
						doAutolink
						canDelete
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'name'             => 'XProfile Field Test',
				'description'      => 'Description',
				'groupId'          => $this->xprofile_group_id,
				'type'             => 'TEXTBOX',
				'doAutolink'       => false,
				'canDelete'        => false,
			]
		);

		$operation_name = 'createXProfileFieldTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
