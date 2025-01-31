<?php
/**
 * @var RouteCollectionInterface $r
 * @see https://route.thephpleague.com/
 */

use DT\Home\Controllers\LoginController;
use DT\Home\Controllers\RedirectController;
use DT\Home\Controllers\RegisterController;
use DT\Home\Controllers\MagicLink\AnalyticsReportingController;
use DT\Home\League\Route\RouteCollectionInterface;
use DT\Home\Middleware\LoggedOut;
use DT\Home\CodeZone\WPSupport\Middleware\Nonce;
use function DT\Home\config;

$r->get( '/', [ RedirectController::class, 'show' ] );
$r->get( '/login', [ LoginController::class, 'show' ] )->middleware( new LoggedOut() );
$r->get( '/register', [ RegisterController::class, 'show' ] )->middleware( new LoggedOut() );

$r->get( '/analytics-reports', [ AnalyticsReportingController::class, 'get_analytics_reports' ] );

$r->group('', function ( RouteCollectionInterface $r ) {
    $r->post( '/login', [ LoginController::class, 'process' ] )->middleware( new LoggedOut() );
    $r->post( '/register', [ RegisterController::class, 'process' ] )->middleware( new LoggedOut() );
})->middleware( new Nonce( config( 'plugin.nonce_name' ) ) );
