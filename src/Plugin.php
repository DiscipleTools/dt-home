<?php

namespace DT\Home;

use DT\Home\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Home\CodeZone\WPSupport\Rewrites\RewritesInterface;
use DT\Home\League\Container\Container;

/**
 * This is the entry-object for the plugin.
 * Handle any setup and bootstrapping here.
 */
class Plugin
{
    public Container $container;
    public ConfigInterface $config;
    public RewritesInterface $rewrites;
    public static $instance;

    /**
     * Plugin constructor.
     *
     * @param Container $container
     * @param RewritesInterface $rewrites
     * @param ConfigInterface $config
     */
    public function __construct( Container $container, RewritesInterface $rewrites, ConfigInterface $config )
    {
        $this->config = $config;
        $this->container = $container;
        $this->rewrites = $rewrites;
    }

    /**
     * Get the instance of the plugin
     * @return void
     */
    public function init()
    {
        register_activation_hook( plugin_path( 'dt-home.php' ), [ $this, 'activation_hook' ] );
        register_deactivation_hook( plugin_path( 'dt-home.php' ), [ $this, 'deactivation_hook' ] );

        static::$instance = $this;

        add_action( 'init', [ $this, 'wp_init' ] );
        add_action( 'wp_loaded', [ $this, 'wp_loaded' ], 20 );
        add_filter( 'dt_plugins', [ $this, 'dt_plugins' ] );
        add_action( 'activated_plugin', [ $this, 'activation_hook' ] );

        foreach ( $this->config->get( 'services.providers' ) as $provider ) {
            $this->container->addServiceProvider( $this->container->get( $provider ) );
        }
    }

    /**
     * Get the directory path of the plugin.
     *
     * This method returns the absolute directory path of the plugin, excluding the "/src" directory
     *
     * @return string The directory path of the plugin.
     */
    public static function dir_path()
    {
        return '/' . trim( str_replace( '/src', '', plugin_dir_path( __FILE__ ) ), '/' );
    }

    /**
     * Initialize the WordPress plugin.
     *
     * This method is a hook that is triggered when WordPress is initialized.
     * It calls the `sync()` method to synchronize any necessary changes
     * or updates with the plugin's rewrites. This can include adding, modifying
     * or removing rewrite rules.
     *
     * @return void
     */
    public function wp_init()
    {
        $this->rewrites->sync();
    }

    /**
     * Activate the plugin.
     *
     * This method is a hook that is triggered when the plugin is activated.
     * It calls the `rewrite_rules()` method to add or modify rewrite rules
     * and then flushes the rewrite rules to update them.
     */
    public function activation_hook()
    {
        $this->rewrites->refresh();
    }

    /**
     * Deactivate the plugin.
     *
     * This method is a hook that is triggered when the plugin is deactivated.
     * It calls the `rewrite_rules()` method to add or modify rewrite rules
     * and then flushes the rewrite rules to update them.
     */
    public function deactivation_hook()
    {
        $this->rewrites->flush();
    }

    /**
     * Runs after wp_loaded
     * @return void
     */
    public function wp_loaded(): void
    {
        if ( !$this->is_dt_version() ) {
            add_action( 'admin_notices', [ $this, 'admin_notices' ] );
            add_action( 'wp_ajax_dismissed_notice_handler', [ $this, 'ajax_notice_handler' ] );

            return;
        }

        if ( !$this->is_dt_theme() ) {
            return;
        }

        if ( !defined( 'DT_FUNCTIONS_READY' ) ) {
            require_once get_template_directory() . '/dt-core/global-functions.php';
        }
    }

    /**
     * is DT up-to-date?
     * @return bool
     */
    public function is_dt_version(): bool
    {
        if ( !$this->is_dt_theme() ) {
            return false;
        }

        $wp_theme = wp_get_theme();

        return version_compare( $wp_theme->version, $this->config->get( 'plugin.dt_version' ), '>=' );
    }

    /**
     * Is the DT Theme installed?
     * @return bool
     */
    protected function is_dt_theme(): bool
    {
        return class_exists( 'Disciple_Tools' );
    }

    /**
     * Register the plugin with disciple.tools
     * @return array
     */
    public function dt_plugins(): array
    {
        $plugin_data = get_file_data(__FILE__, [
            'Version' => '0.0',
            'Plugin Name' => 'DT Plugin',
        ], false);

        $plugins['dt-plugin'] = [
            'plugin_url' => trailingslashit( plugin_dir_url( __FILE__ ) ),
            'version' => $plugin_data['Version'] ?? null,
            'name' => $plugin_data['Plugin Name'] ?? null,
        ];

        return $plugins;
    }
}
