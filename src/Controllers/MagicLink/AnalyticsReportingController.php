<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\AnalyticsReporting;
use function DT\Home\response;

class AnalyticsReportingController
{
    private AnalyticsReporting $analytics_reporting;

    public function __construct( AnalyticsReporting $analytics_reporting )
    {
        $this->analytics_reporting = $analytics_reporting;
    }

    /**
     * Get analytics reports.
     *
     * @param Request $request The request object.
     * @return ResponseInterface The response object.
     */
    public function get_analytics_reports( Request $request ): ResponseInterface
    {
        // Get query parameters
        $query_params = $request->getQueryParams();
        $metrics = $this->extract_metrics( $query_params );
        $start_date = $query_params['date_start'] ?? null;
        $end_date = $query_params['date_end'] ?? null;

        // Fetch reports from the service
        $reports = $this->analytics_reporting->get_reports( $metrics, $start_date, $end_date );

        // Return response as JSON
        return response( [ 'reports' => $reports ] );
    }

    /**
     * Extract metrics from the query parameters.
     *
     * @param array $query_params The query parameters.
     * @return array The extracted metrics.
     */
    private function extract_metrics( array $query_params ): array
    {
        return isset( $query_params['metrics'] ) ? explode( ',', $query_params['metrics'] ) : [];
    }
}
