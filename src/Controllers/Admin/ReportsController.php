<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\RolesPermissions;
use function DT\Home\config;
use function DT\Home\extract_request_input;
use function DT\Home\get_plugin_option;
use function DT\Home\redirect;
use function DT\Home\set_plugin_option;
use function DT\Home\view;
use function DT\Home\container;

class ReportsController
{
    public function report( Request $request )
    {
        $tab = 'reports';
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = 'Home Settings';

        return view( 'settings/reports/reports', compact( 'tab', 'link', 'page_title' ) );
    }
}
