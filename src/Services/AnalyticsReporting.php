<?php

namespace DT\Home\Services;

use DT\Home\Sources\Trainings;
use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;
use DT\Home\Sources\SettingsApps;
use function DT\Home\config;

class AnalyticsReporting
{
    private SettingsApps $settings_apps;
    private Trainings $trainings_source;
    private array $apps = [];

    public function __construct( SettingsApps $source, Trainings $trainings_source )
    {
        $this->settings_apps = $source;
        $this->trainings_source = $trainings_source;
        $this->apps = $this->settings_apps->undeleted();
    }

    /**
     * Get the analytics reports
     *
     * @param array $params The parameters
     * @return array The analytics reports
     */
    public function get_reports( array $params = [] ): array
    {
        $reports = [];

        $dynamic_metrics = [
            'total-active-apps-count' => 'get_active_apps_count',
            'total-active-custom-apps-count' => 'get_active_custom_apps_count',
            'total-active-coded-apps-count' => 'get_active_coded_apps_count',
            'total-deleted-coded-apps-count' => 'get_deleted_coded_apps_count',
            'total-active-training-videos-count' => 'get_active_training_videos_count'
        ];

        foreach ( $dynamic_metrics as $param => $method ) {
            if ( in_array( $param, $params ) ) {
                $reports[$param] = $this->$method();
            }
        }

        $stored_metrics = get_plugin_option( config( 'analytics.dt_home_analytics_reports' ), [] );
        $db_metrics = [ 'admin-app-creation', 'user-app-creation', 'login', 'login-error', 'logout' ];

        foreach ( $db_metrics as $metric ) {
            if ( in_array( $metric, $params ) ) {
                $reports[$metric] = $stored_metrics[$metric] ?? 0;
            }
        }

        return $reports;
    }

    /**
     * Get the count of active apps
     *
     * @return int The count of active apps
     */
    private function get_active_apps_count(): int
    {
        return $this->count_apps_by_filter( fn( $app ) => !$app['is_hidden'] );
    }

    /**
     * Get the count of active custom apps
     *
     * @return int The count of active custom apps
     */
    private function get_active_custom_apps_count(): int
    {
        return $this->count_apps_by_filter( fn( $app ) => !$app['is_hidden'] && $app['creation_type'] === 'custom' );
    }

    /**
     * Get the count of active coded apps
     *
     * @return int The count of active coded apps
     */
    private function get_active_coded_apps_count(): int
    {
        return $this->count_apps_by_filter( fn( $app ) => !$app['is_hidden'] && $app['creation_type'] === 'code' );
    }

    /**
     * Get the count of deleted coded apps
     *
     * @return int The count of deleted coded apps
     */

    private function get_deleted_coded_apps_count(): int
    {
        $deleted_coded_apps = $this->settings_apps->deleted();

        return count( array_filter( $deleted_coded_apps, fn( $app ) => $app['creation_type'] === 'code' ) );
    }

    /**
     * Get the count of active training videos
     *
     * @return int The count of active training videos
     */
    private function get_active_training_videos_count(): int
    {
        return count( $this->trainings_source->all() );
    }

    /**
     * Count the number of apps that match a filter
     *
     * @param callable $filter The filter to apply
     * @return int The number of apps that match the filter
     */
    private function count_apps_by_filter( callable $filter ): int
    {
        return count( array_filter( $this->apps, $filter ) );
    }

    /**
     * Log an admin event
     *
     * @param string $eventName The event name
     */
    public function log_admin_event( string $eventName ): void
    {
        $stored_metrics = get_plugin_option( config( 'analytics.dt_home_analytics_reports' ), [] );
        $stored_metrics[$eventName] = ( $stored_metrics[$eventName] ?? 0 ) + 1;
        set_plugin_option( config( 'analytics.dt_home_analytics_reports' ), $stored_metrics );
    }
}
