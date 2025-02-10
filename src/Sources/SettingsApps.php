<?php

namespace DT\Home\Sources;

use DT\Home\Services\Analytics;
use function DT\Home\container;
use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;

/**
 * Retrieves the raw array of home apps.
 *
 * This function applies the 'dt_home_apps' filter to an empty array,
 * returning the result.
 *
 * @return array The raw array of home apps.
 */
class SettingsApps extends AppSource {
    /**
     * Retrieves the raw array of home apps.
     *
     * This function applies the 'dt_home_apps' filter to an empty array,
     * returning the result.
     *
     * @return array The raw array of home apps.
     */
    public function raw( array $params = [] ): array {
        return get_plugin_option( 'apps' );
    }

    /**
     * Checks if the application is allowed.
     *
     * @param array $app The application data.
     *
     * @return bool True if the application is allowed, false otherwise.
     */
    public function is_allowed( array $app ): bool {
        return $app['is_deleted'] !== true;
    }

    /**
     * Save apps.
     *
     * @param array $apps The apps to be saved.
     * @return bool Whether the saving was successful or not.
     */
    public function save( $apps, array $options = [] ): bool {
        return set_plugin_option( 'apps', $apps );
    }

    /**
     * Formats the application data.
     *
     * @param array $app The application data to format.
     *
     * @return array The formatted application data.
     */
    protected function format_app( array $app ): array {
        $sort = $app['sort'] ?? '';

        $overrides = [];

        if ( ! is_numeric( $sort ) ) {
            $overrides['sort'] = 10;
        }

        return array_merge([
            'name' => '',
            'type' => 'Web View',
            'creation_type' => 'custom',
            'source' => static::handle(),
            'icon' => '',
            'url' => '',
            'sort' => 10,
            'slug' => '',
            'is_hidden' => false,
            'is_deleted' => false,
        ], $app, $overrides);
    }

    /**
     * If enabled, capture a snapshot of core analytical metric counts;
     * with the ability to filter metrics to be exported.
     *
     * @param string $scope Metric scope (library.name) to be used.
     * @param array|null $apps Apps array to process; otherwise default to raw apps.
     * @param array $metrics Metrics to be captured.
     */
    public function capture_analytics_metric_counts( string $scope = __CLASS__, array $apps = null, array $metrics = [
        'total-apps-count',
        'total-active-apps-count',
        'total-active-custom-apps-count',
        'total-active-coded-apps-count',
        'total-deleted-coded-apps-count',
        'total-apps-web-views-count',
        'total-apps-links-count',
        'total-apps-native-links-count'
    ] ): void {

        $analytics = container()->get( Analytics::class );

        if ( !$analytics->is_enabled() ) {
            return;
        }

        $apps = $apps ?? $this->raw();

        // Process identified metrics.
        foreach ( $metrics as $metric ) {
            $properties = null;
            switch ( $metric ) {
                case 'total-apps-count':
                    $total_apps_count = count( $apps );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_apps_count,
                        'unit' => 'apps',
                        'description' => 'Total Apps Count'
                    ];
                    break;
                case 'total-active-apps-count':
                    $total_active_apps_count = count( array_filter( $apps, function ( $app ) {
                        return ( !isset( $app['is_deleted'] ) || $app['is_deleted'] === false );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_active_apps_count,
                        'unit' => 'active-apps',
                        'description' => 'Total Active Apps Count'
                    ];
                    break;
                case 'total-active-custom-apps-count':
                    $total_active_custom_apps_count = count( array_filter( $apps, function ( $app ) {
                        return ( $app['creation_type'] === 'custom' ) && ( !isset( $app['is_deleted'] ) || $app['is_deleted'] === false );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_active_custom_apps_count,
                        'unit' => 'active-custom-apps',
                        'description' => 'Total Active Custom Apps Count'
                    ];
                    break;
                case 'total-active-coded-apps-count':
                    $total_active_coded_apps_count = count( array_filter( $apps, function ( $app ) {
                        return ( $app['creation_type'] === 'code' ) && ( !isset( $app['is_deleted'] ) || $app['is_deleted'] === false );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_active_coded_apps_count,
                        'unit' => 'active-coded-apps',
                        'description' => 'Total Active Coded Apps Count'
                    ];
                    break;
                case 'total-deleted-coded-apps-count':
                    $total_deleted_coded_apps_count = count( array_filter( $apps, function ( $app ) {
                        return ( $app['creation_type'] === 'code' ) && ( isset( $app['is_deleted'] ) && $app['is_deleted'] === true );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_deleted_coded_apps_count,
                        'unit' => 'deleted-coded-apps',
                        'description' => 'Total Deleted Coded Apps Count'
                    ];
                    break;
                case 'total-apps-web-views-count':
                    $total_apps_web_views_count = count( array_filter( $apps, function ( $app ) {
                        return ( isset( $app['type'] ) && strtolower( trim( $app['type'] ) ) == 'web view' );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_apps_web_views_count,
                        'unit' => 'web-view-apps',
                        'description' => 'Total Web View Apps Count'
                    ];
                    break;
                case 'total-apps-links-count':
                    $total_apps_links_count = count( array_filter( $apps, function ( $app ) {
                        return ( isset( $app['type'] ) && strtolower( trim( $app['type'] ) ) == 'link' );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_apps_links_count,
                        'unit' => 'link-apps',
                        'description' => 'Total Link Apps Count'
                    ];
                    break;
                case 'total-apps-native-links-count':
                    $total_apps_native_links_count = count( array_filter( $apps, function ( $app ) {
                        return ( isset( $app['type'] ) && strtolower( trim( $app['type'] ) ) == 'native app link' );
                    } ) );

                    $properties = [
                        'lib_name' => $scope,
                        'value' => $total_apps_native_links_count,
                        'unit' => 'native-link-apps',
                        'description' => 'Total Native Link Apps Count'
                    ];
                    break;

            }

            // Generate metric exports on properties detection.
            if ( !empty( $properties ) ) {
                $analytics->metric( $metric, $properties );
            }
        }
    }
}
