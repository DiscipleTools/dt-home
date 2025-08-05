<?php

namespace DT\Home\Services;

use function DT\Home\config;

/**
 * Fetches apps from multiple sources and merges them together.
 */
class Aggregator {
    protected array $requested_sources;
    protected array $sources;

    public function __construct( $sources = [] ) {
        $this->requested_sources = array_map( function ( $source ) {
            return SourceFactory::as_classname( $source );
        }, $sources );
        $this->sources = $this->build_sources();
    }

    /**
     * Retrieves all the apps from the sources and performs aggregation, filtering, and sorting.
     *
     * @param array $params Optional parameters for retrieving apps.
     * @return array An array of aggregated and filtered apps.
     */
    public function all( array $params = [] ): array {
        $apps = [];

        // Add an action hook to the 'dt_home_app_aggregated' hook for actions to react to the apps being aggregated.
        do_action( 'dt_home_app_aggregate', $this->sources, $params );

        //perform the aggregation
        foreach ( $this->sources as $source ) {
            $this->merge_source_apps( $source, $apps, $params );
        }
        $apps = $this->filter( $apps );
        $this->sort( $apps );

        do_action( 'dt_home_app_aggregated', $this->sources, $apps, $params );

        return $apps;
    }

    /**
     * Merges apps from a specific application source into the existing apps.
     *
     * @param string $source The source name to get the apps from.
     * @param array &$apps The existing apps as reference.
     *
     * @return void
     */
    private function merge_source_apps( string $source, array &$apps, $params = [] ) {
        $source = SourceFactory::make( $source );
        $source_apps = $source->unfiltered( $params );

        foreach ( $source_apps as $app ) {
            $idx = array_search( $app['slug'], array_column( $apps, 'slug' ) );
            if ( $idx === false ) {
                $idx = count( $apps );
            }
            $existing = $apps[$idx] ?? [];
            $apps[$idx] = $source->merge( $existing, $app );
        }
    }

    /**
     * Filters the array of apps based on whether they are allowed by their source.
     *
     * @param array $apps The array of apps to filter.
     * @return array The filtered array of apps.
     */
    private function filter( array $apps ): array {
        return array_filter( $apps, function ( $app ) {
            $source = SourceFactory::make( $app['source'] );
            return $source->is_allowed( $app );
        } );
    }

    /**
     * Builds the sources array based on the requested sources.
     *
     * @return array An array of sources matching the requested sources.
     */
    private function build_sources(): array
    {
        $config_sources = array_values( config( 'apps.sources', [] ) );
        $matching_sources = array_intersect( $config_sources, $this->requested_sources );
        $last_match = end( $matching_sources );
        $sources = [];
        foreach ( $config_sources as $source ) {
            if ( $source === $last_match ) {
                $sources[] = $source;
                break;
            }
            $sources[] = $source;
        }
        return $sources;
    }

    /**
     * Sort an array of apps based on the 'sort' key.
     *
     * @param array $apps The array of apps to
     */
    public function sort( &$apps )
    {
        usort($apps, function ( $a, $b ) {
            $sort_a = isset( $a['sort'] ) ? (int) $a['sort'] : 0;
            $sort_b = isset( $b['sort'] ) ? (int) $b['sort'] : 0;
            return $sort_a - $sort_b;
        });
    }
}
