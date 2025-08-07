<?php

namespace Tests;

use DT\Home\Services\Apps;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

class AppsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_apps_from_source()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        // Save apps to settings
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // Get apps from settings using from method
        $service = container()->get( Apps::class );
        $setting_apps = $service->from( 'settings' );

        // Check if the apps are returned
        $this->assertIsArray( $setting_apps );
        $this->assertGreaterThanOrEqual( 2, count( $setting_apps ) );

        // Check if the apps are the same as the saved apps in settings
        $app_slugs = array_column( $apps, 'slug' );
        $setting_app_slugs = array_column( $setting_apps, 'slug' );

        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $setting_app_slugs );
        }
    }

    /**
     * @test
     */
    public function it_can_get_apps_source()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        // Save apps to settings
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // Get apps from settings using source method
        $service = container()->get( Apps::class );
        $setting_apps = $service->source( 'settings' );

        // Check if the apps are returned and are the same as the saved apps in settings
        $this->assertIsArray( $setting_apps );
        $this->assertGreaterThanOrEqual( 2, count( $setting_apps ) );

        // Check if the apps are the same as the saved apps in settings
        $app_slugs = array_column( $apps, 'slug' );
        $setting_app_slugs = array_column( $setting_apps, 'slug' );

        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $setting_app_slugs );
        }
    }

    /**
     * @test
     */
    public function it_can_find_for_user()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        // Save apps to user apps
        $user_apps_service = container()->get( UserApps::class );
        $user_apps_service->save( $apps );

        // Get user apps using for method
        $user_apps = $user_apps_service->for( $user_id );
        $service = container()->get( Apps::class );

        // Find app for user
        $app = $service->find_for( 'app1', $user_id );
        // Check if the app is returned
        $this->assertIsArray( $app );
        $this->assertEquals( 'app1', $app['slug'] );

        // Check if the apps are the same as the saved apps in user apps
        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $user_apps, 'slug' );

        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }
    }

    /**
     * @test
     */
    public function it_can_for_user()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        // Save apps to user apps using for method
        $user_apps_service = container()->get( UserApps::class );
        $user_apps_service->save( $apps );

        // Get user apps using for method
        $service = container()->get( Apps::class );
        $user_apps = $service->for( $user_id );

        // Check if the apps are returned array
        $this->assertIsArray( $user_apps );
        $this->assertGreaterThanOrEqual( 2, count( $user_apps ) );

        // Check if the apps are the same as the saved apps in user apps
        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $user_apps, 'slug' );

        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }
    }

    /**
     * @test
     */
    public function it_can_find_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        // Save apps to user apps
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // Get apps from settings using find method
        $service = container()->get( Apps::class );
        $app = $service->find( 'app1' );

        // Check if the app is returned
        $this->assertIsArray( $app );
        $this->assertEquals( 'app1', $app['slug'] );

        $app_slugs = array_column( $apps, 'slug' );

        $this->assertContains( $app['slug'], $app_slugs );
    }

    /**
     * @test
     */
    public function it_has_app()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];
        // Save apps to settings
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // validate if app is in the apps list
        $service = container()->get( Apps::class );
        $this->assertTrue( $service->has( 'app1' ) );
    }

    /**
     * @test
     */
    public function it_moves_app_up()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3 ] ),
        ];
        // Save apps to settings
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // Move app up
        $service = container()->get( Apps::class );
        $result = $service->move( 'app3', 'up' );

        // Check if the app is moved up
        $this->assertTrue( $result );

        $find_app = $service->find( 'app3' );

        // Check if the app is moved up
        $this->assertEquals( 2, $find_app['sort'] );
    }

    /**
     * @test
     */
    public function it_moves_app_down()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3 ] ),
        ];

        // Save apps to settings
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );

        // Move app down
        $service = container()->get( Apps::class );
        $result = $service->move( 'app2', 'down' );
        // Check if the app is moved down
        $this->assertTrue( $result );

        // Check if the app is moved down
        $find_app = $service->find( 'app2' );
        $this->assertEquals( 3, $find_app['sort'] );
    }

    /**
     * @test
     */
    public function it_returns_false_if_app_not_found()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $service = container()->get( Apps::class );
        // Move the nonexistent app up
        $result = $service->move( 'nonexistent_app', 'up' );

        $this->assertFalse( $result );
    }

    /**
     * @test
     */
    public function it_imports_apps() {

        // Set unit test current user.
        $this->acting_as( 1 );

        // Generate importing apps.
        $importing_apps = [ app_factory() ];
        $importing_app_slug = $importing_apps[0]['slug'];

        // Assert importing app slug is not already stored.
        $apps_service = container()->get( Apps::class );
        $this->assertFalse( $apps_service->has( $importing_app_slug ) );

        // Proceed with apps import and assert a positive outcome.
        $this->assertTrue( $apps_service->import( $importing_apps ) );

        // Finally, assert app has been imported.
        $this->assertTrue( $apps_service->has( $importing_app_slug ) );
    }

    /**
     * @test
     */
    public function it_updates_importing_apps() {

        // Set unit test current user.
        $this->acting_as( 1 );

        // Generate importing apps.
        $apps = [ app_factory() ];
        $app_slug = $apps[0]['slug'];

        // Save apps to settings and assert successful storage.
        $apps_service = container()->get( Apps::class );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $this->assertTrue( $apps_service->has( $app_slug ) );

        // Import new apps with a slug match; expecting an update outcome.
        $updated_name = 'updated_app';
        $this->assertTrue( $apps_service->import( [ app_factory( [ 'slug' => $app_slug, 'name' => $updated_name ] ) ] ) );

        // Fetch recently saved app and confirm name property updated.
        $updated_app = $apps_service->find( $app_slug );
        $this->assertFalse( empty( $updated_app ) );
        $this->assertTrue( $updated_app['name'] === $updated_name );
    }
}
