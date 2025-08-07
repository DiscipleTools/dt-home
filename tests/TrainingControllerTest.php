<?php

namespace Tests;

use DT\Home\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Home\Controllers\MagicLink\TrainingController;
use function DT\Home\container;

class TrainingControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_shows() {
        $request = ServerRequestFactory::from_globals();
        $video = training_factory();
        $controller = container()->get( TrainingController::class );
        $response = $controller->show( $request, [ 'key' => $video['key'] ] );
        $this->assertEquals( 200, $response->getStatusCode() );
    }
}
