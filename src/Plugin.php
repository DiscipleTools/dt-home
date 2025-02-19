<?php

namespace DT\Home;

use DT\Home\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Home\CodeZone\WPSupport\Rewrites\RewritesInterface;
use DT\Home\League\Container\Container;
use DT\Home\Services\Analytics;


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
    private Analytics $analytics;

    /**
     * Plugin constructor.
     *
     * @param Container $container
     * @param RewritesInterface $rewrites
     * @param ConfigInterface $config
     */
    public function __construct( Container $container, RewritesInterface $rewrites, ConfigInterface $config, Analytics $analytics )
    {
        $this->config = $config;
        $this->container = $container;
        $this->rewrites = $rewrites;
        $this->analytics = $analytics;
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
        add_action( 'admin_notices', [ $this, 'display_analytics_notice' ] );
        add_action( 'wp_ajax_dt_home_analytics_notice', [ $this, 'handle_analytics_notice_dismissal' ] );

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
        $this->analytics->event( 'plugin-activation', [ 'action' => 'start', 'lib_name' => __CLASS__ ] );
        $this->rewrites->refresh();
        $this->analytics->event( 'plugin-activation', [ 'action' => 'stop' ] );
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
        $this->analytics->event( 'plugin-deactivation', [ 'action' => 'start', 'lib_name' => __CLASS__ ] );
        $this->rewrites->flush();
        $this->analytics->event( 'plugin-deactivation', [ 'action' => 'stop' ] );
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
        // dd( 'DT Version is not up-to-date' );
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

    /**
     * Display the analytics permission notice.
     */
    public function display_analytics_notice()
    {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( get_plugin_option( 'dt_home_analytics_permission' ) ) {
            return;
        }

        $dismissed = get_user_meta( get_current_user_id(), 'dt_home_analytics_notice_dismissed', true );
        if ( $dismissed ) {
            return;
        }

        $install_notice_label = $this->config->get( 'assets.javascript_globals.translations.install_notice_label' );
        $allow_label = $this->config->get( 'assets.javascript_globals.translations.allow_label' );
        $dismiss_label = $this->config->get( 'assets.javascript_globals.translations.dismiss_label' );

        $notice = '<div class="notice notice-info is-dismissible dt-home-analytics-notice">';
        $notice .= '<p>' . esc_html( $install_notice_label ) . ' <a href="#" class="dt-home-analytics-allow">' . esc_html( $allow_label ) . '</a> or <a href="#" class="dt-home-analytics-dismiss">' . esc_html( $dismiss_label ) . '</a></p>';
        $notice .= '</div>';

        //phpcs:ignore
        echo $notice;
    }

    /**
     * Handle the dismissal of the analytics notice.
     */
    public function handle_analytics_notice_dismissal()
    {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['dt_home_analytics_action'] ) ) {
            if ( $_POST['dt_home_analytics_action'] === 'allow' ) {
                set_plugin_option( 'dt_home_analytics_permission', true );
            } elseif ( $_POST['dt_home_analytics_action'] === 'dismiss' ) {
                update_user_meta( get_current_user_id(), 'dt_home_analytics_notice_dismissed', true );
            }
        }

        // Ensure the response is sent back to the client
        wp_redirect( admin_url() );
        exit;
    }
}
