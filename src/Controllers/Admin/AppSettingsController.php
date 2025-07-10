<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Apps;
use DT\Home\Services\SVGIconService;
use function DT\Home\container;
use function DT\Home\extract_request_input;
use function DT\Home\get_plugin_option;
use function DT\Home\redirect;
use function DT\Home\response;
use function DT\Home\set_plugin_option;
use function DT\Home\view;

class AppSettingsController {

    /**
     * Show the general settings app tab.
     *
     * @return ResponseInterface
     */
    public function show() {

        $tab        = "app";
        $link       = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        $data = $this->get_all_apps_data();

        return view( "settings/app", compact( 'tab', 'link', 'page_title', 'data' ) );
    }

    /**
     * Show the available apps tab.
     * @return ResponseInterface
     */

    public function show_available_apps() {

        $tab        = "app";
        $link       = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        $data = $this->get_all_softdelete_apps();

        return view( "settings/available-apps", compact( 'tab', 'link', 'page_title', 'data' ) );
    }

    /**
     * Get all apps data from the options and ensure default values.
     *
     * @return array
     */
    protected function get_all_apps_data() {
        $apps = container()->get( Apps::class );

        // Get the apps array from the option
        $apps_collection = $apps->all();
        $apps_array      = array_values( array_filter( $apps_collection, function ( $app ) {
            if ( ! isset( $app['is_deleted'] ) ) {
                return true;
            }

            return false === $app['is_deleted'];
        } ) );

        usort( $apps_array, function ( $a, $b ) {
            return (int) $a['sort'] - (int) $b['sort'];
        } );

        $apps_array = array_map( function ( $app ) {
            return array_merge( [
                'name'      => '',
                'type'      => 'webview',
                'icon'      => '',
                'url'       => '',
                'sort'      => 0,
                'slug'      => '',
                'is_hidden' => false,
            ], $app );
        }, $apps_array );


        return $apps_array;
    }

    /**
     * Get all soft deleted apps data from the options and ensure default values.
     *
     * @return ResponseInterface
     */
    protected function get_all_softdelete_apps() {
        $apps = container()->get( Apps::class );
        // Get the apps array from the option
        $apps_collection = $apps->all();
        $apps_array      = array_values( array_filter( $apps_collection, function ( $app ) {
            if ( ! isset( $app['is_deleted'] ) ) {
                return false;
            }

            return true === $app['is_deleted'];
        } ) );
        // Sort the array based on the 'sort' key
        usort( $apps_array, function ( $a, $b ) {
            return $a['sort'] - $b['sort'];
        } );

        $apps_array = array_map( function ( $app ) {
            return array_merge( [
                'name'          => '',
                'type'          => 'Web View',
                'creation_type' => '',
                'icon'          => '',
                'url'           => '',
                'sort'          => 0,
                'slug'          => '',
                'is_hidden'     => false,
            ], $app );
        }, $apps_array );


        return $apps_array;
    }

    /**
     * Show the form to create a new app.
     *
     * @return ResponseInterface
     */
    public function create() {
        $tab         = "app";
        $link        = 'admin.php?page=dt_home&tab=';
        $page_title  = "Home Settings";
        $svg_service = new SVGIconService( get_template_directory() . '/dt-assets/images/' );

        return view( "settings/create", compact( 'tab', 'link', 'page_title' ) );
    }

