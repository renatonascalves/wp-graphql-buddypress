<?php
/**
 * XProfileFieldOptionsConnectionResolver Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Connection\Resolvers
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Connection\Resolvers;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

class XProfileFieldOptionsConnectionResolver {

	public static function resolve( $source, array $args, AppContext $context ) {

		if ( ! method_exists( $source->options, 'get_children' ) ) {
			return null;
		}

		$fields = array_map(
			function( $item ) use ( $context ) {
				return Factory::resolve_xprofile_field_object( $item->id, $context );
			},
			$source->options->get_children()
		);

		$connection = Relay::connectionFromArray( $fields, $args );

		$nodes = [];
		if ( ! empty( $connection['edges'] ) && is_array( $connection['edges'] ) ) {
			foreach ( $connection['edges'] as $edge ) {
				$nodes[] = ! empty( $edge['node'] ) ? $edge['node'] : null;
			}
		}

		$connection['nodes'] = ! empty( $nodes ) ? $nodes : null;

		return ! empty( $fields ) ? $connection : null;
	}
}
