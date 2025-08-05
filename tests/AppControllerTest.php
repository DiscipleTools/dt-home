<?php

namespace Tests;

use DT\Home\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Home\Controllers\MagicLink\AppController;
use DT\Home\Services\Apps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

class AppControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handles_missing_apps()
    {
        $request = ServerRequestFactory::from_globals();
        $app = app_factory();
        $controller = container()->get( AppController::class );
        $response = $controller->show( $request, [ 'key' => $app['key'], 'slug' => $app['slug'] ] );
        $this->assertEquals( 404, $response->getStatusCode() );
    }

    /**
     * @test
     *
     */
    public function it_renders()
    {
        $user_id = 1;
        $this->acting_as( $user_id );
        $request = ServerRequestFactory::from_globals();
        $app = app_factory([
            'creation_type' => 'custom',
        ]);
        $apps = container()->get( Apps::class );
        $data = $apps->from( 'settings' );

        $data[] = $app;

        update_user_option( $user_id, 'dt_home_apps', $data );
        $controller = container()->get( AppController::class );
        $response = $controller->show( $request, [ 'key' => $app['key'], 'slug' => $app['slug'] ] );
        $this->assertEquals( 200, $response->getStatusCode() );
    }

    /**
     * @test
     */
    public function it_can_hide_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps_service = container()->get( Apps::class );
        $apps = $apps_service->for( $user_id );
        $data = $apps[0];

        container()->get( UserApps::class )->save_for( $user_id, $apps );

        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/update-hide-apps', $data );
        $key = $this->faker->md5;
        $controller = container()->get( AppController::class );
        $response = $controller->hide( $request, [ 'key' => $key ] );
        $this->assertEquals( 200, $response->getStatusCode() );
    }

    /**
     * @test
     */
    public function it_can_unhide_apps(): void
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps_service = container()->get( Apps::class );
        $apps = $apps_service->for( $user_id );
        $data = $apps[0];

        container()->get( UserApps::class )->save_for( $user_id, $apps );

        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/un-hide-app', $data );
        $key = $this->faker->md5;
        $controller = container()->get( AppController::class );
        $response = $controller->unhide( $request, [ 'key' => $key ] );


        $this->assertEquals( 200, $response->getStatusCode() );
    }

    /**
     * @test
     */
    public function it_can_update_app_order()
    {
        $data = [
            app_factory(),
            app_factory(),
            app_factory(),
        ];
        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/update-app-order', $data );
        $key = $this->faker->md5;
        $controller = container()->get( AppController::class );
        $response = $controller->reorder( $request, [ 'key' => $key ] );
        $this->assertEquals( 200, $response->getStatusCode() );
    }

    /**
     * @test
     */
    public function it_can_create_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $app = app_factory(
            [
                'slug' => 'test-app',
                'creation_type' => 'custom',
                'is_hidden' => true,
                'is_deleted' => false
            ]
        );

        $controller = container()->get( AppController::class );

        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/create-app', $app );
        $response = $controller->store_apps( $request );

        $this->assertEquals( 200, $response->getStatusCode() );
        $apps = container()->get( Apps::class )->from( 'user' );
        $this->assertContains( $app['slug'], array_column( $apps, 'slug' ) );
    }


    /**
     * @test
     */
    public function it_can_update_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $app = app_factory(
            [
                'slug' => 'test-app',
                'creation_type' => 'custom',
                'is_hidden' => true,
                'is_deleted' => false
            ]
        );

        $controller = container()->get( AppController::class );

        //Create an app
        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/create-app', $app );
        $controller->store_apps( $request );
        $apps = container()->get( Apps::class )->from( 'user' );
        $this->assertContains( $app['slug'], array_column( $apps, 'slug' ) );

        //Update the app
        $app['name'] = 'Updated App';
        $app['icon'] = 'mdi mdi-test';
        $request = ServerRequestFactory::request( 'POST', 'apps/launcher/key/update-apps', $app );
        $response = $controller->update_apps( $request, [ 'slug' => $app['slug'] ] );

        $this->assertEquals( 200, $response->getStatusCode() );
        $apps = container()->get( Apps::class )->from( 'user' );

        $find_app = container()->get( Apps::class )->find_for( 'test-app', $user_id );
        $this->assertEquals( 'Updated App', $find_app['name'] );
        $this->assertEquals( 'mdi mdi-test', $find_app['icon'] );

        $this->assertContains( $app['name'], array_column( $apps, 'name' ) );
        $this->assertContains( $app['icon'], array_column( $apps, 'icon' ) );
    }
}
