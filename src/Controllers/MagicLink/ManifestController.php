<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Psr\Http\Message\ServerRequestInterface as Request;
use function DT\Home\response;

class ManifestController {
    public function show( Request $request, $params ): ResponseInterface {
        $key = $params['key'];
        $launcher_url = '/apps/launcher/' . $key;

        $manifest = [
            'name' => 'D.T Home',
            'short_name' => 'D.T Home',
            'description' => 'D.T HomeSreen',
            'start_url' => $launcher_url,
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#3f729b',
            'icons' => [
                [
                    'src' => get_template_directory_uri() . '/dt-assets/favicons/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => get_template_directory_uri() . '/dt-assets/favicons/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
                [
                    'src' => get_template_directory_uri() . '/dt-assets/favicons/apple-touch-icon.png',
                    'sizes' => '180x180',
                    'type' => 'image/png',
                ],
            ],
        ];

        $json_manifest = json_encode( $manifest, JSON_UNESCAPED_SLASHES );

        return response( $json_manifest )
            ->withHeader( 'Content-Type', 'application/json' );
    }
}
