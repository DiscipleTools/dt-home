<?php

namespace Tests;

use DT\Home\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Home\Controllers\Admin\GeneralSettingsController;
use function DT\Home\config;
use function DT\Home\container;
use function DT\Home\get_plugin_option;
use function DT\Home\plugin_url;
use function DT\Home\set_plugin_option;

class GeneralSettingsControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_renders()
    {
        $request = ServerRequestFactory::from_globals();
        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->show( $request );
        $this->assertEquals( 200, $response->getStatusCode() );
    }

    /**
     * @test
     */
    public function it_updates_require_login()
    {
        set_plugin_option( 'require_login', 'off' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_require_login' => 'on',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertTrue( get_plugin_option( 'require_login', false ) );
    }

    /**
     * @test
     */
    public function it_updates_require_login_off()
    {
        set_plugin_option( 'require_login', 'on' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_require_login' => 'off',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertFalse( get_plugin_option( 'require_login', true ) );
    }

    /**
     * @test
     */
    public function it_updates_show_in_menu()
    {
        set_plugin_option( 'show_in_menu', 'off' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_show_in_menu' => 'on',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertTrue( get_plugin_option( 'show_in_menu', false ) );
    }

    /**
     * @test
     */
    public function it_updates_show_in_menu_off()
    {
        set_plugin_option( 'show_in_menu', 'on' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_show_in_menu' => 'off',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertFalse( get_plugin_option( 'show_in_menu', true ) );
    }

    /**
     * @test
     */
    public function it_updates_button_color()
    {
        $default_color = config( 'options.defaults.button_color' );
        set_plugin_option( 'button_color', 'red' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_button_color' => $default_color,
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertEquals( $default_color, get_plugin_option( 'button_color', $default_color ) );
    }

    /**
     * @test
     */
    public function it_updates_reset_apps()
    {
        set_plugin_option( 'reset_apps', 'off' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_reset_apps' => 'on',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertTrue( get_plugin_option( 'reset_apps', false ) );
    }

    /**
     * @test
     */
    public function it_updates_reset_apps_off()
    {
        set_plugin_option( 'reset_apps', 'on' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_reset_apps' => 'off',
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertFalse( get_plugin_option( 'reset_apps', true ) );
    }

    public function it_updates_ministry_logo()
    {
        $default_logo = plugin_url( 'resources/img/logo-color.png' );
        set_plugin_option( 'custom_ministry_logo', '/dt-launcher.png' );
        $request = ServerRequestFactory::request('POST', '/admin.php?page=dt_home&tab=general', [
            'dt_home_custom_ministry_logo' => $default_logo,
        ]);

        $controller = container()->get( GeneralSettingsController::class );
        $response = $controller->update( $request );
        $this->assertEquals( 302, $response->getStatusCode() );
        $this->assertEquals( $default_logo, get_plugin_option( 'custom_ministry_logo', $default_logo ) );
    }
}
