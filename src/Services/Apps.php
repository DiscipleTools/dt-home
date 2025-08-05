<?php

namespace DT\Home\Services;

use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

/**
 * Queries apps from multiple sources,
 * and may add business logic that the app sources do not.
 */
class Apps {
    private MagicApps $magic_apps;

    public function __construct( MagicApps $magic_apps )
    {
        $this->magic_apps = $magic_apps;
    }

    /**
     * Get all apps from the specified app source classname.
     *
     * @param string $app_source The class name of the app source.
     * @param array $params Optional parameters for filtering the apps.
     */
    public function from( $app_source, array $params = [] ) {
        $aggregator = new Aggregator( [
            $app_source
        ]);
        return $aggregator->all( $params );
    }

    /**
     * Alias for from method.
     *
     * @param string $app_source
     * @param array $params
     * @return array
     */
    public function source( $app_source, array $params = [] ) {
        return $this->from( $app_source, $params );
    }

    /**
     * Retrieve the apps array for a specific user.
     *
     * If the user has a specific apps array set, it will be returned.
     *
     * @param int $user_id The ID of the user.
     * @param array $params Optional parameters for filtering the apps.
     */
    public function for( int $user_id = 0, array $params = [] ) {
        if ( $user_id === 0 ) {
            $user_id = get_current_user_id();
        }
        $params['user_id'] = $user_id;
        $apps = $this->from( UserApps::class, $params );

        // Filter out apps; which the user does not currently have permission to access and reindex.
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $dt_custom_roles = get_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [] );
        $apps = array_values( array_filter( $apps, function ( $app ) use ( $user_id, $roles_permissions_srv, $dt_custom_roles ) {
            return $roles_permissions_srv->has_permission( $app, $user_id, $dt_custom_roles );
        } ) );

        // Proceed with hydration of magic link urls.
        $this->magic_apps->hydrate_magic_urls( $apps, $user_id );
        return $apps;
    }

    /**
     * Find an app for a specific user by the app's slug.
     *
     * @param string $slug The slug of the app.
     * @param int $user_id The ID of the user.
     * @param array $params Optional parameters for filtering the apps.
     * @return array|null The app with matching slug for the user, or null if not found.
     */
    public function find_for( string $slug, int $user_id, array $params = [] )
    {
        if ( $user_id === 0 ) {
            $user_id = get_current_user_id();
        }
        $params['user_id'] = $user_id;
        $service = SourceFactory::make( UserApps::class );
        $app = $service->find( $slug, $params );
		if ( ! $app ) {
			return null;
		}
        $this->magic_apps->hydrate_magic_url( $app, $user_id );
        return $app;
    }

    /**
     * Find an app by slug.
     *
     * @param string $slug The slug of the app.
     * @return array|null The app with matching slug, or null if not found.
     */
    public function find( string $slug, array $params = [] ) {
        $source = $params['source'] ?? SettingsApps::class;
        $apps = $this->from( $source, $params );
        return $this->first_with_slug( $apps, $slug );
    }

    /**
     * Confirm app slug exists.
     *
     * @param string $slug The slug of the app.
     * @return bool
     */
    public function has( string $slug ): bool {
        return !empty( $this->find( $slug ) );
    }

    /**
     * Move identified app in the specified direction.
     *
     * @param string $slug
     * @param string $direction
     * @return bool
     */
    public function move( string $slug, string $direction ): bool {
        if ( $this->has( $slug ) ) {
            $key = 'sort';
            $settings_apps = container()->get( SettingsApps::class );

            // Fetch all apps in ascending order, with reset sort counts.
            $apps = $settings_apps->sort( $this->from( SettingsApps::class ), [
                'reset' => true,
            ] );

            // Adjust sort count for specified app.
            $apps = array_map( function ( $app ) use ( $slug, $direction, $key ) {
                if ( $slug === $app['slug'] ?? '' ) {

                    /**
                     * Increment or Decrement accordingly by a couple hops, to
                     * ensure new sort position falls on the lower or upper
                     * side of adjacent app; based on specified direction.
                     */

                    switch ( $direction ){
                        case 'up':
                            $app[ $key ] -= 2;
                            break;
                        case 'down':
                            $app[ $key ] += 2;
                            break;
                    }
                }

                return $app;
            }, $apps );

            // Refresh counts following adjustments.
            $apps = $settings_apps->sort( $apps, [
                'reset' => true,
            ] );

            // Save updated apps list.
            return $settings_apps->save( $apps );
        }

        return false;
    }

    /**
     * Import specified apps; creating or updating accordingly, based on incoming slugs.
     *
     * @param array $importing_apps
     *
     * @return bool
     */
    public function import( array $importing_apps ): bool
    {
        $required_properties = [ 'slug', 'name', 'icon', 'type', 'url' ];

        // Filter out valid apps, suitable for import.
        $filtered_apps = array_filter( $importing_apps, function ( $app ) use ( $required_properties ) {

            // Ensure required properties are present.
            return count( $required_properties ) === count( array_intersect( $required_properties, array_keys( $app ) ) );
        } );

        if ( empty( $filtered_apps ) ) {
            return false;
        }

        // Ensure key internal properties, are also present within importing apps.
        $filtered_apps = array_map( function ( $filtered_app ) {
            return array_merge( [
                'creation_type' => 'custom',
                'source' => 'settings',
                'sort' => 10,
                'is_hidden' => false,
                'is_deleted' => false,
                'open_in_new_tab' => true,
                'roles' => []
            ], $filtered_app );
        }, $filtered_apps );

        // Fetch existing system apps.
        $existing_apps = $this->from( SettingsApps::class );

        // Extract array of importing slug ids.
        $importing_slugs = array_map( function ( $app ) {
            return $app['slug'];
        }, $filtered_apps );

        // Proceed with import and reshaping of existing apps.
        $updated_apps = $filtered_apps;
        foreach ( $existing_apps as $existing_app ) {
            if ( !in_array( $existing_app['slug'], $importing_slugs ) ) {
                $updated_apps[] = $existing_app;
            }
        }

        // Save updated apps list.
        return container()->get( SettingsApps::class )->save( $updated_apps );
    }

    /**
     * Returns the first element of the given array or null if the array is empty.
     *
     * @param array $apps The input array.
     * @return mixed|null The first element of the array or null if the array is empty.
     */
    private function first( array $apps ) {
        return !empty( $apps ) ? $apps[0] : null;
    }

    /**
     * Filter the apps array by slug and return an array of matching items.
     * @param array $apps
     * @param string $slug
     * @return array
     */
    private function with_slug( array $apps, string $slug ): array
    {
        return $this->filter( $apps, 'slug', $slug );
    }

    /**
     * Filter the apps array to find the items with matching key and value.
     *
     * @param array $apps The array of apps.
     * @param string $key The key to check for a match.
     * @param mixed $value The value to check for a match.
     *
     * @return array The filtered array of apps with matching key and value.
     */
    private function filter( array $apps, string $key, $value ): array
    {
        // Filter the $apps array to find the item with matching key and value.
        $filtered_apps = array_filter($apps, function ( $app ) use ( $key, $value ) {
            return ( $app[$key] ?? '' ) === $value;
        });

        // array_filter preserves array keys, so use array_values to reindex it
        return array_values( $filtered_apps );
    }


    /**
     * Get the first app from the apps array with the given slug.
     */
    private function first_with_slug( $apps, $slug )
    {
        $filtered_apps = $this->with_slug( $apps, $slug );
        return $this->first( $filtered_apps );
    }
}
