<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Cache;
use DT\Home\Services\RolesPermissions;
use function DT\Home\config;
use function DT\Home\extract_request_input;
use function DT\Home\get_plugin_option;
use function DT\Home\redirect;
use function DT\Home\set_plugin_option;
use function DT\Home\view;
use function DT\Home\container;

class GeneralSettingsController
{
    /**
     * Show the general settings admin tab
     *
     * @return ResponseInterface
     */
    public function show( Request $request )
    {
        $tab = 'general';
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = 'Home Settings';
        $dt_home_require_login = get_plugin_option( 'require_login' );
        $dt_home_reset_apps = get_plugin_option( 'reset_apps' );
        $dt_home_show_in_menu = get_plugin_option( 'show_in_menu' );
        $dt_home_button_color = get_plugin_option( 'button_color' );
        $dt_home_file_upload = get_plugin_option( 'custom_ministry_logo' ) ?? '';
        $dt_home_invite_others    = get_plugin_option( 'invite_others' );

        $dt_home_use_capabilities = container()->get( RolesPermissions::class )->is_enabled();

        return view( 'settings/general', compact( 'tab', 'link', 'page_title', 'dt_home_require_login', 'dt_home_reset_apps', 'dt_home_use_capabilities', 'dt_home_button_color', 'dt_home_show_in_menu', 'dt_home_file_upload', 'dt_home_invite_others' ) );
    }

    /**
     * Update the general settings admin tab
     *
     * @param Request $request The Request object containing the parsed body data
     *
     * @return ResponseInterface The redirect response
     */
    public function update( Request $request )
    {
        $input = extract_request_input( $request );
        $require_user = $input['dt_home_require_login'] ?? 'off';
        $reset_apps = $input['dt_home_reset_apps'] ?? 'off';
        $dt_home_use_capabilities = $input['dt_home_use_capabilities'] ?? 'off';
        $dt_home_show_in_menu = $input['dt_home_show_in_menu'] ?? 'off';
        $button_color = $input['dt_home_button_color'] ?? config( 'options.defaults.button_color' );
        $dt_home_file_upload = $input['dt_home_custom_ministry_logo'] ?? '';
        $invite_others   = $input['dt_home_invite_others'] ?? 'off';

        set_plugin_option( 'require_login', $require_user === 'on' );
        set_plugin_option( 'reset_apps', $reset_apps === 'on' );
        set_plugin_option( 'button_color', $button_color );
        set_plugin_option( 'show_in_menu', $dt_home_show_in_menu === 'on' );
        set_plugin_option( 'custom_ministry_logo', $dt_home_file_upload );
        set_plugin_option( 'invite_others', $invite_others === 'on' );

        container()->get( RolesPermissions::class )->enabled( $dt_home_use_capabilities === 'on' );

        container()->get( Cache::class )->delete( [ 'custom_ministry_logo' ] );

        $redirect_url = add_query_arg( 'message', 'updated', admin_url( 'admin.php?page=dt_home' ) );

        return redirect( $redirect_url );
    }
}
