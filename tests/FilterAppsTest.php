<?php

namespace Tests;

use DT\Home\Sources\FilterApps;
use Exception;
use function DT\Home\container;

class FilterAppsTest extends TestCase {

    /**
     * @test
     */
    public function can_return_raw_array(): void {
        $filter_apps = [
            app_factory( [
                'creation_type' => 'custom'
            ] ),
            app_factory( [
                'creation_type' => 'custom'
            ] ),
            app_factory( [
                'creation_type' => 'custom'
            ] ),
            app_factory( [
                'creation_type' => 'custom'
            ] )
        ];

        // Register custom filter apps.
        add_filter( 'dt_home_apps', function ( $apps ) use ( $filter_apps ) {
            return $filter_apps;
        } );

        // Fetch raw filtered apps.
        $raw_apps = container()->get( FilterApps::class )->raw();

        // Assert expected filtered apps are returned.
        foreach ( $raw_apps as $raw_app ) {
            $filter_app = array_filter( $filter_apps, function ( $app ) use ( $raw_app ) {
                return $app['slug'] === $raw_app['slug'];
            } );

            $this->assertNotNull( $filter_app );
        }
    }

    /**
     * @test
     */
    public function can_allow_app(): void {
        $this->assertTrue(
            container()->get( FilterApps::class )->is_allowed(
                app_factory( [
                    'is_deleted' => false
                ] )
            )
        );
    }

    /**
     * @test
     */
    public function can_disallow_app(): void {
        $this->assertFalse(
            container()->get( FilterApps::class )->is_allowed(
                app_factory( [
                    'is_deleted' => true
                ] )
            )
        );
    }

    /**
     * @test
     */
    public function can_merge_apps(): void {
        $existing_app = app_factory();
        $new_app = app_factory();

        // Execute merge operation.
        $merged_app = container()->get( FilterApps::class )->merge( $existing_app, $new_app );

        // Ensure returned merged app, maps accordingly with new app.
        foreach ( $new_app as $key => $value ) {
            $this->assertTrue( $merged_app[ $key ] === $value );
        }
    }

    /**
     * @test
     */
    public function can_not_call_save_function(): void {
        $this->expectException( Exception::class );

        container()->get( FilterApps::class )->save( app_factory() );
    }
}
