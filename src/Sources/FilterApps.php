<?php

namespace DT\Home\Sources;

/**
 * Retrieves the raw array of home apps.
 *
 * This function applies the 'dt_home_apps' filter to an empty array,
 * returning the result.
 *
 * @param array $params The parameters to pass to the filter.
 *
 * @return array The raw array of home apps.
 */
class FilterApps extends AppSource {

    /**
     * Retrieves the raw array of home apps.
     *
     * This function applies the 'dt_home_apps' filter to an empty array,
     * returning the result.
     *
     * @return array The raw array of home apps.
     */
    public function raw( array $params = [] ): array {
        $result = apply_filters( 'dt_home_apps', [] );

        if ( ! $result ) {
            $result = [];
        }

        return $result;
    }

    /**
     * Checks if the application is allowed.
     *
     * @param array $app The application data.
     *
     * @return bool True if the application is allowed, false otherwise.
     */
    public function is_allowed( array $app ): bool {
        $allowed = true;
        if ( isset( $app['is_deleted'] ) && $app['is_deleted'] === true ) {
            $allowed = false;
        }

        return apply_filters( 'dt_home_app_allowed', $allowed, $app );
    }

    /**
     * Formats the application data.
     *
     * @param array $app The application data to format.
     *
     * @return array The formatted application data.
     */
    protected function format_app( array $app ): array {
        $sort = $app['sort'] ?? '';

        $overrides = [
            'creation_type' => 'code',
            'source' => static::handle(),
            'is_deleted' => false,
            'is_hidden' => false,
        ];

        if ( ! is_numeric( $sort ) ) {
            $overrides['sort'] = 10;
        }

        return array_merge([
                'name' => '',
                'type' => 'Web View',
                'icon' => '',
                'url' => '',
                'sort' => 10,
                'slug' => '',
        ], $app, $overrides);
    }

    /**
     * Merges the existing application data with the new application data.
     *
     * @param array $existing The existing application data.
     * @param array $app The new application data.
     *
     * @return array The merged application data.
     */
    public function merge( $existing, $app ) {
        $type = $existing['creation_type'] ?? 'custom';

        //If there is no existing app or the existing app is not a coded app, just merge the app
        if ( ( ! isset( $apps[ $app['slug'] ] ) )
            || ( $type !== 'code' ) ) {
            return array_merge( $existing, $app );
        }

        //If the existing app is a coded app, we need to keep the url and magic link meta
        $overrides = [
            'url' => $existing['url'] ?? ''
        ];

        if ( isset( $app['magic_link_meta'] ) ) {
            $overrides['magic_link_meta'] = $app['magic_link_meta'];
        }


        //Merge the app with the overrides
        return array_merge( $existing, $app, $overrides );
    }

    /**
     * Saves the given applications.
     *
     * @param array $apps The applications to be saved.
     * @return void
     */
    public function save( $apps, array $options = [] ): bool
    {
        throw new \Exception( 'Saving filter apps is not supported. Save the app with the app with the Settings or User app source instead.' );
    }
}
