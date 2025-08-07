<?php

namespace Tests;

use DT\Home\Controllers\Admin\AppSettingsController;
use DT\Home\Services\Aggregator;
use DT\Home\Services\Apps;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

class AggregatorTest extends TestCase {

    /**
     * @test
     */
    public function it_aggregates_filter_apps() {
        $filter_apps = [
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ])
        ];

        $settings_apps = [
            app_factory([
                'creation_type' => 'custom'
            ])
        ];

        foreach ( $filter_apps as $app ) {
            $settings_apps[] = $app;
        }

        add_filter( 'dt_home_apps', function ( $apps ) use ( $filter_apps ) {
            return $filter_apps;
        } );

        $settings_app_source = container()->get( SettingsApps::class );
        $settings_app_source->save( $settings_apps );

        $aggregator = new Aggregator( [ 'filter' ] );
        $aggregated = $aggregator->all();

        foreach ( $filter_apps as $app ) {
            $this->assertContains( $app['slug'], array_column( $aggregated, 'slug' ) );
        }

        foreach ( $aggregated as $app ) {
            $this->assertEquals( 'filter', $app['source'] );
        }
    }

    /**
     * @test
     */
    public function it_aggregates_settings_apps() {
        $filter_apps = [
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ])
        ];

        $settings_apps = [
            app_factory([
                'name' => 'settings app',
                'creation_type' => 'custom'
            ])
        ];

        //Register some filter apps
        add_filter( 'dt_home_apps', function ( $apps ) use ( $filter_apps ) {
            return $filter_apps;
        } );

        //Edit them with settings apps
        foreach ( $filter_apps as $app ) {
            $settings_apps[] = array_merge( [], $app, [ 'name' => 'settings app' ] );
        }

        //Only 1 coded app that has yet to be overwritten by settings apps
        add_filter( 'dt_home_apps', function ( $apps ) use ( $filter_apps ) {
            $apps[] = app_factory([
                'name' => 'coded_app',
            ]);
            return $apps;
        } );

        $settings_app_source = container()->get( SettingsApps::class );
        $settings_app_source->save( $settings_apps );

        $aggregator = new Aggregator( [ 'settings' ] );
        $aggregated = $aggregator->all();

        $this->assertCount(1, array_filter($aggregated, function ( $app ) {
            return $app['source'] === 'filter';
        }));

        foreach ( $aggregated as $app ) {
            if ( $app['source'] === 'settings' ) {
                $this->assertEquals( 'settings app', $app['name'] );
                $this->assertEquals( 'settings', $app['source'] );
            }
        }
    }

    /**
     * @test
     */
    public function it_aggregates_user_apps() {
        $filter_apps = [
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ]),
            app_factory([
                'creation_type' => 'code'
            ])
        ];

        $settings_apps = [
            app_factory([
                'creation_type' => 'custom'
            ]),
            app_factory([
                'creation_type' => 'custom'
            ]),
            app_factory([
                'creation_type' => 'custom'
            ])
        ];

        $user_filter_apps = [
            app_factory([
                'name' => 'code',
            ]),
            app_factory([
                'name' => 'code',
            ])
        ];

        $this->as_user();

        //Register some filter apps
        add_filter( 'dt_home_apps', function ( $apps ) use ( $filter_apps ) {
            return $filter_apps;
        } );

        //Edit them with settings apps
        foreach ( $filter_apps as $app ) {
            $settings_apps[] = array_merge( [], $app, [ 'name' => 'settings app' ] );
        }

        //Only 2 coded app that has yet to be overwritten by settings apps
        add_filter( 'dt_home_apps', function ( $apps ) use ( $user_filter_apps ) {
            foreach ( $user_filter_apps as $app ) {
                $apps[] = $app;
            }

            //One more coded that we won't touch
            $apps[] = app_factory([
                'name' => 'coded_app',
            ]);
            return $apps;
        } );

        $settings_app_source = container()->get( SettingsApps::class );
        $settings_app_source->save( $settings_apps );

        //Override the first setting app, and two user apps, one filter app and
        $user_apps_source = container()->get( UserApps::class );
        $user_apps_source->update( $settings_apps[0]['slug'], $settings_apps[0] );
        foreach ( $user_filter_apps as $app ) {
            $user_apps_source->update( $app['slug'], $app );
        }

        $aggregator = new Aggregator( [ 'user' ] );
        $aggregated = $aggregator->all();

        $this->assertCount(1, array_filter($aggregated, function ( $app ) {
            return $app['source'] === 'filter';
        }));

        $this->assertCount(5, array_filter($aggregated, function ( $app ) {
            return $app['source'] === 'settings';
        }));

        $this->assertCount(3, array_filter($aggregated, function ( $app ) {
            return $app['source'] === 'user';
        }));
    }
}
