<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Home\MagicLinks\Launcher;
use DT\Home\MagicLinks\AppJson;

$config->merge( [
    'magic' => [
        'links' => [
            Launcher::class,
            AppJson::class
        ]
    ]
] );
