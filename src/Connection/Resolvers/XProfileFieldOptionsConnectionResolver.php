<?php
/**
 * XProfileFieldOptionsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

/**
 * Class XProfileFieldOptionsConnectionResolver
 */
class XProfileFieldOptionsConnectionResolver {

	/**
	 * XProfile Field Options Resolver.
	 *
	 * @param XProfileField $source  Source.
	 * @param array         $args    Query args to pass to the connection resolver.
	 * @param AppContext    $context The context of the query to pass along.
	 * @return array|null
	 */
	public static function resolve( XProfileField $source, array $args, AppContext $context ): ?array {

		if ( ! method_exists( $source->options, 'get_children' ) ) {
			return null;
		}

		// If the visibility is set to members only, return nothing.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'xprofile' ] ) ) {
			return null;
		}

		$fields = array_map(
			function ( $item ) use ( $context ) {
				return Factory::resolve_xprofile_field_object( $item->id, $context );
			},
			$source->options->get_children()
		);

		if ( empty( $fields ) ) {
			return null;
		}

		$connection = Relay::connectionFromArray( $fields, $args );

		$nodes = [];
		if ( ! empty( $connection['edges'] ) && is_array( $connection['edges'] ) ) {
			foreach ( $connection['edges'] as $edge ) {
				$nodes[] = ! empty( $edge['node'] ) ? $edge['node'] : null;
			}
		}

		$connection['nodes'] = ! empty( $nodes ) ? $nodes : null;

		return $connection;
	}
}
