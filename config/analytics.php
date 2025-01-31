<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Home\Services\Analytics;

$config->merge([
    'analytics' => [
        'dt_home_analytics_reports' => 'dt_home_analytics_reports',
        'export_endpoints' => [
            'honeycomb' => [
                'traces' => [
                    'endpoint' => 'https://api.honeycomb.io/v1/traces',
                    'content_type' => 'application/json',
                    'headers' => [
                        'x-honeycomb-team' => getenv( 'ANALYTICS_HONEYCOMB_API_KEY' ),
                        'x-honeycomb-dataset' => Analytics::DT_HOME_PLUGIN_NAME
                    ]
                ],
                'metrics' => [
                    'endpoint' => 'https://api.honeycomb.io/v1/metrics',
                    'content_type' => 'application/json',
                    'headers' => [
                        'x-honeycomb-team' => getenv( 'ANALYTICS_HONEYCOMB_API_KEY' ),
                        'x-honeycomb-dataset' => Analytics::DT_HOME_PLUGIN_NAME
                    ]
                ],
                'logs' => [
                    'endpoint' => 'https://api.honeycomb.io/v1/logs',
                    'content_type' => 'application/json',
                    'headers' => [
                        'x-honeycomb-team' => getenv( 'ANALYTICS_HONEYCOMB_API_KEY' ),
                        'x-honeycomb-dataset' => Analytics::DT_HOME_PLUGIN_NAME
                    ]
                ]
            ]
        ]
    ]
]);
