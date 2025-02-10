<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use function DT\Home\view;

class ReportsController {

    /**
     * Show the local analytics reporting admin tab.
     *
     * @return ResponseInterface
     */
    public function show( Request $request ){
        $tab = 'reports';
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = 'Home Settings';
        $reports = $this->generate_reports();

        return view( 'settings/reports/reports', compact( 'tab', 'link', 'page_title', 'reports' ) );
    }

    /**
     * Build reports data structure; which shall list section headings and
     * associated sub-reports with labels, etc...
     *
     * return array
     */
    private function generate_reports(): array {
        return [
            'users' => [
                'label' => __( 'Users', 'dt-home' ),
                'tab' => 'analytics_reports_tab_users',
                'default_metric' => 'users-auth',
                'metrics' => [
                    [
                        'metric' => 'users-auth',
                        'label' => __( 'Authentication', 'dt-home' ),
                        'events' => [ 'login', 'logout' ],
                        'chart_type' => 'pie',
                        'calculation_type' => 'cumulative',
                        'filters' => [
                            'date-range'
                        ]
                    ],
                    [
                        'metric' => 'users-failures',
                        'label' => __( 'Failures', 'dt-home' ),
                        'events' => [ 'login-error' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'cumulative',
                        'filters' => [
                            'date-range'
                        ]
                    ]
                ]
            ],
            'apps' => [
                'label' => __( 'Apps', 'dt-home' ),
                'tab' => 'analytics_reports_tab_apps',
                'default_metric' => 'apps-creation-types',
                'metrics' => [
                    [
                        'metric' => 'apps-totals',
                        'label' => __( 'Total Counts', 'dt-home' ),
                        'events' => [ 'total-apps-count', 'total-active-apps-count' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'latest',
                        'filters' => []
                    ],
                    [
                        'metric' => 'apps-creation-types',
                        'label' => __( 'Creation Types', 'dt-home' ),
                        'events' => [ 'total-active-custom-apps-count', 'total-active-coded-apps-count' ],
                        'chart_type' => 'h-bar',
                        'calculation_type' => 'latest',
                        'filters' => [
                            'date-range'
                        ]
                    ],
                    [
                        'metric' => 'apps-app-types',
                        'label' => __( 'App Types', 'dt-home' ),
                        'events' => [ 'total-apps-web-views-count', 'total-apps-links-count', 'total-apps-native-links-count' ],
                        'chart_type' => 'v-bar',
                        'calculation_type' => 'latest',
                        'filters' => [
                            'date-range'
                        ]
                    ],
                    [
                        'metric' => 'apps-coded-deletes',
                        'label' => __( 'Deleted Coded Apps', 'dt-home' ),
                        'events' => [ 'total-deleted-coded-apps-count' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'latest',
                        'filters' => [
                            'date-range'
                        ]
                    ],
                    [
                        'metric' => 'apps-custom-deletes',
                        'label' => __( 'Deleted Custom Apps', 'dt-home' ),
                        'events' => [ 'deleted-custom-app' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'cumulative',
                        'filters' => [
                            'date-range'
                        ]
                    ]
                ]
            ],
            'training' => [
                'label' => __( 'Training Videos', 'dt-home' ),
                'tab' => 'analytics_reports_tab_training',
                'default_metric' => 'training-deletes',
                'metrics' => [
                    [
                        'metric' => 'training-totals',
                        'label' => __( 'Total Counts', 'dt-home' ),
                        'events' => [ 'total-active-training-videos-count' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'latest',
                        'filters' => [
                            'date-range'
                        ]
                    ],
                    [
                        'metric' => 'training-deletes',
                        'label' => __( 'Deleted Videos', 'dt-home' ),
                        'events' => [ 'deleted-training-video' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'cumulative',
                        'filters' => [
                            'date-range'
                        ]
                    ]
                ]
            ],
            'usage' => [
                'label' => __( 'Usage', 'dt-home' ),
                'tab' => 'analytics_reports_tab_usage',
                'default_metric' => 'usage-shares',
                'metrics' => [
                    [
                        'metric' => 'usage-shares',
                        'label' => __( 'Shared Links', 'dt-home' ),
                        'events' => [ 'share-link' ],
                        'chart_type' => 'count',
                        'calculation_type' => 'cumulative',
                        'filters' => [
                            'date-range'
                        ]
                    ]
                ]
            ]
        ];
    }
}
