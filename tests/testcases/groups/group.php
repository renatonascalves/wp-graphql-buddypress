<?php
/**
 * Test_Groups_group_Queries Class.
 *
 * @group groups
 */
class Test_Groups_group_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		$this->global_id = $this->toRelayId( 'group', (string) $this->group );
	}

	public function test_group_query() {
		$this->assertQuerySuccessful( $this->get_a_group() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->group )
			->hasField( 'name', 'Group Test' )
			->hasField( 'status', 'PUBLIC' )
			->hasField( 'description', 'Group Description' )
			->hasField( 'totalMemberCount', null )
			->hasField( 'lastActivity', null )
			->hasField( 'hasForum', 1 )
			->hasField( 'parent', null )
			->hasField( 'admins', null )
			->hasField( 'mods', null )
			->hasField( 'creator', [ 'userId' => $this->user_id ] )
			->hasField( 'uri', bp_get_group_url( new \BP_Groups_Group( $this->group ) ) );
	}

	public function test_group_by_query_with_groupid_param() {
		$this->assertQuerySuccessful( $this->get_a_group() )
			->hasField( 'name', 'Group Test' )
			->hasField( 'id', $this->global_id );
	}

	public function test_group_by_query_with_slug_param() {
		$slug     = 'group-test';
		$group_id = $this->create_group_id();

		$this->assertQuerySuccessful( $this->get_a_group( $slug, 'SLUG' ) )
			->hasField( 'slug', $slug )
			->hasField( 'databaseId', $group_id );
	}

	public function test_group_by_query_with_previous_slug_param() {
		$previous_slug = 'group-test';
		$group_id      = $this->create_group_id();
		$global_id     = $this->toRelayId( 'group', (string) $group_id );

		// Update slug.
		groups_edit_base_group_details(
			[
				'group_id' => $group_id,
				'slug'     => 'newslug',
			]
		);

		$this->assertQuerySuccessful( $this->get_a_group( $previous_slug, 'PREVIOUS_SLUG' ) )
			->hasField( 'slug', 'newslug' )
			->hasField( 'databaseId', $group_id )
			->hasField( 'id', $global_id );
	}

	public function test_get_group_with_parent_group() {
		$parent_id       = $this->create_group_id();
		$child_id        = $this->bp_factory->group->create( [ 'parent_id' => $parent_id ] );
		$global_id       = $this->toRelayId( 'group', (string) $parent_id );
		$global_child_id = $this->toRelayId( 'group', (string) $child_id );

		$this->assertQuerySuccessful( $this->get_a_group( $child_id, 'DATABASE_ID' ) )
			->hasField( 'id', $global_child_id )
			->hasField( 'databaseId', $child_id )
			->hasField(
				'parent',
				[
					'id'         => $global_id,
					'databaseId' => $parent_id,
				]
			);
	}

	public function test_get_group_with_invalid_group_id() {
		$this->assertQueryFailed( $this->get_a_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_get_group_with_avatar_disabled() {
		buddypress()->avatar->show_avatars = false;

		$this->assertQuerySuccessful( $this->get_a_group() )
			->hasField( 'attachmentAvatar', null );

		buddypress()->avatar->show_avatars = true;
	}

	public function test_get_hidden_group() {
		$g = $this->bp_factory->group->create( [ 'status' => 'hidden' ] );

		$this->bp->add_user_to_group( $this->user_id, $g );

		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->get_a_group( $g ) )
			->hasField( 'status', 'HIDDEN' )
			->hasField( 'databaseId', $g );
	}

	public function test_get_hidden_group_without_being_from_group() {
		$g = $this->bp_factory->group->create( [ 'status' => 'hidden' ] );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->get_a_group( $g );

		$this->assertEmpty( $response['data']['group'] );
	}

	/**
	 * Get a group.
	 *
	 * @param int|string|null $group_id Group ID.
	 * @param string|null     $type     Type.
	 * @return array
	 */
	protected function get_a_group( $group_id = null, $type = 'DATABASE_ID' ): array {
		$group = $group_id ?? $this->group;
		$query = "
			query {
				group(id: \"{$group}\", idType: {$type}) {
					databaseId
					description(format: RAW)
					hasForum
					id
					lastActivity
					name
					slug
					status
					totalMemberCount
					uri
					creator {
						userId
					}
					mods {
						userId
					}
					admins {
						userId
					}
					parent {
						id
						databaseId
					}
					attachmentAvatar {
						full
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
