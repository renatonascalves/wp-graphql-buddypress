<?php
/**
 * Test_Groups_groupBy_Queries Class.
 *
 * @group groups
 */
class Test_Groups_groupBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->global_id = $this->toRelayId( 'group', $this->group );
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
			->hasField( 'creator', [ 'userId' => $this->user ] )
			->hasField( 'uri', bp_get_group_permalink( new \BP_Groups_Group( $this->group ) ) );
	}

	public function test_group_by_query_with_groupid_param() {
		$this->assertQuerySuccessful( $this->get_a_group() )
			->hasField( 'name', 'Group Test' )
			->hasField( 'id', $this->global_id );
	}

	public function test_group_by_query_with_id_param() {
		$query = "
			query {
				groupBy(id: \"{$this->global_id}\") {
					databaseId
					name
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'name', 'Group Test' )
			->hasField( 'databaseId', $this->group );
	}

	public function test_group_by_query_with_slug_param() {
		$slug     = 'group-test';
		$group_id = $this->create_group_object();
		$query    = "
			query {
				groupBy(slug: \"{$slug}\") {
					databaseId
					slug
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'slug', 'group-test' )
			->hasField( 'databaseId', $group_id );
	}

	public function test_group_by_query_with_previous_slug_param() {
		$previous_slug = 'group-test';
		$group_id      = $this->create_group_object();
		$global_id     = $this->toRelayId( 'group', $group_id );

		// Update slug.
		groups_edit_base_group_details(
			[
				'group_id' => $group_id,
				'slug'     => 'newslug',
			]
		);

		$query = "
			query {
				groupBy(previousSlug: \"{$previous_slug}\") {
					id
					databaseId
					slug
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'slug', 'newslug' )
			->hasField( 'databaseId', $group_id )
			->hasField( 'id', $global_id );
	}

	public function test_get_group_with_parent_group() {
		$parent_id       = $this->create_group_object();
		$child_id        = $this->bp_factory->group->create( [ 'parent_id' => $parent_id ] );
		$global_id       = $this->toRelayId( 'group', $parent_id );
		$global_child_id = $this->toRelayId( 'group', $child_id );
		$query           = "{
			groupBy(id: \"{$global_child_id}\") {
				id
				databaseId
				parent {
					id
					databaseId
				}
			}
		}";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'id', $global_child_id )
			->hasField( 'databaseId', $child_id )
			->hasField( 'parent', [
				'id'      => $global_id,
				'databaseId' => $parent_id
			] );
	}

	public function test_get_group_with_invalid_id() {
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			groupBy(id: \"{$id}\") {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'The "id" is invalid.' );
	}

	public function test_get_group_with_invalid_group_id() {
		$this->assertQueryFailed( $this->get_a_group( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group does not exist.' );
	}

	public function test_get_group_with_unkown_id_argument() {
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			groupBy(groupID: \"{$id}\") {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'Unknown argument "groupID" on field "groupBy" of type "RootQuery". Did you mean "groupId"?' );
	}

	public function test_get_group_with_avatar_disabled() {
		buddypress()->avatar->show_avatars = false;

		$this->assertQuerySuccessful( $this->get_a_group() )
			->hasField( 'attachmentAvatar', null );

		buddypress()->avatar->show_avatars = true;
	}

	public function test_get_hidden_group() {
		$g = $this->bp_factory->group->create( [ 'status' => 'hidden' ] );

		$this->bp->add_user_to_group( $this->user, $g );

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_a_group( $g ) )
			->hasField( 'status', 'HIDDEN' )
			->hasField( 'databaseId', $g );
	}

	public function test_get_hidden_group_without_being_from_group() {
		$g = $this->bp_factory->group->create( [ 'status' => 'hidden' ] );

		$this->bp->set_current_user( $this->user );

		$response = $this->get_a_group( $g );

		$this->assertEmpty( $response['data']['groupBy'] );
	}

	/**
	 * Get a group.
	 *
	 * @param int|null $group_id Group ID.
	 * @return array
	 */
	protected function get_a_group( $group_id = null ): array {
		$group = $group_id ?? $this->group;
		$query = "
			query {
				groupBy(groupId: {$group}) {
					id,
					databaseId
					name
					status
					description(format: RAW)
					totalMemberCount
					lastActivity
					hasForum
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
