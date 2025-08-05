<?php

namespace Tests;

use DT\Home\Controllers\Admin\AppSettingsController;
use DT\Home\League\Container\Exception\NotFoundException;
use DT\Home\Services\Apps;
use DT\Home\Services\SourceFactory;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

class SourceFactoryTest extends TestCase {

    /**
     * @test
     */
    public function it_makes_from_classnames() {
        $source = SourceFactory::make( UserApps::class );
        $this->assertInstanceOf( UserApps::class, $source );
    }

    /**
     * @test
     */
    public function it_makes_from_handles() {
        $source = SourceFactory::make( 'user' );
        $this->assertInstanceOf( UserApps::class, $source );
    }

    /**
     * @test
     */
    public function it_makes_from_instances() {
        $source = SourceFactory::make( new UserApps() );
        $this->assertInstanceOf( UserApps::class, $source );
    }

    /**
     * @test
     */
    public function it_makes_from_invalid_handles()
    {
        $this->expectException( NotFoundException::class );
        SourceFactory::make( 'invalid' );
    }

    /**
     * @test
     */
    public function it_can_get_classnames_from_handles() {
        $this->assertEquals( 'user', SourceFactory::classname_to_handle( UserApps::class ) );
    }
}
