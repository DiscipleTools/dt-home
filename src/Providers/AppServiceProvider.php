<?php

namespace DT\Home\Providers;

use DT\Home\Apps\Autolink;
use DT\Home\Apps\BiblePlugin;
use DT\Home\Apps\DiscipleTools;
use DT\Home\Apps\ThreeThirdsMeetings;
use DT\Home\League\Container\ServiceProvider\AbstractServiceProvider;
use DT\Home\League\Container\ServiceProvider\BootableServiceProviderInterface;
use DT\Home\Services\GarbageCollector;
use DT\Home\Services\MagicApps;
use DT\Home\Sources\FilterApps;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;

class AppServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $apps = [
        Autolink::class,
        BiblePlugin::class,
        ThreeThirdsMeetings::class,
        DiscipleTools::class,
    ];

    public function boot(): void
    {
        $this->getContainer()->addShared( MagicApps::class );
        $this->getContainer()->get( MagicApps::class );
        $this->getContainer()->get( GarbageCollector::class );

        foreach ( $this->apps as $app ) {
            $this->getContainer()->add( $app );
            $this->getContainer()->get( $app );
        }
    }

    public function provides( string $id ): bool
    {
        return in_array( $id, [
            FilterApps::class,
            SettingsApps::class,
            UserApps::class,
        ]);
    }

    public function register(): void
    {

        $this->getContainer()->add(FilterApps::class, function () {
            return new FilterApps();
        });

        $this->getContainer()->add(SettingsApps::class, function () {
            return new SettingsApps();
        });

        $this->getContainer()->add(UserApps::class, function () {
            return new UserApps();
        });
    }
}
