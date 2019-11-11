<?php
/**
 * Registers BuddyPress types to the schema.
 *
 * @package \WPGraphQL\Extensions\BuddyPress
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress;

/**
 * Class TypeRegistry
 */
class TypeRegistry {

	/**
	 * Registers actions related to type registry.
	 */
	public static function add_actions() {
		add_action( 'graphql_register_types', array( __CLASS__, 'graphql_register_types' ), 10 );
	}

	/**
	 * Registers BuddyPress types, connection, and mutations to GraphQL schema.
	 */
	public static function graphql_register_types() {

		// Groups component.
		if ( bp_is_active( 'groups' ) ) {

			// Enums.
			\WPGraphQL\Extensions\BuddyPress\Type\WPEnum\GroupEnums::register();

			// Objects.
			\WPGraphQL\Extensions\BuddyPress\Type\WPObject\GroupType::register();

			// Connections.
			\WPGraphQL\Extensions\BuddyPress\Connection\GroupConnection::register_connections();
		}

		// InputObjects.
		// Object fields.
		// Connections.
		// Mutations.
	}
}
