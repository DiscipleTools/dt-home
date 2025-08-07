<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */

use DT\Home\Middleware\CanAccess;
use DT\Home\Middleware\SetBypassCookie;
use DT\Home\Middleware\UnCached;

$config->merge( [
    'routes' => [
        'rewrites' => [
            '^apps/?$' => 'index.php?dt-home=/',
            '^apps/(.+)/?' => 'index.php?dt-home=$matches[1]',
            '^dt-home/?$' => 'index.php?dt-home-redirect=/',
            '^dt-home/(.+)/?' => 'index.php?dt-home-redirect=$matches[1]',
        ],
        'files' => [
            'web' => [
                "file" => "web.php",
                'query' => 'dt-home',
                'path' => 'apps',
            ]
        ],
        'middleware' => [
            new SetBypassCookie(),
	        new UnCached(),
            new CanAccess()
        ],
    ],
] );
