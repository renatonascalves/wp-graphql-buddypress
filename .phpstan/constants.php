<?php
/**
 * Constants defined in this file are to help phpstan analyze code where constants outside the plugin (WordPress core constants, etc) are being used.
 *
 * @package WPGraphQL\Extensions\BuddyPress
 */

define( 'WP_LANG_DIR', true );
define( 'SAVEQUERIES', true );
define( 'WP_PLUGIN_DIR', true );
define( 'WPGRAPHQL_PLUGIN_URL', true );
define( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR', true );

if ( ! defined( 'BP_DIR' ) ) {
	define( 'BP_DIR', dirname( dirname( dirname( __FILE__ ) ) ) . '/buddypress' );
}

require_once BP_DIR . '/bp-loader.php';
require_once BP_DIR . '/src/bp-friends/bp-friends-functions.php';
require_once BP_DIR . '/src/bp-friends/classes/class-bp-friends-friendship.php';
require_once BP_DIR . '/src/bp-blogs/bp-blogs-functions.php';
require_once BP_DIR . '/src/bp-blogs/bp-blogs-template.php';
require_once BP_DIR . '/src/bp-activity/bp-activity-functions.php';
require_once BP_DIR . '/src/bp-members/bp-members-functions.php';

// Groups component.
require_once BP_DIR . '/src/bp-groups/classes/class-bp-groups-group.php';
require_once BP_DIR . '/src/bp-groups/bp-groups-functions.php';
require_once BP_DIR . '/src/bp-groups/bp-groups-template.php';

// Messages component.
require_once BP_DIR . '/src/bp-messages/classes/class-bp-messages-box-template.php';
require_once BP_DIR . '/src/bp-messages/classes/class-bp-messages-message.php';
require_once BP_DIR . '/src/bp-messages/classes/class-bp-messages-thread.php';
require_once BP_DIR . '/src/bp-messages/bp-messages-template.php';
require_once BP_DIR . '/src/bp-messages/bp-messages-functions.php';
require_once BP_DIR . '/src/bp-messages/bp-messages-star.php';

// XProfile Component.
require_once BP_DIR . '/src/bp-xprofile/classes/class-bp-xprofile-field.php';
require_once BP_DIR . '/src/bp-xprofile/classes/class-bp-xprofile-profiledata.php';
require_once BP_DIR . '/src/bp-xprofile/classes/class-bp-xprofile-group.php';
require_once BP_DIR . '/src/bp-xprofile/bp-xprofile-functions.php';
require_once BP_DIR . '/src/bp-xprofile/bp-xprofile-template.php';

if ( ! defined( 'WPGRAPHQL_PLUGIN_DIR' ) ) {
	define( 'WPGRAPHQL_PLUGIN_DIR', dirname( dirname( dirname( __FILE__ ) ) ) . '/wp-graphql' );
}

require_once WPGRAPHQL_PLUGIN_DIR . '/vendor/autoload.php';
