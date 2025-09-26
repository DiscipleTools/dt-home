<?php

namespace DT\Home\Middleware;

use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Psr\Http\Message\ServerRequestInterface;
use DT\Home\Psr\Http\Server\MiddlewareInterface;
use DT\Home\Psr\Http\Server\RequestHandlerInterface;
use DT\Home\Services\RolesPermissions;
use function DT\Home\container;
use function DT\Home\response;

class CanAccess implements MiddlewareInterface {

    /**
     * If enabled, determine if current user has can_access_home_screen
     * permission, in order to use plugin.
     *
     * @param ServerRequestInterface $request The HTTP request.
     * @param RequestHandlerInterface $handler The request handler.
     *
     * @return ResponseInterface The HTTP response.
     */
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        if ( !container()->get( RolesPermissions::class )->can_access_plugin( get_current_user_id() ) ) {
            return response( __( 'Plugin not found', 'dt-home' ), 404 );
        }

        return $handler->handle( $request );
    }
}
