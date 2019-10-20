<?php
/**
 * Registers BuddyPress types to the schema.
 *
 * @package \WPGraphQL\Extensions\BuddyPress
 * @since   0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress;

/**
 * Class Type_Registry
 */
class Type_Registry {

	/**
	 * Registers actions related to type registry.
	 */
	public static function add_actions() {
		add_action( 'graphql_register_types', array( __CLASS__, 'graphql_register_types' ), 10 );
	}

	/**
	 * Registers BuddyPress types, connection, and mutations to GraphQL schema
	 */
	public static function graphql_register_types() {

		// Enumerations.
		\WPGraphQL\Extensions\BuddyPress\Type\WPEnum\GroupEnums::register();

		// InputObjects.

		// Objects.
		\WPGraphQL\Extensions\BuddyPress\Type\WPObject\Group_Type::register();

		// Object fields.

		// Connections.

		// Mutations.
	}
}
