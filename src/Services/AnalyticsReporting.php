<?php

namespace DT\Home\Services;

class AnalyticsReporting
{

    public function __construct() {
    }

    /**
     * Get the analytics reports
     *
     * @param array $params The parameters
     * @return array The analytics reports
     */
    public function get_reports( array $params = [], $start_date = null, $end_date = null ): array {
        $reports = [];

        foreach ( $params as $metric ) {
            $reports[$metric] = $this->get_activity_logs( $metric, $start_date, $end_date );
        }

        return $reports;
    }

    /**
     * Initialize the database
     *
     * @return array The database connection, table name, and user ID
     */
    private function initialize_db(): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dt_activity_log';
        $user_id = get_current_user_id();

        return [ $wpdb, $table_name, $user_id ];
    }

    /**
     * Log an event
     *
     * @param string $event_name The event name
     */
    public function log_event( string $event_name, $event_value = '' ): void {
        [ $wpdb, $table_name, $user_id ] = $this->initialize_db();

        $wpdb->insert(
            $table_name,
            [
                'user_id' => $user_id,
                'action' => $event_name,
                'hist_time' => time(),
                'object_type' => 'dt_home_event',
                'object_name' => $event_name,
                'object_note' => '',
                'meta_key' => $event_name,
                'meta_value' => $event_value
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
     * @param string $event_name The event name
     * @return array The activity logs
     */
    private function get_activity_logs( string $event_name, $start_date = null, $end_date = null ): array {
        if ( empty( $event_name ) ) {
            return [];
        }

        [ $wpdb, $table_name ] = $this->initialize_db();

        $query = "SELECT hist_time as evt_timestamp, action as event, meta_value as value FROM $table_name WHERE object_type = %s AND action = %s";
        $params = [ 'dt_home_event', $event_name ];

        if ( $start_date && $end_date ) {
            $query .= " AND hist_time BETWEEN %s AND %s";
            $params[] = strtotime( $start_date );
            $params[] = strtotime( $end_date );
        }

        $query .= " ORDER BY hist_time ASC";

        return $wpdb->get_results( $wpdb->prepare( $query, ...$params ), ARRAY_A );
    }
}
