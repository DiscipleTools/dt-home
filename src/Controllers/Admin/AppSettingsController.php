<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Apps;
use DT\Home\Services\RolesPermissions;
use DT\Home\Services\SVGIconService;
use DT\Home\Sources\SettingsApps;
use function DT\Home\extract_request_input;
use function DT\Home\redirect;
use function DT\Home\response;
use function DT\Home\view;

class AppSettingsController
{

    private Apps $apps;
    private SettingsApps $settings_apps;
    private RolesPermissions $roles_permissions;

    public function __construct( Apps $apps, SettingsApps $source, RolesPermissions $roles_permissions )
    {
        $this->apps = $apps;
        $this->settings_apps = $source;
        $this->roles_permissions = $roles_permissions;
    }

    /**
     * Show the apps settings app tab.
     *
     * @return ResponseInterface
     */
    public function show()
    {

        $tab = "app";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        $data = $this->settings_apps->undeleted();

        return view( "settings/app", compact( 'tab', 'link', 'page_title', 'data' ) );
    }

    /**
     * Show the available apps tab.
     * @return ResponseInterface
     */

    public function show_available_apps()
    {

        $tab = "app";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        $data = $this->settings_apps->deleted();

        return view( "settings/available-apps", compact( 'tab', 'link', 'page_title', 'data' ) );
    }

