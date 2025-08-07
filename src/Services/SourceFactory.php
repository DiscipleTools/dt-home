<?php

namespace DT\Home\Services;

use DT\Home\League\Container\Exception\NotFoundException;
use DT\Home\Sources\AppSource;
use function DT\Home\config;
use function DT\Home\container;

/**
 * Factory for creating instances of sources.
 */
class SourceFactory {
    /**
     * Make an instance of the given source using the container.
     *
     * @param mixed $source The source to create an instance of.
     *                      Can be either an object or a string.
     * @return AppSource The created instance of the source.
     *
     */
    public static function make( $source ): AppSource {
        $classname = self::as_classname( $source );
        $available_sources = array_values( config( 'apps.sources', [] ) );

        if ( ! in_array( $classname, $available_sources ) ) {
            throw new NotFoundException( 'Invalid source type: ' .  esc_attr( $classname ) );
        }

        return container()->get( $classname );
    }

    /**
     * Get the class name of the given source.
     *
     * @param mixed $source The source to get the class name from.
     *                      Can be either an object or a string.
     * @return string The class name of the source.
     *
     * @throws \InvalidArgumentException If the source type is invalid.
     */
    public static function as_classname( $source ): string {
        if ( is_object( $source ) ) {
            return get_class( $source );
		}

        if ( ! is_string( $source ) ) {
            throw new \InvalidArgumentException( 'Invalid source type' );
		}

        return self::handle_to_classname( $source );
    }

    /**
     * Get the class name based on the handle.
     *
     * @param mixed $handle The handle to get the class name from.
     * @return string The class name associated with the handle.
     */
    public static function handle_to_classname( $handle ) {
        $handles = config( 'apps.sources', [] );
        return $handles[$handle] ?? $handle;
    }

    /**
     * Transforms a class name to a corresponding handle.
     *
     * @param string $classname The class name to transform.
     * @return string The corresponding handle.
     */
    public static function classname_to_handle( $classname ) {
        $handles = config( 'apps.sources', [] );
        //phpcs:ignore
        return array_search( $classname, $handles ) ?: $classname;
    }
}
