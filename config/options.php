<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */

$config->merge( [
    'options' => [
        'prefix'   => 'dt_home',
        'defaults' => [
            'require_login' => true,
            'reset_apps'    => false,
            'invite_others' => true,
            'apps'          => [],
            'trainings'     => [],
        ],
    ]
] );
