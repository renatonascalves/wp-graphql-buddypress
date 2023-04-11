<?php

/* Path to the WordPress codebase you'd like to test. Add a backslash in the end. */
define( 'ABSPATH', dirname( __FILE__, 6 ) . '/' );

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
defined( 'WP_DEFAULT_THEME' ) || define( 'WP_DEFAULT_THEME', 'default' );

/*
 * Test with multisite enabled.
 */
// define( 'WP_TESTS_MULTISITE', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** Database settings ** //

/*
 * This configuration file will be used by the copy of WordPress being tested.
 * wordpress/wp-config.php will be ignored.
 *
 * WARNING WARNING WARNING!
 * These tests will DROP ALL TABLES in the database with the prefix named below.
 * DO NOT use a production database or one that is shared with something else.
 */

define( 'DB_NAME', 'wordpress_unit_tests' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY',         'wMkwvZnrJ/6S *-?a7-AOP,0W#jsc+-8~=#br5!q^%K|UFn4)Lo&ik(4]9rnwp!W');
define('SECURE_AUTH_KEY',  '9lf6BWxZeu!Va@I@:RONXxGL(T`8tVnpV}5=Nk/EV04lhEg|^QH{zNpy~Z/,j<og');
define('LOGGED_IN_KEY',    'XTm8+z;Y-yvx}!h,(Q(~%[&yx_DpCs~|xe+U^,?~e5ce]UtvV@TOG8=)ZJC>eff.');
define('NONCE_KEY',        'pvN|+j_jitf=3rm^-l;U+sudRqv!Jh(P./SY1rfGREU4lu1V;|eY[s};fE{z(gN4');
define('AUTH_SALT',        'TH$DB^G,-s-[v79B=yUL1c}zv2+=3;ursKwOPCcWy5}~d|E;+~m)cnD]se,YnijX');
define('SECURE_AUTH_SALT', 'F:F<+~/%apUWqwu+_+<Q}{$l?S+s,>PSwB]b)bSOUAFh{np;-47rS%B-2hv2`ARG');
define('LOGGED_IN_SALT',   '6CmyG-XqE>~OaLVGW_{i$>;<a=du*`:nj-e7 nRi{j`7]Zs<5F+hIEvurv{w*I.o');
define('NONCE_SALT',       'NI!wIYxM#gW3%fihc*:=+^$4^8>AY|FAJb?? 0dPVcS+0JQ 8j:raaW)I`]-|xp;');

$table_prefix  = 'wptests_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );

