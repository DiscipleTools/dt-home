<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Apps;
use DT\Home\Services\RolesPermissions;
use function DT\Home\container;
use function DT\Home\extract_request_input;
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


    /**
     * Displays the app based on the provided slug.
     *
     * @param Request $request The request object.
     * @param array   $params
     *
     * @return ResponseInterface The response object.
     */
    public function show( Request $request, $params )
    {
        // Fetch the app
        $slug = $params['slug'];
        $apps = container()->get( Apps::class );
        $user_id = get_current_user_id();
        $apps_array = $apps->for_user( $user_id );
        $app  = $apps->find_for_user( $user_id, $slug );
dt_write_log('====================== AppController');
        // Also confirm user has relevant permission to access app.
        if ( ! $app || !container()->get( RolesPermissions::class )->has_permission( $app, $user_id, get_option( RolesPermissions::OPTION_KEY_CUSTOM_ROLES, [] ) ) ) {
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
        if ( ! $url ) {
            // No URL found 404
            return response( __( 'Not Found', 'dt-home' ), 404 );
        }

        return template( 'web-view', compact( 'app', 'url', 'apps_array' ) );
    }//end show()


    /**
     * Adds or updates a query parameter in a URL.
     *
     * @param string $url   The original URL.
     * @param string $key   The query parameter key.
     * @param string $value The query parameter value.
     *
     * @return string The updated URL.
     */
    private function add_or_update_query_param( $url, $key, $value )
    {
        // Split the URL into the base and the query string
        $url_parts    = explode( '?', $url, 2 );
        $base_url     = $url_parts[0];
        $query_string = ( $url_parts[1] ?? '' );

        // Parse the query string into an associative array
        parse_str( $query_string, $query_params );

        // Update the query parameters
        $query_params[$key] = $value;

        // Rebuild the query string
        $new_query_string = http_build_query( $query_params );

        return $base_url.'?'.$new_query_string;
    }//end add_or_update_query_param()


    /**
     * This method is responsible for updating the "is_hidden" status of an app.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing the rendered template.
     */
    public function hide( Request $request )
    {
        $apps = container()->get( Apps::class );
        $data = extract_request_input( $request );

        $apps_array = $apps->for_user( get_current_user_id() );

        // Find the app with the specified slug and update its 'is_hidden' status
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $data['slug'] ) {
                $apps_array[$key]['is_hidden'] = 1;
                // Set 'is_hidden' to 1 (hide)
                break;
                // Exit the loop once the app is found and updated
            }
        }

        // Separate hidden and visible apps
        $hidden_apps  = [];
        $visible_apps = [];

        foreach ( $apps_array as $app ) {
            if ( $app['is_hidden'] == 1 ) {
                $hidden_apps[] = $app;
            } else {
                $visible_apps[] = $app;
            }
        }

        // Sort visible apps by the 'sort' field
        usort(
            $visible_apps,
            function ( $a, $b ) {
                return ( $a['sort'] <=> $b['sort'] );
            }
        );

        // Reset sort values for visible apps
        foreach ( $visible_apps as $index => $app ) {
            $visible_apps[$index]['sort'] = ( $index + 1 );
        }

        // Add hidden apps back to the end
        foreach ( $hidden_apps as $hidden_app ) {
            $hidden_app['sort'] = ( count( $visible_apps ) + 1 );
            $visible_apps[]     = $hidden_app;
        }

        // Save the updated array back to the option
        update_user_option( get_current_user_id(), 'dt_home_apps', $visible_apps );

        return response( [ 'message' => 'App visibility and order updated' ] );
    }//end hide()


    /**
     * This method is responsible for updating the "is_hidden" status of an app.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface
     */
    public function unhide( Request $request )
    {
        $apps = container()->get( Apps::class );
        $data = extract_request_input( $request );

        $apps_array = $apps->for_user( get_current_user_id() );

        // Find the app with the specified ID and update its 'is_hidden' status
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $data['slug'] ) {
                $apps_array[$key]['is_hidden'] = 0;
                // Set 'is_hidden' to 1 (hide)
                break;
                // Exit the loop once the app is found and updated
            }
        }

        // Separate hidden and visible apps
        $hidden_apps  = [];
        $visible_apps = [];

        foreach ( $apps_array as $app ) {
            if ( $app['is_hidden'] == 1 ) {
                $hidden_apps[] = $app;
            } else {
                $visible_apps[] = $app;
            }
        }

        // Sort visible apps by the 'sort' field
        usort(
            $visible_apps,
            function ( $a, $b ) {
                return ( $a['sort'] <=> $b['sort'] );
            }
        );

        // Reset sort values for visible apps
        foreach ( $visible_apps as $index => $app ) {
            $visible_apps[$index]['sort'] = ( $index + 1 );
        }

        // Add hidden apps back to the end
        foreach ( $hidden_apps as $hidden_app ) {
            $hidden_app['sort'] = ( count( $visible_apps ) + 1 );
            $visible_apps[]     = $hidden_app;
        }

        // Save the updated array back to the option
        update_user_option( get_current_user_id(), 'dt_home_apps', $visible_apps );

        return response( [ 'message' => 'App visibility updated' ] );
    }//end unhide()


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
    }//end reorder()


    /**
     * Resets the user's apps by clearing the 'dt_home_apps' option
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface The response containing a success message.
     */
    public function reset_apps( Request $request )
    {
        $apps       = container()->get( Apps::class );
        $admin_apps = $apps->all();

        update_user_option( get_current_user_id(), 'dt_home_apps', $admin_apps );

        return response( [ 'message' => 'App order updated' ] );
    }//end reset_apps()
}//end class
