<?php

namespace DT\Home\Providers;

use DT\Home\CodeZone\WPSupport\Config\ConfigInterface;
use DT\Home\CodeZone\WPSupport\Rewrites\RewritesInterface;
use DT\Home\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Home\League\Container\Exception\NotFoundException;
use DT\Home\Plugin;
use DT\Home\Psr\Container\ContainerExceptionInterface;
use DT\Home\Services\Analytics;

class PluginServiceProvider extends AbstractServiceProvider
{
    /**
     * Provide the services that this provider is responsible for.
     *
     * @param string $id The ID to check.
     * @return bool Returns true if the given ID is provided, false otherwise.
     */
    public function provides( string $id ): bool
    {
        return in_array($id, [
            Plugin::class
        ]);
    }


    /**
     * Register the plugin and its service providers.
     *
     * @return void
     * @throws NotFoundException|ContainerExceptionInterface
     */
    public function register(): void
    {
        $this->getContainer()->addShared(Plugin::class, function () {
            return new Plugin(
                $this->getContainer(),
                $this->getContainer()->get( RewritesInterface::class ),
                $this->getContainer()->get( ConfigInterface::class ),
                $this->getContainer()->get( Analytics::class )
            );
        });
    }
}
