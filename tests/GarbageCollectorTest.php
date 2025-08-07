<?php

namespace Tests;

use DT\Home\Controllers\Admin\AppSettingsController;
use DT\Home\Services\Apps;
use DT\Home\Sources\SettingsApps;
use function DT\Home\container;

class GarbageCollectorTest extends TestCase {

    /**
     * @test
     */
    public function it_does_not_filter_coded_apps() {
        $app = app_factory([
            'creation_type' => 'code',
            'slug' => 'coded-app',
            'is_deleted' => false
        ]);
        $settings_apps = container()->get( SettingsApps::class );
        $settings_apps->save( [
            app_factory(),
            app_factory()
        ] );
        add_filter( 'dt_home_apps', function ( $apps ) use ( $app ) {
            $apps[] = $app;
            return $apps;
        } );
        $this->assertContains( $app['slug'], array_column( $settings_apps->merged(), 'slug' ) );
    }

    /**
     * @test
     */
    public function it_filters_out_stale_coded_apps()
    {
        $app = app_factory([
            'creation_type' => 'code',
            'slug' => 'coded-app',
            'is_deleted' => false
        ]);
        $settings_apps = container()->get( SettingsApps::class );
        $settings_apps->save( [
            app_factory(),
            $app,
            app_factory()
        ] );
        $this->assertNotContains( $app['slug'], array_column( $settings_apps->merged(), 'slug' ) );
    }
}
