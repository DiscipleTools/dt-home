<?php

namespace DT\Home\Sources;

use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;

/**
 * Retrieves the raw array of home apps.
 *
 * This function applies the 'dt_home_apps' filter to an empty array,
 * returning the result.
 *
 * @return array The raw array of home apps.
 */
class SettingsApps extends AppSource {
    /**
     * Retrieves the raw array of home apps.
     *
     * This function applies the 'dt_home_apps' filter to an empty array,
     * returning the result.
     *
     * @return array The raw array of home apps.
     */
    public function raw( array $params = [] ): array {
        return get_plugin_option( 'apps' );
    }

    /**
     * Checks if the application is allowed.
     *
     * @param array $app The application data.
     *
     * @return bool True if the application is allowed, false otherwise.
     */
    public function is_allowed( array $app ): bool {
        return $app['is_deleted'] !== true;
    }

    /**
     * Save apps.
     *
     * @param array $apps The apps to be saved.
     * @return bool Whether the saving was successful or not.
     */
    public function save( $apps, array $options = [] ): bool {
        return set_plugin_option( 'apps', $apps );
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

        $overrides = [];

        if ( ! is_numeric( $sort ) ) {
            $overrides['sort'] = 10;
        }

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
        ], $app, $overrides);
    }
}
