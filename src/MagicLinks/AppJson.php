<?php

namespace DT\Home\MagicLinks;

use DT\Home\League\Route\RouteCollectionInterface;
use function DT\Home\routes_path;

/**
 * Class AppJson
 *
 * Represents registered App settings in a Json format.
 */
class AppJson extends MagicLink {

    public $page_title = 'App JSON';
    public $page_description = 'App JSON Settings';
    public $root = 'apps';
    public $type = 'json';
    public $post_type = 'user';
    public $show_bulk_send = true;
    public $show_app_tile = true;

    public $json_whitelist = [
        'json'
    ];

    public function __construct() {
        parent::__construct( false );
    }

    /**
     * Do any action before the magic link is bootstrapped
     * @return void
     */
    public function init() {
        $this->whitelist_current_route();
    }

    /**
     * Called if the route is a magic link route.
     *
     * @return void
     */
    public function boot() {
        $this->render();
    }

    /**
     * Add routes to the RouteCollection
     *
     * @param RouteCollectionInterface $r The RouteCollection object
     * @return void
     */
    public function routes( RouteCollectionInterface $r ) {
        require_once routes_path( 'app-json.php' );
    }
}
