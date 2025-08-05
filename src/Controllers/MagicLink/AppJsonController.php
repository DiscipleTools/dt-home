<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Services\Apps;
use function DT\Home\template;

/**
 * Class JsonController
 *
 * Controls the display of application json setting structures.
 */
class AppJsonController {
    private $apps_service;

    public function __construct( Apps $apps ) {
        $this->apps_service = $apps;
    }

    /**
     * Display the app json settings
     *
     * @param Request $request The request object.
     * @param array $params The parameters.
     */
    public function index( Request $request, $params ) {

        $apps = [];
        $display_by_exportable_flag = true;
        $required_properties = [ 'slug', 'name', 'icon', 'type', 'url' ];

        // If slugs url parameter array is present, then display json by specified slugs.
        $url_query_params = $request->getQueryParams();
        if ( !empty( $url_query_params ) && is_array( $url_query_params['slugs'] ) ) {
            $display_by_exportable_flag = false;
            foreach ( $url_query_params['slugs'] as $slug ){
                if ( $this->apps_service->has( $slug ) ){
                    $apps[] = $this->apps_service->find( $slug );
                }
            }
        } else {
            $apps = $this->apps_service->for();
        }

        // Fetch apps with json exportable flag enabled.
        $exportable_apps = array_filter( $apps, function ( $app ) use ( $display_by_exportable_flag, $required_properties ) {

            // Ensure exportable flag is set.
            if ( $display_by_exportable_flag && ( !isset( $app['is_exportable'] ) || boolval( $app['is_exportable'] ) === false ) ) {
                return false;
            }

            // Ensure required properties are present.
            return count( $required_properties ) === count( array_intersect( $required_properties, array_keys( $app ) ) );
        } );

        // Sort exportable apps into ascending order by name.
        usort( $exportable_apps, function ( $a, $b ) {
            if ( !isset( $a['name'], $b['name'] ) ) {
                return false;
            }

            return strcmp( $a['name'], $b['name'] );
        } );

        // Next, reshape identified exportable apps into required json output shape.
        $apps = [];
        foreach ( $exportable_apps as $app ) {
            $reshaped_app = [];
            foreach ( $required_properties as $key ) {
                $reshaped_app[$key] = $app[$key];
            }

            $apps[] = $reshaped_app;
        }

        // Call json index template view, with reshaped apps array.
        return template(
            'json/index',
            compact( 'apps' )
        );
    }
}
