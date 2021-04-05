<?php

/**
 * Test_Groups_Group_Queries Class.
 *
 * @group groups
 */
class Test_Groups_Group_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_group_query() {
		$group_id  = $this->create_group_object();
		$global_id = $this->toRelayId( 'group', $group_id );
		$query     = "
			query {
				groupBy(id: \"{$global_id}\") {
					id,
					groupId
					name
					status
					description(format: RAW)
					totalMemberCount
					lastActivity
					hasForum
					link
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
						groupId
					}
				}
			}
		";

		// Test.
		$this->assertEquals(
			[
				'data' => [
					'groupBy' => [
						'id'               => $global_id,
						'groupId'          => $group_id,
						'name'             => 'Group Test',
						'status'           => 'PUBLIC',
						'totalMemberCount' => null,
						'description'      => 'Group Description',
						'lastActivity'     => null,
						'hasForum'         => 1,
						'link'             => bp_get_group_permalink( new \BP_Groups_Group( $group_id ) ),
						'creator'          => [
							'userId' => $this->admin,
						],
						'mods'             => null,
						'admins'           => null,
						'parent'           => null,
					],
				],
			],
			$this->graphql( compact( 'query' ) )
		);
	}

	public function test_group_by_query_with_id_param() {
		$group_id  = $this->create_group_object();
		$global_id = $this->toRelayId( 'group', $group_id );
		$query     = "
			query {
				groupBy(id: \"{$global_id}\") {
					id,
					name
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'name', 'Group Test' )
			->hasField( 'id', $global_id );
	}

	public function test_group_by_query_with_groupid_param() {
		$group_id = $this->create_group_object();
		$query    = "
			query {
				groupBy(groupId: {$group_id}) {
					groupId
					name
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'name', 'Group Test' )
			->hasField( 'groupId', $group_id );
	}

	public function test_group_by_query_with_slug_param() {
		$slug     = 'group-test';
		$group_id = $this->create_group_object();
		$query    = "
			query {
				groupBy(slug: \"{$slug}\") {
					groupId
					slug
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'slug', 'group-test' )
			->hasField( 'groupId', $group_id );
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
					groupId
					slug
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'slug', 'newslug' )
			->hasField( 'groupId', $group_id )
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
				groupId
				parent {
					id
					groupId
				}
			}
		}";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'id', $global_child_id )
			->hasField( 'groupId', $child_id )
			->hasField( 'parent', [
				'id'      => $global_id,
				'groupId' => $parent_id
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
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			groupBy(groupId: {$id}) {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
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

	public function test_hidden_group() {
		$u = $this->user;
		$g = $this->bp_factory->group->create( array(
			'status' => 'hidden',
		) );

		$this->bp->add_user_to_group( $u, $g );
		$this->bp->set_current_user( $u );

		$query    = "
			query {
				groupBy(groupId: {$g}) {
					groupId
					status
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'status', 'HIDDEN' )
			->hasField( 'groupId', $g );
	}

	public function test_hidden_group_without_being_from_group() {
		$u = $this->user;
		$g = $this->bp_factory->group->create( array(
			'status' => 'hidden',
		) );

		$this->bp->set_current_user( $u );

		$query    = "
			query {
				groupBy(groupId: {$g}) {
					groupId
					status
				}
			}
		";

		$response = $this->graphql( compact( 'query' ) );

		$this->assertEmpty( $response['data']['groupBy'] );
	}

	protected function create_group_object( $args = [] ) {
		return $this->bp_factory->group->create(
			array_merge(
				[
					'slug'         => 'group-test',
					'name'         => 'Group Test',
					'description'  => 'Group Description',
					'creator_id'   => $this->admin,
				],
				$args
			)
		);
	}
}
