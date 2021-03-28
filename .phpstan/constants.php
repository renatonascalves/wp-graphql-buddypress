<?php
/**
 * Constants defined in this file are to help phpstan analyze code where constants outside the plugin (WordPress core constants, etc) are being used.
 *
 * @package WPGraphQL\Extensions\BuddyPress
 */

define( 'WP_LANG_DIR', true );
define( 'SAVEQUERIES', true );
define( 'WPGRAPHQL_PLUGIN_URL', true );
define( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR', true );

if ( ! defined( 'BP_DIR' ) ) {
	define( 'BP_DIR', dirname( dirname( dirname( __FILE__ ) ) ) . '/BuddyPress' );
}
require_once BP_DIR . '/bp-loader.php';

if ( ! defined( 'WPGRAPHQL_PLUGIN_DIR' ) ) {
	define( 'WPGRAPHQL_PLUGIN_DIR', dirname( dirname( dirname( __FILE__ ) ) ) . '/wp-graphql' );
}
require_once WPGRAPHQL_PLUGIN_DIR . '/vendor/autoload.php';
