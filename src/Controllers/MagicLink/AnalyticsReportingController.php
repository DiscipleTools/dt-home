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
        $queryParams = $request->getQueryParams();
        $metrics = $this->extract_metrics( $queryParams );

        // Fetch reports from the service
        $reports = $this->analytics_reporting->get_reports( $metrics );

        // Return response as JSON
        return response( [ 'reports' => $reports ] );
    }

    /**
     * Extract metrics from the query parameters.
     *
     * @param array $queryParams The query parameters.
     * @return array The extracted metrics.
     */
    private function extract_metrics( array $queryParams ): array
    {
        return isset( $queryParams['metrics'] ) ? explode( ',', $queryParams['metrics'] ) : [];
    }
}
