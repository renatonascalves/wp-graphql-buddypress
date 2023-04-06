<?php // phpcs:disable

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
define( 'ABSPATH', dirname( __FILE__, 6 ) . '/' );

/*
 * Path to the theme to test with.
 */
define( 'WP_DEFAULT_THEME', 'twentytwenty' );

/*
 * Test with multisite enabled.
 * Alternatively, use the tests/phpunit/multisite.xml configuration file.
 */
// define( 'WP_TESTS_MULTISITE', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

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
define('AUTH_KEY',         'ru~EW? DN2Y-4~d@ S4$.w2]i-sfJ8hHi+0-* bIj%7j!Xg=p5Aap&2K#bTS5}It');
define('SECURE_AUTH_KEY',  'aIza+%`qW=DYdFC|zW:R|g5cR;8DUuJcSn}u@%GWnc6vp}N8rac.ezr[-pSb;jh<');
define('LOGGED_IN_KEY',    'ohzNHv@qON]fNZa=$IVzcAFf.G-}1A?uTu*|bT+ocUHdn+mHd+-=AU P]sEA,8*G');
define('NONCE_KEY',        'Xm`3yu{CDIGlyeUL0qOae3n-REPjO+/IHN:=f_]7a|N5mz1S.hnKYiGg,*Beb1U6');
define('AUTH_SALT',        '~bos&4+*ceyTR,tNxckFTWTD2]DJ4f_1s^xPl4a>Zig+fEs104@RauaE/!k=xEi4');
define('SECURE_AUTH_SALT', '7!nVL!+Bj%gQPm:Q|OaOqqD@%Q&.t!bOLJ)HWB#^xs;n<ngEj>d,@:3bzs4xGd1m');
define('LOGGED_IN_SALT',   'tt33 :89iL+)6pXVzm&?zu#W(5Ir2Tj!t7@qAr,#.~w]Il@]-#Z<S~F}W/:3/.]X');
define('NONCE_SALT',       'p8lEcu3nR35=3j4=>:LkVL+tP!|NeD07Q8(+REC^pg/$V=4|FuL?x$G|XRz%mliB');

$table_prefix = 'wptests_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Site' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
