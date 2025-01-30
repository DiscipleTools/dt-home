<?php

/**
 * @var RouteCollectionInterface $r
 * @see https://route.thephpleague.com/
 */
use DT\Home\CodeZone\WPSupport\Middleware\HasCap;
use DT\Home\Controllers\Admin\AppSettingsController;
use DT\Home\Controllers\Admin\GeneralSettingsController;
use DT\Home\Controllers\Admin\TrainingSettingsController;
use DT\Home\CodeZone\WPSupport\Middleware\Nonce;
use DT\Home\League\Route\RouteCollectionInterface;

$r->group( '/wp-admin', function ( RouteCollectionInterface $r ) {
	$r->get( '/admin.php?page=dt_home', [ GeneralSettingsController::class, 'show' ] );
	$r->get( '/admin.php?page=dt_home&tab=general', [ GeneralSettingsController::class, 'show' ] );

	$r->get( '/admin.php?page=dt_home&tab=app', [ AppSettingsController::class, 'show' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=available_app', [ AppSettingsController::class, 'show_available_apps' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=create', [ AppSettingsController::class, 'create' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=edit/{slug}', [ AppSettingsController::class, 'edit' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=unhide/{slug}', [ AppSettingsController::class, 'unhide' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=hide/{slug}', [ AppSettingsController::class, 'hide' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=up/{slug}', [ AppSettingsController::class, 'up' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=down/{slug}', [ AppSettingsController::class, 'down' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=delete/{slug}', [ AppSettingsController::class, 'delete' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=softdelete/{slug}', [ AppSettingsController::class, 'soft_delete_app' ] );
	$r->get( '/admin.php?page=dt_home&tab=app&action=restore_app/{slug}', [ AppSettingsController::class, 'restore_app' ] );

	$r->get( '/admin.php?page=dt_home&tab=training', [ TrainingSettingsController::class, 'show' ] );
	$r->get('/admin.php?page=dt_home&tab=training&action=create', [
		TrainingSettingsController::class,
		'create'
	]);
	$r->get('/admin.php?page=dt_home&tab=training&action=edit/{id}', [
		TrainingSettingsController::class,
		'edit'
	]);
	$r->get( '/admin.php?page=dt_home&tab=training&action=up/{id}', [ TrainingSettingsController::class, 'up' ] );
	$r->get( '/admin.php?page=dt_home&tab=training&action=down/{id}', [ TrainingSettingsController::class, 'down' ] );
	$r->get('/admin.php?page=dt_home&tab=training&action=delete/{id}', [
		TrainingSettingsController::class,
		'delete'
	]);
})->middleware( new HasCap( 'manage_dt' ) );

$r->group( '/wp-admin', function ( RouteCollectionInterface $r ) {
	$r->post( '/admin.php?page=dt_home', [ GeneralSettingsController::class, 'update' ] );
	$r->post( '/admin.php?page=dt_home&tab=general', [ GeneralSettingsController::class, 'update' ] );
	$r->post( '/admin.php?page=dt_home&tab=app&action=create', [ AppSettingsController::class, 'store' ] );
	$r->post( '/admin.php?page=dt_home&tab=app&action=edit/{slug}', [ AppSettingsController::class, 'update' ] );
	$r->post( '/admin.php?page=dt_home&tab=training&action=create', [ TrainingSettingsController::class, 'store' ] );
	$r->post( '/admin.php?page=dt_home&tab=training&action=edit/{id}', [ TrainingSettingsController::class, 'update' ] );
} )->middleware( new Nonce( 'dt_admin_form_nonce' ) )
    ->middleware( new HasCap( 'manage_dt' ) );
