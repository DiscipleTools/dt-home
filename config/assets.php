<?php

/**
 * @var $config DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Home\plugin_path;

$config->merge( [
    'assets' => [
        'allowed_styles' => [
            'material-font-icons',
            'material-font-icons-local',
            'dt-home',
        ],
        'allowed_scripts' =>[
            'dt-home',
        ],
        'javascript_global_scope' => '$home',
        'javascript_globals' => [
            'translations' => [
                'installAppLabel' => 'Install as App',
                'hiddenAppLabel' => 'Hidden Apps',
                'buttonLabel' => 'Ok',
            ]
        ],
        'manifest_dir' => plugin_path( '/dist' )
    ]
] );
