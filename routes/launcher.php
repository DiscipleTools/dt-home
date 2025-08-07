<?php

/**
 * @var RouteCollectionInterface $r
 * @var Launcher $this
 * @see https://route.thephpleague.com/
 */

use DT\Home\CodeZone\WPSupport\Middleware\Nonce;
use DT\Home\Controllers\LoginController;
use DT\Home\Controllers\MagicLink\AppController;
use DT\Home\Controllers\MagicLink\LauncherController;
use DT\Home\Controllers\MagicLink\ManifestController;
use DT\Home\Controllers\MagicLink\ShareController;
use DT\Home\Controllers\MagicLink\TrainingController;
use DT\Home\League\Route\RouteCollectionInterface;
use DT\Home\MagicLinks\Launcher;
use DT\Home\Middleware\CheckShareCookie;
use DT\Home\Middleware\CanAccess;
use DT\Home\Middleware\LoggedIn;
use function DT\Home\config;

$r->group('/apps/launcher/{key}', function ( RouteCollectionInterface $r ) {
    $r->get( '/app/{slug}', [ AppController::class, 'show' ] );
    $r->get( '/', [ LauncherController::class, 'show' ] );
    $r->get( '/training', [ TrainingController::class, 'show' ] );
    $r->get( '/logout', [ LoginController::class, 'logout' ] );
    $r->get( '/reset-apps', [ AppController::class, 'reset_apps' ] );
    $r->get( '/manifest.json', [ ManifestController::class, 'show' ] );
    $r->get( '/apps', [ AppController::class, 'all' ] );
})->middleware( new LoggedIn() )
    ->middleware( new CheckShareCookie() );

$r->group('/apps/launcher/{key}', function ( RouteCollectionInterface $r ) {
    $r->post( '/hide', [ AppController::class, 'hide' ] );
    $r->post( '/unhide', [ AppController::class, 'unhide' ] );
    $r->post( '/reorder', [ AppController::class, 'reorder' ] );
    $r->post( '/reset-apps', [ AppController::class, 'reset_apps' ] );
    $r->post( '/store-app', [ AppController::class, 'store_apps' ] );
    $r->post( '/update-app/{slug}', [ AppController::class, 'update_apps' ] );
})->middleware( new LoggedIn() )
    ->middleware( new CheckShareCookie() )
    ->middleware( new Nonce( config( 'plugin.nonce_name' ) ) );

$r->group('/apps/launcher/{key}', function ( RouteCollectionInterface $r ) {
    $r->get( '/share', [ ShareController::class, 'show' ] );
});