    /**
     * Store a new app in the database.
     *
     * @param Request $request The request object containing the form data.
     *
     * @return ResponseInterface
     */
    public function store( Request $request ) {
        // Retrieve form data
        $input = extract_request_input( $request );

        $name            = sanitize_text_field( $input['name'] ?? '' );
        $type            = sanitize_text_field( $input['type'] ?? '' );
        $creation_type   = sanitize_text_field( $input['creation_type'] ?? '' );
        $icon            = sanitize_text_field( $input['icon'] ?? '' );
        $url             = sanitize_text_field( $input['url'] ?? '' );
        $slug            = sanitize_text_field( $input['slug'] ?? '' );
        $is_hidden       = filter_var( $input['is_hidden'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $open_in_new_tab = filter_var( $input['open_in_new_tab'] ?? '0', FILTER_SANITIZE_NUMBER_INT );

        $apps = container()->get( Apps::class );
        // Get the existing apps array
        $apps_array = $apps->all(); // Default to an empty array if the option does not exist

        // Find the highest sort value and add 1 to ensure new apps go to the end
        $max_sort = 0;
        foreach ( $apps_array as $app ) {
            if ( isset( $app['sort'] ) && (int) $app['sort'] > $max_sort ) {
                $max_sort = (int) $app['sort'];
            }
        }
        $new_sort = $max_sort + 1;

        // Prepare the data to be stored
        $app_data = [
            'name'            => $name,
            'type'            => $type,
            'creation_type'   => $creation_type,
            'icon'            => $icon,
            'url'             => $url,
            'sort'            => $new_sort,
            'slug'            => $slug,
            'is_hidden'       => $is_hidden == "1" ? 1 : 0,
            'open_in_new_tab' => $open_in_new_tab,
        ];

        // Avoid duplicate slugs and append unique counter if required.
        $dup_apps = array_filter( $apps_array, function ( $app ) use ( $app_data ) {

            // Check for identical matches, as well as for previously set slugs, with appended counts.
            return ( isset( $app['slug'] ) && ( ( $app['slug'] === $app_data['slug'] ) || ( substr( $app['slug'], 0, strlen( $app_data['slug'] . '_' ) ) === ( $app_data['slug'] . '_' ) ) ) );
        } );
        if ( count( $dup_apps ) > 0 ) {
            $app_data['slug'] .= '_' . ( count( $dup_apps ) + 1 );
        }

        // Append new app data to the array
        $apps_array[] = $app_data;

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Unhide an app.
     *
     * @param Request $request The request object.
     * @param mixed $params Additional parameters.
     *
     * @return ResponseInterface
     */
    public function unhide( Request $request, $params ) {
        // Retrieve the existing array of apps
        $slug = $params['slug'] ?? '';
        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();
        // Find the app with the specified ID and update its 'is_hidden' status
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {
                $apps_array[ $key ]['is_hidden'] = 0;
                break;
            }
        }
        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Hide an app based on its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The redirect response.
     */
    public function hide( Request $request, $params ) {
        // Retrieve the existing array of apps
        $slug = $params['slug'] ?? '';
        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();

        // Find the app with the specified ID and update its 'is_hidden' status
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {
                $apps_array[ $key ]['is_hidden'] = 1; // Set 'is_hidden' to 1 (hide)
                break; // Exit the loop once the app is found and updated
            }
        }

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Updates the sort order of an app.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The RedirectResponse instance.
     */

    public function up( Request $request, $params ) {
        $slug = $params['slug'] ?? '';
        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }

        // Retrieve the existing array of apps
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();

        // Find the index of the app and its current sort value
        $current_index = null;
        $current_sort  = null;
        foreach ( $apps_array as $key => $app ) {
            if ( $app['slug'] == $slug ) {
                $current_index = $key;
                $current_sort  = (int) $app['sort'];
                break;
            }
        }

        // Adjust the sort values
        foreach ( $apps_array as $key => &$app ) {
            if ( $app['sort'] == $current_sort - 1 ) {
                // Increment the sort value of the app that's currently one position above
                $app['sort']++;
            }
        }

        // Decrement the sort value of the current app
        if ( $current_sort > 0 ) {
            $apps_array[ $current_index ]['sort']--;
        }

        // Normalize the sort values to ensure they are positive and sequential
        usort( $apps_array, function ( $a, $b ) {
            return (int) $a['sort'] - (int) $b['sort'];
        } );

        foreach ( $apps_array as $key => &$app ) {
            $app['sort'] = $key;
        }

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Move an app down in the list of apps.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The RedirectResponse instance.
     */
    public function down( Request $request, $params ) {
        // Retrieve the existing array of apps
        $slug = $params['slug'] ?? '';
        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();

        // Find the index of the app and its current sort value
        $current_index = null;
        $current_sort  = null;
        foreach ( $apps_array as $key => $app ) {
            if ( $app['slug'] == $slug ) {
                $current_index = $key;
                $current_sort  = $app['sort'];
                break;
            }
        }

        // Determine the maximum sort value
        $max_sort = count( $apps_array );

        // Only proceed if the app was found and it's not already at the bottom
        if ( $current_index !== null && $current_sort < $max_sort ) {
            // Adjust the sort values
            foreach ( $apps_array as $key => &$app ) {
                if ( $app['sort'] == (int) $current_sort + 1 ) {
                    // Decrement the sort value of the app that's currently one position below
                    $app['sort']--;
                }
            }
            // Increment the sort value of the current app
            $apps_array[ $current_index ]['sort']++;

            // Re-sort the array
            usort( $apps_array, function ( $a, $b ) {
                return (int) $a['sort'] - (int) $b['sort'];
            } );

            foreach ( $apps_array as $key => &$app ) {
                $app['sort'] = $key;
            }

            // Save the updated array back to the option
            set_plugin_option( 'apps', $apps_array );

        }

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Update an app.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function update( Request $request, $params ) {

        $slug            = $params['slug'] ?? '';
        $input           = extract_request_input( $request );
        $name            = sanitize_text_field( $input['name'] ?? '' );
        $type            = sanitize_text_field( $input['type'] ?? '' );
        $creation_type   = sanitize_text_field( $input['creation_type'] ?? '' );
        $icon_url        = sanitize_text_field( $input['icon'] ?? '' );
        $url             = sanitize_text_field( $input['url'] ?? '' );
        $sort            = sanitize_text_field( $input['sort'] ?? '' );
        $new_slug        = sanitize_text_field( $input['slug'] ?? '' );
        $is_hidden       = filter_var( $input['is_hidden'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $open_in_new_tab = filter_var( $input['open_in_new_tab'] ?? '0', FILTER_SANITIZE_NUMBER_INT );

        // Retrieve the existing array of apps
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();

        // Find and update the app in the array
        foreach ( $apps_array as $key => $app ) {
            if ( $app['slug'] == $slug ) {
                $apps_array[ $key ] = [
                    'name'            => $name,
                    'type'            => $type,
                    'creation_type'   => $creation_type,
                    'icon'            => $icon_url,
                    'url'             => $url,
                    'slug'            => $new_slug,
                    'sort'            => $sort,
                    'is_hidden'       => $is_hidden == "1" ? 1 : 0,
                    'open_in_new_tab' => $open_in_new_tab,
                ];
                break; // Stop the loop once the app is found and updated
            }
        }

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Show the form to edit an existing app.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function edit( Request $request, $params ) {
        $slug        = $params['slug'] ?? '';
        $svg_service = new SVGIconService( get_template_directory() . '/dt-assets/images/' );

        $existing_data = $this->get_data_by_slug( $slug );

        $tab        = "app";
        $link       = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        if ( ! $existing_data ) {
            return response( __( 'App not found', 'dt-home' ), 404 );
        }

        // Load the edit form view and pass the existing data
        return view( "settings/edit", compact( 'existing_data', 'link', 'tab', 'page_title' ) );
    }

    /**
     * Get app data by ID.
     *
     * @param int $slug
     *
     * @return ResponseInterface
     */
    protected function get_data_by_slug( $slug ) {
        $apps_array = container()->get( Apps::class )->all();

        foreach ( $apps_array as $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {
                return $app;
            }
        }

        return null; // Return null if no app is found with the given slug
    }

    /**
     * Delete an app by its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function delete( Request $request, $params ) {
        $slug = $params['slug'] ?? '';

        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }

        // Retrieve the existing array of trainings
        $apps_array = get_plugin_option( 'apps' );

        // Find the app with the specified ID and remove it from the array
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {
                unset( $apps_array[ $key ] ); // Remove the app from the array
                break; // Exit the loop once the app is found and removed
            }
        }

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /** Soft delete an app by its slug.
     * This function marks an app as soft deleted based on its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function soft_delete_app( Request $request, $params ) {
        $slug = $params['slug'] ?? '';

        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }

        // Retrieve the existing array of apps
        $apps       = container()->get( Apps::class );
        $apps_array = $apps->all();

        // Find the app with the specified slug and mark it as soft deleted
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {

                $apps_array[ $key ]['is_deleted'] = true; // Mark the app as soft deleted
                break; // Exit the loop once the app is found and marked
            }
        }

        $apps->save( $apps_array );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }

    /**
     * Restore an app by its slug.
     *
     * This function restores a soft deleted app based on its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function restore_app( Request $request, $params ) {
        $slug = $params['slug'] ?? '';

        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }

        // Retrieve the existing array of apps
        $apps_array = get_option( 'dt_home_apps', [] );

        // Find the app with the specified slug and restore it
        foreach ( $apps_array as $key => $app ) {
            if ( isset( $app['slug'] ) && $app['slug'] == $slug ) {
                $apps_array[ $key ]['is_deleted'] = false; // Restore the app
                break; // Exit the loop once the app is found and restored
            }
        }

        // Save the updated array back to the option
        set_plugin_option( 'apps', $apps_array );

        // Redirect to the page with a success message
        return redirect( 'admin.php?page=dt_home&tab=app&action=available_app&updated=true' );
    }



    /**
     * Handle GET-based reorder requests.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface
     */
    public function reorder_get( Request $request )
    {
        $input = extract_request_input( $request );
        $ordered_slugs = isset( $input['ordered_ids'] ) ? explode( ',', $input['ordered_ids'] ) : [];

        if ( empty( $ordered_slugs ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&error=1' );
        }

        // Get current app data
        $apps = container()->get( Apps::class );
        $apps_array = $apps->all();
        
        // Create a lookup array for existing data
        $apps_lookup = [];
        foreach ( $apps_array as $app ) {
            if ( isset( $app['slug'] ) ) {
                $apps_lookup[$app['slug']] = $app;
            }
        }

        // Reorder based on the provided slugs and update sort values
        $reordered_apps = [];
        $processed_slugs = [];
        
        foreach ( $ordered_slugs as $index => $app_slug ) {
            if ( isset( $apps_lookup[$app_slug] ) ) {
                $app = $apps_lookup[$app_slug];
                $app['sort'] = $index + 1;
                $reordered_apps[] = $app;
                $processed_slugs[] = $app_slug;
            }
        }

        // Add any missing items to the end to prevent data loss
        foreach ( $apps_array as $app ) {
            if ( isset( $app['slug'] ) && !in_array( $app['slug'], $processed_slugs ) ) {
                $app['sort'] = count( $reordered_apps ) + 1;
                $reordered_apps[] = $app;
            }
        }

        // Save the updated app order back to the option
        set_plugin_option( 'apps', $reordered_apps );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=true' );
    }
}
