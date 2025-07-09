<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Disciple.Tools
 */
$_tests_dir   = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
$_core_dir    = getenv( 'WP_CORE_DIR' ) ? getenv( 'WP_CORE_DIR' ) : rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
$_theme_dir   = getenv( 'WP_THEME_DIR' ) ? getenv( 'WP_THEME_DIR' ) : $_core_dir . '/wp-content/themes/disciple-tools-theme';
// @phpcs:ignore
$_plugin_file = $_ENV['WP_PLUGIN_FILE'] ?? false ?: dirname( __DIR__ ) . '/' . basename( dirname( __DIR__ ) ) . '.php';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    echo "Could not find " . $_tests_dir . "/includes/functions.php, have you run tests/install-wp-tests.sh ?" . PHP_EOL; //@phpcs:ignore
    exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

// Polyfill for getallheaders() function which is not available in CLI environments
if ( ! function_exists( 'getallheaders' ) ) {
    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return array The HTTP header key/value pairs.
     */
    function getallheaders() {
        $headers = [];
        foreach ( $_SERVER as $name => $value ) {
            if ( substr( $name, 0, 5 ) == 'HTTP_' ) {
                $headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Registers theme
 */
$_register_theme = function () use ( $_tests_dir, $_core_dir, $_theme_dir, $_plugin_file ) {
    //The theme throws some errors because it is missing some tables even after migration,
    //but our tests still run properly without them.
    global $wpdb;
    $wpdb->suppress_errors( true );

    $current_theme = basename( $_theme_dir );
    $theme_root    = dirname( $_theme_dir );

    add_filter( 'theme_root', function () use ( $theme_root ) {
        return $theme_root;
    } );

    register_theme_directory( $theme_root );

    add_filter( 'pre_option_template', function () use ( $current_theme ) {
        return $current_theme;
    } );
    add_filter( 'pre_option_stylesheet', function () use ( $current_theme ) {
        return $current_theme;
    } );
    add_filter( 'init', function () {
        require_once get_template_directory() . '/dt-core/setup-functions.php';
        dt_setup_roles_and_permissions();
    }, 500, 0 );

    require $_plugin_file;
};

tests_add_filter( 'muplugins_loaded', $_register_theme );

// Enable user registration for tests
tests_add_filter( 'muplugins_loaded', function() {
    update_option( 'users_can_register', 1 );
} );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
