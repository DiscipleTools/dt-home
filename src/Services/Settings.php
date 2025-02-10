<?php

namespace DT\Home\Services;

use DT\Home\CodeZone\WPSupport\Router\RouteInterface;
use DT\Home\Psr\Http\Message\ServerRequestInterface;
use function DT\Home\container;
use function DT\Home\namespace_string;
use function DT\Home\routes_path;

/**
 * Class settings
 *
 * The Settings class is responsible for adding the
 * settings page to the WordPress admin area.
 * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
 */
class Settings
{

    /**
     * Register the admin menu.
     *
     * @return void
     */
    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'register_menu' ], 99 );
    }

    /**
     * Register the admin menu
     *
     * @return void
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     */
    public function register_menu(): void
    {
        $menu = add_submenu_page('dt_extensions',
            __( 'Home Screen', 'dt-home' ),
            __( 'Home Screen', 'dt-home' ),
            'manage_dt',
            'dt_home',
            [ $this, 'route' ]
        );

        add_filter(namespace_string( 'settings_tabs' ), function ( $menu ) {
            $menu[] = [
                'label' => __( 'General', 'dt-home' ),
                'tab' => 'general'
            ];
            $menu[] = [
                'label' => __( 'Apps', 'dt-home' ),
                'tab' => 'app'
            ];
            $menu[] = [
                'label' => __( 'Training Videos', 'dt-home' ),
                'tab' => 'training'
            ];
            $menu[] = [
                'label' => __( 'Reports', 'dt-home' ),
                'tab' => 'reports'
            ];

            return $menu;
        }, 10, 1);

        add_action( 'load-' . $menu, [ $this, 'load' ] );
    }

    /**
     * Loads the necessary scripts and styles for the admin area.
     *
     * This method adds an action hook to enqueue the necessary JavaScript when on the admin area.
     * The JavaScript files are enqueued using the `admin_enqueue_scripts` action hook.
     *
     * @return void
     */
    public function load(): void
    {
        container()->get( Assets::class )->enqueue();
    }

    /**
     * Resolve and render the view file.
     *
     * @return void
     */
    public function route(): void
    {
        $request = container()->get( ServerRequestInterface::class );
        $query = $request->getQueryParams();
        $tab = $query['tab'] ?? 'general';
        $route = container()->get( RouteInterface::class );
        $route->file( routes_path( 'settings.php' ) )
            ->resolve();
    }
}
