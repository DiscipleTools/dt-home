<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Apps;
use DT\Home\Services\RolesPermissions;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;
use function DT\Home\extract_request_input;
use function DT\Home\get_plugin_option;
use function DT\Home\magic_url;
use function DT\Home\namespace_string;
use function DT\Home\response;
use function DT\Home\template;

/**
 * Class AppController
 *
 * Controls the display of application details.
 */
class AppController
{

    private $apps;

    private $user_apps;

    private $roles_permissions;

    public function __construct( Apps $apps, UserApps $user_apps, RolesPermissions $roles_permissions )
    {
        $this->apps = $apps;
        $this->user_apps = $user_apps;
        $this->roles_permissions = $roles_permissions;
    }

    /**
     * Display the app launcher
     *
     * @param Request $request The request object.
     * @param array $params The parameters.
     */
    public function index( Request $request, $params )
    {

        $key = $params['key'];
        $user = get_current_user_id();
        $apps_array = $this->apps->for( $user );
        $data = json_encode( $apps_array );
        $hidden_data = $data;
        $app_url = magic_url( '', $key );
        $magic_link = $app_url . '/share';
        $reset_apps = get_plugin_option( 'reset_apps' );
        $button_color = get_plugin_option( 'button_color' );

        return template(
            'index',
            compact(
                'user',
                'data',
                'app_url',
                'magic_link',
                'hidden_data',
                'reset_apps',
                'button_color'
            )
        );
    }

    /**
     * Displays the app based on the provided slug.
     *
     * @param Request $request The request object.
     * @param array $params
     *
     * @return ResponseInterface The response object.
     */
    public function show( Request $request, $params )
    {
        // Fetch the app
        $slug = $params['slug'];
        $user_id = get_current_user_id();
        $app = $this->apps->find_for( $slug, $user_id );

        // If no initial hit, attempt a direct search.
        if ( !$app ) {
            $filtered_array = array_values(array_filter($this->apps->for( $user_id ), function ( $element ) use ( $slug ) {
                return ( isset( $element['slug'] ) && $element['slug'] === $slug );
            }));

            $app = !empty( $filtered_array[0] ) ? $filtered_array[0] : null;
        }

        // Also confirm user has relevant permission to access app.
        if ( !$app || !container()->get( RolesPermissions::class )->has_permission( $app, $user_id, get_option( RolesPermissions::OPTION_KEY_CUSTOM_ROLES, [] ) ) ) {
            return response( __( 'Not Found', 'dt-home' ), 404 );
        }

        // Check if there is a custom action to render the app
        $action = has_action( 'dt_home_render' );
        if ( $action ) {
            add_action(
                namespace_string( 'filter_asset_queue' ),
                function ( $queue ) use ( $app ) {
                    // Don't filter assets
                }
            );
            do_action( 'dt_home_app_render', $app );
        }

        // Check if the app has a custom template
        $html = apply_filters( 'dt_home_app_template', '', $app );

        if ( $html ) {
            if ( $html instanceof ResponseInterface ) {
                return $html;
            }

            return response( $html );
        }

        // Check to see if the app has an iframe URL
        $url = apply_filters( 'dt_home_webview_url', ( $app['url'] ?? '' ), $app );
        if ( !$url ) {
            // No URL found 404
            return response( __( 'Not Found', 'dt-home' ), 404 );
        }

        return template( 'web-view', compact( 'app', 'url' ) );
    }


    /**
     * This method is responsible for updating the "is_hidden" status of an app.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing the rendered template.
     */
    public function hide( Request $request )
    {
        $params = extract_request_input( $request );
        $result = $this->user_apps->hide( $params['slug'] );

        if ( !isset( $result['is_hidden'] ) || !$result['is_hidden'] ) {
            return response( [ 'message' => 'Failed to update visibility.' ], 500 );
        }

        return response( [ 'message' => 'App visibility updated' ] );
    }

    /**
     * This method is responsible for updating the "is_hidden" status of an app.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface
     */
    public function unhide( Request $request )
    {
        $params = extract_request_input( $request );
        $result = $this->user_apps->unhide( $params['slug'] );

        if ( !isset( $result['is_hidden'] ) || $result['is_hidden'] ) {
            return response( [ 'message' => 'Failed to update visibility.' ], 500 );
        }

        return response( [ 'message' => 'App visibility updated' ] );
    }

    /**
     * Updates the app order based on the provided request data.
     *
     * @param Request $request The request object containing the app order data.
     *
     * @return ResponseInterface
     */
    public function reorder( Request $request )
    {
        $data = extract_request_input( $request );

        // Iterate through each app in the data
        foreach ( $data as $key => $app ) {
            // Update the 'sort' field for each app based on its position in the array
            $data[$key]['sort'] = ( $key + 1 );
        }

        // Save the updated app order back to the database or storage
        update_user_option( get_current_user_id(), 'dt_home_apps', $data );

        return response( [ 'message' => 'App order updated' ] );
    }

    /**
     * Resets the user's apps by clearing the 'dt_home_apps' option
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing a success message.
     */
    public function reset_apps( Request $request )
    {
        update_user_option( get_current_user_id(), 'dt_home_apps', container()->get( SettingsApps::class )->raw() );

        return response( [ 'message' => 'App order updated' ] );
    }

    /**
     * Stores the user's apps by updating the 'dt_home_apps' option
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing a success message.
     */
    public function store_apps( Request $request )
    {
        $data = extract_request_input( $request );
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $roles = dt_recursive_sanitize_array( $user_roles ?? [] );
        $apps_array = $this->apps->from( 'user' );
        $apps_count = count( $apps_array );
        $app_data = [
            'name' => $data['name'] ?? 'test',
            'type' => $data['type'] ?? 'Web View',
            'icon' => $data['icon'] ?? '',
            'url' => $data['url'],
            'slug' => $data['slug'],
            'open_in_new_tab' => $data['open_in_new_tab'] ?? false,
            'sort' => $apps_count,
            'roles' => $roles,
        ];
        $apps_array = $this->apps->from( 'user' );
        $apps_array[] = $app_data;
        $this->user_apps->save( $apps_array );

        return response( [ 'message' => 'App has been added', 'app' => $app_data ] );
    }

    /**
     * Updates the user's apps by updating the 'dt_home_apps' option
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing a success message.
     */
    public function update_apps( Request $request, $params )
    {
        $data = extract_request_input( $request );
        $slug = $params['slug'];
        $apps_array = $this->apps->from( 'user' );
        foreach ( $apps_array as $key => $app ) {
            if ( $app['slug'] == $slug ) {
                $apps_array[$key] = [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'icon' => $data['icon'],
                    'url' => $data['url'],
                    'slug' => $data['slug'],
                    'open_in_new_tab' => $data['open_in_new_tab'] ?? false,
                    'sort' => $app['sort'],
                    'creation_type' => $app['creation_type'],
                    'is_hidden' => $app['is_hidden'],
                ];
                break; // Stop the loop once the app is found and updated
            }
        }
        $this->user_apps->save( $apps_array );

        return response( [ 'message' => 'App has been updated', 'app' => $data ] );
    }

    /**
     * Fetches all the apps for the current user.
     *
     * @return ResponseInterface The response containing the user's apps.
     */
    public function all()
    {
        $user = get_current_user_id();
        $apps_array = $this->apps->for( $user );
        $data = json_encode( $apps_array );
        return response( $data );
    }
}
