<?php

namespace DT\Home\Providers;

use DT\Home\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Home\CodeZone\WPSupport\Assets\AssetQueue;
use DT\Home\Services\Assets;
use DT\Home\CodeZone\WPSupport\Assets\AssetQueueInterface;
use function DT\Home\magic_url;
use function DT\Home\namespace_string;
use function DT\Home\config;
use function DT\Home\route_url;

class AssetServiceProvider extends AbstractServiceProvider
{

    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise. */
    public function provides( string $id ): bool
    {
        return in_array($id, [
            AssetQueue::class,
            Assets::class
        ]);
    }

    /**
     * Register method.
     *
     * This method is used to register filters and dependencies for the plugin.
     *
     * @return void
     */
    public function register(): void{
        add_filter( namespace_string( 'allowed_styles' ), function ( $allowed_css ) {
            return array_merge( $allowed_css, config( 'assets.allowed_styles' ) );
        } );

        add_filter( namespace_string( 'allowed_scripts' ), function ( $allowed_js ) {
            return array_merge( $allowed_js, config( 'assets.allowed_scripts' ) );
        } );

        add_filter( namespace_string( 'javascript_globals' ), function ( $data ) {
            $javascript_globals = config( 'assets.javascript_globals' );
            
            // Handle callback function for translations
            if ( isset( $javascript_globals['translations'] ) && is_callable( $javascript_globals['translations'] ) ) {
                $javascript_globals['translations'] = $javascript_globals['translations']();
            }
            
            return array_merge( $data, $javascript_globals, [
                'magic_url' => magic_url(),
                'nonce' => wp_create_nonce( 'dt_home' ),
                'admin_nonce' => wp_create_nonce( 'dt_admin_form_nonce' ),
                'route_url' => route_url(),
            ] );
        });

        $this->getContainer()->add( AssetQueueInterface::class, function () {
            return new AssetQueue();
        } );

        $this->getContainer()->add( Assets::class, function () {
            return new Assets(
                $this->getContainer()->get( AssetQueueInterface::class )
            );
        } );
    }
}