    /**
     * Show the form to create a new app.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $tab = "app";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";
        $svg_service = new SVGIconService( get_template_directory() . '/dt-assets/images/' );
        $svg_images = $svg_service->get_svg_icon_urls();

        return view( "settings/create", compact( 'tab', 'link', 'page_title', 'svg_images' ) );
    }

    /**
     * Store a new app in the database.
     *
     * @param Request $request The request object containing the form data.
     *
     * @return ResponseInterface
     */
    public function store( Request $request )
    {
        // Retrieve form data
        $input = extract_request_input( $request );

        $name = sanitize_text_field( $input['name'] ?? '' );
        $type = sanitize_text_field( $input['type'] ?? '' );
        $creation_type = sanitize_text_field( $input['creation_type'] ?? '' );
        $icon = sanitize_text_field( $input['icon'] ?? '' );
        $icon_color = sanitize_text_field( $input['icon_color'] ?? '' );
        $is_icon_color_deleted = sanitize_text_field( $input['icon_color_hidden'] ?? '' ) === 'deleted';
        $icon_dark = sanitize_text_field( $input['icon_dark'] ?? '' );
        $icon_dark_color = sanitize_text_field( $input['icon_dark_color'] ?? '' );
        $is_icon_dark_color_deleted = sanitize_text_field( $input['icon_dark_color_hidden'] ?? '' ) === 'deleted';
        $url = sanitize_text_field( $input['url'] ?? '' );
        $fallback_url_ios = sanitize_text_field( $input['fallback_url_ios'] ?? '' );
        $fallback_url_android = sanitize_text_field( $input['fallback_url_android'] ?? '' );
        $fallback_url_others = sanitize_text_field( $input['fallback_url_others'] ?? '' );
        $slug = sanitize_text_field( $input['slug'] ?? '' );
        $sort = sanitize_text_field( $input['sort'] ?? '' );
        $is_hidden = filter_var( $input['is_hidden'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $is_exportable = filter_var( $input['is_exportable'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $open_in_new_tab = filter_var( $input['open_in_new_tab'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $roles = dt_recursive_sanitize_array( $input['roles'] ?? [] );
        $deleted_roles = json_decode( stripslashes_from_strings_only( $input['deleted_roles'] ?? '[]' ) );

        // Prepare the data to be stored
        $app_data = [
            'name' => $name,
            'type' => $type,
            'creation_type' => $creation_type,
            'icon' => $icon,
            'icon_color' => $is_icon_color_deleted ? null : $icon_color,
            'icon_dark' => $icon_dark,
            'icon_dark_color' => $is_icon_dark_color_deleted ? null : $icon_dark_color,
            'url' => $url,
            'fallback_url_ios' => $fallback_url_ios,
            'fallback_url_android' => $fallback_url_android,
            'fallback_url_others' => $fallback_url_others,
            'sort' => $sort,
            'slug' => $slug,
            'is_hidden' => $is_hidden == "1" ? 1 : 0,
            'is_exportable' => $is_exportable == "1" ? 1 : 0,
            'open_in_new_tab' => $open_in_new_tab,
            'roles' => $roles
        ];

        // Get the existing apps array
        $apps_array = $this->settings_apps->raw();

        // Avoid duplicate slugs and append unique counter if required.
        $dup_apps = array_filter($apps_array, function ( $app ) use ( $app_data ) {

            // Check for identical matches, as well as for previously set slugs, with appended counts.
            return ( isset( $app['slug'] ) && ( ( $app['slug'] === $app_data['slug'] ) || ( substr( $app['slug'], 0, strlen( $app_data['slug'] . '_' ) ) === ( $app_data['slug'] . '_' ) ) ) );
        });
        if ( count( $dup_apps ) > 0 ) {
            $app_data['slug'] .= '_' . ( count( $dup_apps ) + 1 );
        }

        // Append new app data to the array
        $apps_array[] = $app_data;

        $this->settings_apps->save( $apps_array );

        // Update global roles and permissions.
        $this->roles_permissions->update( $slug, [ $this->roles_permissions->generate_permission_key( $slug ) ], $roles, $deleted_roles );

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
    public function unhide( Request $request, $params )
    {
        $result = $this->settings_apps->unhide( $params['slug'] );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=' . ( $result['is_hidden'] ? 'false' : 'true' ) );
    }

    /**
     * Hide an app based on its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The redirect response.
     */
    public function hide( Request $request, $params )
    {
        $result = $this->settings_apps->hide( $params['slug'] );

        return redirect( 'admin.php?page=dt_home&tab=app&updated=' . ( $result['is_hidden'] ? 'true' : 'false' ) );
    }

    /**
     * Updates the sort order of an app.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The RedirectResponse instance.
     */

    public function up( Request $request, $params )
    {
        return redirect( 'admin.php?page=dt_home&tab=app&updated=' . ( $this->apps->move( $params['slug'] ?? '', 'up' ) ? 'true' : 'false' ) );
    }

    /**
     * Move an app down in the list of apps.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface The RedirectResponse instance.
     */
    public function down( Request $request, $params )
    {
        return redirect( 'admin.php?page=dt_home&tab=app&updated=' . ( $this->apps->move( $params['slug'] ?? '', 'down' ) ? 'true' : 'false' ) );
    }

    /**
     * Update an app.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function update( Request $request, $params )
    {

        $slug = $params['slug'] ?? '';
        $input = extract_request_input( $request );
        $name = sanitize_text_field( $input['name'] ?? '' );
        $type = sanitize_text_field( $input['type'] ?? '' );
        $creation_type = sanitize_text_field( $input['creation_type'] ?? '' );
        $icon = sanitize_text_field( $input['icon'] ?? '' );
        $icon_color = sanitize_text_field( $input['icon_color'] ?? '' );
        $is_icon_color_deleted = sanitize_text_field( $input['icon_color_hidden'] ?? '' ) === 'deleted';
        $icon_dark = sanitize_text_field( $input['icon_dark'] ?? '' );
        $icon_dark_color = sanitize_text_field( $input['icon_dark_color'] ?? '' );
        $is_icon_dark_color_deleted = sanitize_text_field( $input['icon_dark_color_hidden'] ?? '' ) === 'deleted';
        $url = sanitize_text_field( $input['url'] ?? '' );
        $fallback_url_ios = sanitize_text_field( $input['fallback_url_ios'] ?? '' );
        $fallback_url_android = sanitize_text_field( $input['fallback_url_android'] ?? '' );
        $fallback_url_others = sanitize_text_field( $input['fallback_url_others'] ?? '' );
        $new_slug = sanitize_text_field( $input['slug'] ?? '' );
        $is_hidden = filter_var( $input['is_hidden'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $is_exportable = filter_var( $input['is_exportable'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $open_in_new_tab = filter_var( $input['open_in_new_tab'] ?? '0', FILTER_SANITIZE_NUMBER_INT );
        $roles = dt_recursive_sanitize_array( $input['roles'] ?? [] );
        $deleted_roles = json_decode( stripslashes_from_strings_only( $input['deleted_roles'] ?? '[]' ) );

        // Retrieve the existing array of apps
        $apps_array = $this->settings_apps->raw();

        // Find and update the app in the array
        foreach ( $apps_array as $key => $app ) {
            if ( $app['slug'] == $slug ) {
                $apps_array[$key] = [
                    'name' => $name,
                    'type' => $type,
                    'creation_type' => $creation_type,
                    'icon' => $icon,
                    'icon_color' => $is_icon_color_deleted ? null : $icon_color,
                    'icon_dark' => $icon_dark,
                    'icon_dark_color' => $is_icon_dark_color_deleted ? null : $icon_dark_color,
                    'url' => $url,
                    'fallback_url_ios' => $fallback_url_ios,
                    'fallback_url_android' => $fallback_url_android,
                    'fallback_url_others' => $fallback_url_others,
                    'slug' => $new_slug,
                    'sort' => $app['sort'] ?? '',
                    'is_hidden' => $is_hidden == "1" ? 1 : 0,
                    'is_exportable' => $is_exportable == "1" ? 1 : 0,
                    'open_in_new_tab' => $open_in_new_tab,
                    'roles' => $roles
                ];
                break; // Stop the loop once the app is found and updated
            }
        }

        $this->settings_apps->save( $apps_array );

        // Update global roles and permissions.
        $this->roles_permissions->update( $slug, [ $this->roles_permissions->generate_permission_key( $slug ) ], $roles, $deleted_roles );

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
    public function edit( Request $request, $params )
    {
        $existing_data = $this->apps->find( $params['slug'] ?? '' );

        $tab = "app";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";
        $svg_service = new SVGIconService( get_template_directory() . '/dt-assets/images/' );
        $svg_images = $svg_service->get_svg_icon_urls();

        if ( !$existing_data ) {
            return response( __( 'App not found', 'dt-home' ), 404 );
        }

        // Load the edit form view and pass the existing data
        return view( "settings/edit", compact( 'existing_data', 'link', 'tab', 'page_title', 'svg_images' ) );
    }


    /**
     * Delete an app by its slug.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     * @return ResponseInterface
     */
    public function delete( Request $request, array $params ): ResponseInterface
    {
        $this->settings_apps->destroy( $params['slug'] ?? '' );

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
    public function soft_delete_app( Request $request, array $params ): ResponseInterface
    {
        return redirect( 'admin.php?page=dt_home&tab=app&updated=' . ( $this->settings_apps->delete( $params['slug'] ?? '' ) ? 'true' : 'false' ) );
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
    public function restore_app( Request $request, array $params ): ResponseInterface
    {
        $slug = $params['slug'] ?? '';

        if ( empty( $slug ) ) {
            return redirect( 'admin.php?page=dt_home&tab=app&updated=false' );
        }

        // Restore identified app.
        $restored_apps = array_map(function ( $app ) use ( $slug ) {
            if ( $slug === $app['slug'] ?? '' ) {
                $app['is_deleted'] = false;
            }

            return $app;
        }, $this->settings_apps->deleted());

        // Return restored app back into the fold.
        $apps = $this->settings_apps->sort(array_merge( $restored_apps, $this->settings_apps->undeleted() ), [
            'reset' => true
        ]);

        // Save the updated array back to the option
        $this->settings_apps->save( $apps );

        // Redirect to the page with a success message
        return redirect( 'admin.php?page=dt_home&tab=app&action=available_app&updated=true' );
    }

    /**
     * This function handles import requests.
     *
     * @param Request $request The request instance.
     * @param array $params The route parameters.
     *
     */
    public function import( Request $request, array $params )
    {
        return response([
            'success' => $this->apps->import( extract_request_input( $request ) )
        ], 200, [ 'Content-Type' => 'application/json' ]);
    }
}
