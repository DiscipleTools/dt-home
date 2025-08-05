<?php

namespace DT\Home\Sources;

use DT\Home\Services\Aggregator;
use function DT\Home\config;

/**
 * Base class for application sources.
 *
 * @param array $apps Array of applications.
 * @return array Deleted applications.
 */
abstract class AppSource extends Source
{
    /**
     * Handle the application.
     *
     * @return string The name of the application.
     */
    public static function handle(): string
    {
        $handles = config( 'apps.sources', [] );
        //phpcs:ignore
        return array_search( static::class, $handles ) ?: self::class;
    }

	/**
	 * Returns all applications without applying any filters.
	 *
	 * @param array $params An array of parameters for retrieving applications.
	 *
	 * @return array An array containing all the items without any filters applied.
	 */
	public function unfiltered( array $params = [] ): array {
		$params['merged'] = $params['merged'] ?? false;

		if ( $params['merged'] === true ) {
			return $this->merged( $params );
		}

		return $this->all( $params );
	}

	/**
	 * Returns all applications without applying any filters.
	 *
	 * @param string $slug The ID of the application to retrieve.
	 * @param array $params An array of parameters for retrieving applications.
	 *
	 * @return array|null An array containing all the items without any filters applied or null if not found.
	 */
	public function find_unfiltered( $slug, array $params = [] ) {
		return $this->find( $slug, array_merge( [ 'filter' => false ], $params ) );
	}

    /**
     * We want to merge in dependencies when saving.
     *
     * @param array $params Parameters for fetching data.
     * @return array Data merged and ready for saving.
     */
    public function fetch_for_save( array $params = [] ): array
    {
		$params['merged'] = $params['merged'] ?? true;
        return $this->unfiltered( $params );
    }

    /**
     * Provides a place to control the way data is merged for the app source.
     *
     * @param array $existing The existing array.
     * @param array $app The app array to merge.
     * @return array The merged array.
     */
    public function merge( $existing, $app ) {
        $overrides = [];
        return array_merge( $existing, $app, $overrides );
    }

    /**
     * Returns the applications merged with its dependencies.
     *
     * @param array $params An array of parameters for filtering the items.
     * @return array An array containing all the aggregated items.
     */
    public function merged( array $params = [] ) {
		unset( $params['merged'] );
        $aggregator = new Aggregator( [ $this ] );
        return $aggregator->all( $params );
    }

    /**
     * Returns all applications.
     *
     * @return array An array containing all the items.
     */
    public function all( array $params = [] ): array {
	    $params['merged'] = $params['merged'] ?? false;
		$params['filter'] = $params['filter'] ?? true;

	    $filter = $params['filter'];

		// If we are merging in inherited apps, we need to fetch them from the aggregator
		if ( $params['merged'] === true ) {
			return $this->merged( $params );
		}

        if ( $filter === true ) {
            return $this->filter(
                $this->formatted( $params )
            );
        }
        return $this->formatted( $params );
    }

    /**
     * Filters the array of applications.
     *
     * @param array $apps The array of applications to be filtered.
     *
     * @return array The filtered array of applications.
     */
    public function filter( array $apps = [] ): array {
        return array_filter( $apps, function ( $app ) {
            return $this->is_allowed( $app );
        } );
    }

    /**
     * Checks if the application is allowed.
     *
     * @param array $app The application data.
     *
     * @return bool True if the application is allowed, false otherwise.
     */
    public function is_allowed( array $app ): bool {
        return true;
    }

    /**
     * Filters and retrieves disallowed applications.
     *
     * @param array $apps Array of applications.
     * @return array Disallowed applications.
     */
    public function disallowed( array $apps ): array {
        return array_filter( $apps, function ( $app ) {
            return !$this->is_allowed( $app );
        } );
    }

    /**
     * Formats the given applications.
     *
     * @param array $apps The applications to be formatted.
     *
     * @return array The formatted applications.
     */
    protected function format( $apps ): array {
        $apps = array_map(function ( $app ) {
            return apply_filters( 'dt_home_format_app', $this->format_app( is_array( $app ) ? $app : [] ) );
        }, $apps);

        return $this->sort( $apps );
    }


    /**
     * Formats the application data.
     *
     * @param array $app The application data to format.
     *
     * @return array The formatted application data.
     */
    protected function format_app( array $app ): array {
        return array_merge([
            'name' => '',
            'type' => 'Web View',
            'creation_type' => 'custom',
            'source' => static::handle(),
            'icon' => '',
            'url' => '',
            'sort' => 10,
            'slug' => '',
            'is_hidden' => false,
            'is_deleted' => false,
        ], $app);
    }

    /**
     * Sets the "is_hidden" property to true for a given application.
     *
     * @param string $slug The slug of the application.
     * @param array $params Optional parameters to apply when retrieving applications.
     * @return array The updated application data.
     */
    public function hide( $slug, $params = [] ) {
        $this->set( $slug, 'is_hidden', true );
        return $this->find( $slug, $params );
    }

    /**
     * Sets the "is_hidden" property to false for a given application.
     *
     * @param string $slug The slug of the application.
     * @param array $params Optional parameters to apply when retrieving applications.
     * @return array The updated application data.
     */
    public function unhide( $slug, $params = [] ) {
        $this->set( $slug, 'is_hidden', false );
        return $this->find( $slug, $params = [] );
    }

    /**
     * Checks if the application is visible.
     *
     * @param array $app The application data.
     *
     * @return bool True if the application is visible, false otherwise.
     */
    public function is_visible( array $app ): bool {
        return ! $app['is_hidden'];
    }

    /**
     * Filters and retrieves hidden applications.
     *
     * @param array $apps Array of applications.
     * @return array Hidden applications.
     */
    public function hidden( array $apps ): array {
        return array_filter( $apps, function ( $app ) {
            return !$this->is_visible( $app );
        } );
    }


    /**
     * Retrieves all deleted applications.
     *
     * @param array $params Optional parameters to apply when retrieving applications.
     * @return array All deleted applications data.
     */
    public function deleted( array $params = [] ): array {
        $params = array_merge( $params, [ 'filter' => false ] );
        $apps = $this->all( $params );
        $filtered = array_filter( $apps, function ( $app ) {
            return $app['is_deleted'] === true;
        } );
        return $filtered;
    }

    /**
     * Retrieves all undeleted applications.
     *
     * @param array $params Additional parameters for filtering.
     * @return array Undeleted applications data.
     */
    public function undeleted( array $params = [] ): array {
        $apps = $this->all( $params );
        $filtered = array_filter( $apps, function ( $app ) {
            return $app['is_deleted'] !== true;
        } );
        return $filtered;
    }

    /**
     * Soft Deletes an item.
     *
     * @param string $slug The slug of the item to be deleted.
     * @param array $params Optional. Additional parameters for the deletion. Default is an empty array.
     * @param array $save_params Optional. Additional parameters for saving the changes. Default is an empty array.
     *
     * @return bool Returns true if the item was successfully deleted.
     */
    public function delete( $slug, array $params = [], $save_params = [] ) {
        $this->set( $slug, 'is_deleted', true, $params, $save_params );
		return $this->value( $slug, 'is_deleted', $params );
    }

    /**
     * Removes an app with a specific slug.
     *
     * @param string $slug The slug of the app to be removed.
     * @return bool|mixed Returns false if the app with the given slug is not found,
     *                   otherwise returns result of save() method.
     */
    public function destroy( $slug, array $params = [], $save_params = [] ) {
        return parent::delete( $slug, $params, $save_params );
    }
}
