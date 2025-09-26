<?php
/**
 * Plugin Name: D.T Home
 * Plugin URI: https://github.com/DiscipleTools/dt-home
 * Description: An app home screen for disciple.tools. Part of the DT Toolbox.
 * Text Domain: dt-home
 * Domain Path: /languages
 * Version:  1.2.1
 * Author URI: https://github.com/DiscipleTools
 * GitHub Plugin URI: https://github.com/DiscipleTools/dt-home
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 6.3
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */
use DT\Home\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Home\CodeZone\WPSupport\Container\ContainerFactory;
use DT\Home\Plugin;
use DT\Home\Providers\ConfigServiceProvider;
use DT\Home\Providers\PluginServiceProvider;
use DT\Home\Providers\RewritesServiceProvider;
use DT\Home\Services\RolesPermissions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Load dependencies
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-scoped/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';



// Create the IOC container
$container = ContainerFactory::singleton();

require_once plugin_dir_path( __FILE__ ) . 'src/helpers.php';

$boot_providers = [
    ConfigServiceProvider::class,
    RewritesServiceProvider::class,
    PluginServiceProvider::class
];

foreach ( $boot_providers as $provider ) {
    $container->addServiceProvider( $container->get( $provider ) );
}

// Init the plugin
$dt_home = $container->get( Plugin::class );
$dt_home->init();

$container->get( RolesPermissions::class )->init();

// Add the rest of the service providers
$config = $container->get( ConfigInterface::class );
foreach ( $config->get( 'services.providers' ) as $provider ) {
    $container->addServiceProvider( $container->get( $provider ) );
}
