<?php

namespace DT\Home\Sources;

use DT\Home\Services\RolesPermissions;
use function DT\Home\container;

class UserApps extends AppSource {
    /**
     * Retrieves the raw array of home apps.
     *
     * This function applies the 'dt_home_apps' filter to an empty array,
     * returning the result.
     *
     * @return array The raw array of home apps.
     */
    public function raw( array $params = [] ): array {
        $user_id = $params['user_id'] ?? get_current_user_id();
        $result = get_user_option( 'dt_home_apps', $user_id );
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
        return container()->get( RolesPermissions::class )->has_permission( $app, get_current_user_id(), get_option( RolesPermissions::OPTION_KEY_CUSTOM_ROLES, [] ) );
    }

    /**
     * Retrieves all coded applications for a specific user.
     *
     * @param int $user_id The ID of the user.
     *
     * @return array All applications data for the specified user.
     */
    public function for( $user_id ): array {
        return $this->all( [ 'user_id' => $user_id ] );
    }

    /**
     * Saves applications for a specific user.
     *
     * @param int $user_id The ID of the user.
     * @param array $apps The applications to be saved.
     *
     * @return bool Indicates whether the saving operation was successful.
     */
    public function save_for( $user_id, $apps ): bool {
        return $this->save( $apps, [ 'user_id' => $user_id ] );
    }

    /**
     * Save apps.
     *
     * @param array $apps The apps to be saved.
     * @return bool Whether the saving was successful or not.
     */
    public function save( $apps, array $options = [] ): bool {
        $user_id = $options['user_id'] ?? get_current_user_id();
        return update_user_option( $user_id, 'dt_home_apps', array_values( $apps ) );
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
}
