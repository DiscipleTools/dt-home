<?php

namespace DT\Home\Services;

class AnalyticsReporting
{
    /**
     * Get the analytics reports
     *
     * @param array $params The parameters
     * @return array The analytics reports
     */
    public function get_reports( array $params = [], $start_date = null, $end_date = null ): array
    {
        $reports = [];

        $db_metrics = [
            'admin-app-creation',
            'user-app-creation',
            'login',
            'login-error',
            'logout',
            'total-active-apps-count',
            'total-active-custom-apps-count',
            'total-active-coded-apps-count',
            'total-deleted-coded-apps-count',
            'total-active-training-videos-count'
        ];

        foreach ( $db_metrics as $metric ) {
            if ( in_array( $metric, $params ) ) {
                $reports[$metric] = $this->get_activity_logs( $metric, $start_date, $end_date );
            }
        }

        return $reports;
    }

    /**
     * Initialize the database
     *
     * @return array The database connection, table name, and user ID
     */
    private function initialize_db(): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dt_activity_log';
        $userId = get_current_user_id();

        return [ $wpdb, $table_name, $userId ];
    }

    /**
     * Log an admin event
     *
     * @param string $eventName The event name
     */
    public function log_admin_event( string $eventName, $eventValue = null ): void
    {
        [$wpdb, $table_name, $userId] = $this->initialize_db();

        $wpdb->insert(
            $table_name,
            [
                'user_id' => $userId,
                'action' => $eventName,
                'hist_time' => time(),
                'object_type' => 'dt_home_event',
                'object_name' => $eventName,
                'object_note' => '',
                'meta_key' => $eventName,
                'meta_value' => $eventValue
            ],
            [
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ]
        );
    }

    /**
     * Get the activity logs
     *
     * @param string $eventName The event name
     * @return array The activity logs
     */
    private function get_activity_logs( string $eventName, $start_date = null, $end_date = null ): array
    {
        [$wpdb, $table_name] = $this->initialize_db();

        $query = "SELECT histid, user_id, action, object_type, object_name, object_note, meta_key, meta_value, FROM_UNIXTIME(hist_time) as hist_time FROM $table_name WHERE object_type = %s";
        $params = [ 'dt_home_event' ];

        if ( $eventName ) {
            $query .= " AND action = %s";
            $params[] = $eventName;
        }

        if ( $start_date && $end_date ) {
            $query .= " AND hist_time BETWEEN %s AND %s";
            $params[] = strtotime( $start_date );
            $params[] = strtotime( $end_date );
        }

        $results = $wpdb->get_results( $wpdb->prepare( $query, ...$params ), ARRAY_A );

        return $results;
    }
}
