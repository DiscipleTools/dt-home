<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */
$config->merge( [
    'plugin' => [
        'text_domain' => 'dt-home',
        'nonce_name' => 'dt_home',
        'share_cookie' => 'dt_home_share',
        'dt_version' => 1.19,
        'paths' => [
            'src' => 'src',
            'resources' => 'resources',
            'routes' => 'routes',
            'views' => 'resources/views',
        ]
    ]
]);
