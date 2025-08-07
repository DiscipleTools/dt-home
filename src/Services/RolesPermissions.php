<?php

namespace DT\Home\Services;

use DT\Home\Sources\SettingsApps;
use WP_User;
use function DT\Home\container;
use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;

/**
 * Manage D.T Roles & Permissions.
 */
class RolesPermissions
{

    public const CAPABILITIES_SOURCE = 'Home Screen';
    public const OPTION_KEY_CUSTOM_ROLES = 'dt_custom_roles';
    public const OPTION_KEY_USE_CAPABILITIES = 'dt_home_use_capabilities';

    public function __construct()
    {
    }

    /**
     * Determine if roles & permissions enforcement is enabled.
     * @return bool
     */
    public function is_enabled(): bool
    {
        return get_plugin_option( self::OPTION_KEY_USE_CAPABILITIES, false );
    }

    /**
     * Update roles & permissions enforcement enabled state.
     * @param bool $enable
     * @return bool
     */
    public function enabled( bool $enable ): bool
    {
        return set_plugin_option( self::OPTION_KEY_USE_CAPABILITIES, $enable );
    }

    /**
     * Initialise and hook in to various filters and actions.
     * @return void
     */
    public function init(): void
    {
        add_filter( 'dt_capabilities', [ $this, 'dt_capabilities' ], 50, 1 );
        add_filter( 'dt_set_roles_and_permissions', [ $this, 'dt_set_roles_and_permissions' ], 10, 1 );
    }

    /**
     * Default D.T Home Screen capabilities.
     * @return array
     */
    private function default_capabilities(): array
    {
        $capabilities = [
            'can_access_home_screen' => [
                'source' => self::CAPABILITIES_SOURCE,
                'description' => ''
            ]
        ];

        // Capture available apps and build associated capabilities.
        foreach ( container()->get( SettingsApps::class )->all() ?? [] as $app ) {
            if ( isset( $app['slug'] ) ) {
                $capabilities[$this->generate_permission_key( $app['slug'] )] = [
                    'source' => self::CAPABILITIES_SOURCE,
                    'description' => ''
                ];
            }
        }

        return $capabilities;
    }

    /**
     * Register plugin specific D.T Capabilities.
     * @param array $capabilities
     * @return array
     */
    public function dt_capabilities( array $capabilities ): array
    {
        if ( $this->is_enabled() ) {
            $capabilities = array_merge( $capabilities, $this->default_capabilities() );
        }

        return $capabilities;
    }

    /**
     * Default D.T Home Screen role and permission assignments.
     * @return array
     */
    private function default_roles_and_permissions(): array
    {
        $default_roles = [
            'administrator',
            'custom_developer',
            'dispatcher',
            'dt_admin',
            'multiplier'
        ];

        // Pair default roles with capabilities; in an initial selected state.
        $default_roles_and_permissions = [];
        $default_capabilities = $this->default_capabilities();
        foreach ( $default_roles as $role ) {
            $default_roles_and_permissions[$role] = [];
            foreach ( array_keys( $default_capabilities ) as $capability ) {
                $default_roles_and_permissions[$role][$capability] = true;
            }
        }

        return $default_roles_and_permissions;
    }

    /**
     * Register plugin specific D.T Roles & Permissions.
     * @param array $expected_roles
     * @return array
     */
    public function dt_set_roles_and_permissions( array $expected_roles ): array
    {
        if ( $this->is_enabled() ) {
            $dt_custom_roles = get_option( self::OPTION_KEY_CUSTOM_ROLES, [] );

            /** $dt_custom_roles_updated = false; **/
            foreach ( $this->default_roles_and_permissions() as $role => $permissions ) {
                if ( !isset( $expected_roles[$role] ) ) {
                    $expected_roles[$role] = [ 'permissions' => [] ];
                } elseif ( !is_array( $expected_roles[$role]['permissions'] ) ) {
                    $expected_roles[$role]['permissions'] = [];
                }

                /**
                 * Ensure selected flag is set accordingly, based on saved
                 * custom role settings; which take priority.
                 */

                foreach ( $permissions as $permission => $selected ) {
                    $expected_roles[$role]['permissions'][$permission] = $dt_custom_roles[$role]['capabilities'][$permission] ?? $selected;

                    /**
                     * If no corresponding custom roles settings detected,
                     * capture and persist; to ensure selected flag state is made
                     * available further downstream for processing.
                     *
                     * if ( !is_array( $dt_custom_roles[$role]['capabilities'] ) ) {
                     * $dt_custom_roles[$role]['capabilities'] = [];
                     * }
                     *
                     * if ( !isset( $dt_custom_roles[$role]['capabilities'][$permission] ) ) {
                     * $dt_custom_roles_updated = true;
                     * $dt_custom_roles[$role]['capabilities'][$permission] = $expected_roles[$role]['permissions'][$permission];
                     * }
                     ***/
                }
            }

            /**if ( $dt_custom_roles_updated ) {
             * update_option( self::OPTION_KEY_CUSTOM_ROLES, $dt_custom_roles );
             * }**/
        }

        return $expected_roles;
    }

    /**
     * Build associated permission key, based on specified slug and type.
     *
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function generate_permission_key( string $slug, string $type = 'access' ): string
    {
        switch ( $type ) {
            case 'access':
            default:
                return 'can_access_' . $slug . '_app';
        }
    }

    /**
     * Update global user roles for specified permissions.
     *
     * @param string $app_slug
     * @param array $permissions
     * @param array $roles
     * @param array $deleted_roles
     * @return bool
     */
    public function update( string $app_slug, array $permissions, array $roles = [], array $deleted_roles = [] ): bool
    {
        $dt_custom_roles = array_map(function ( $custom_role ) use ( $permissions, $roles, $deleted_roles ) {
            if ( isset( $custom_role['slug'] ) ) {
                $custom_role_slug = $custom_role['slug'];

                // Update specified role permissions.
                if ( in_array( $custom_role_slug, $roles ) ) {
                    if ( !isset( $custom_role['capabilities'] ) ) {
                        $custom_role['capabilities'] = [];
                    }

                    foreach ( $permissions as $permission ) {
                        $custom_role['capabilities'][$permission] = true;
                    }
                }

                // Delete specified role permissions.
                if ( in_array( $custom_role_slug, $deleted_roles ) ) {
                    if ( isset( $custom_role['capabilities'] ) ) {
                        foreach ( $permissions as $permission ) {
                            $custom_role['capabilities'][$permission] = false;
                        }
                    }
                }
            }

            return $custom_role;
        }, get_option( self::OPTION_KEY_CUSTOM_ROLES, [] ));

        // Persist updated global custom roles.
        return update_option( self::OPTION_KEY_CUSTOM_ROLES, $dt_custom_roles );
    }

    /**
     * Determine if specified user has permission to access given app.
     *
     * @param array $app
     * @param int $user_id
     * @param array $dt_custom_roles
     * @return bool
     */
    public function has_permission( array $app, int $user_id = 0, array $dt_custom_roles = [] ): bool
    {

        // Default to true if roles & permissions enforcement is currently disabled.
        if ( !$this->is_enabled() ) {
            return true;
        }

        /**
         * Determine if user has a valid role for the specified app;
         * ensuring globally set $dt_custom_roles take priority.
         */

        $has_permission = false;

        // Capture user id to be validated against.
        if ( $user_id === 0 ) {
            $user_id = get_current_user_id();
        }

        // Determine permission to be validated against.
        $permission = $this->generate_permission_key( $app['slug'] );

        // Capture user's currently assigned roles and determine if they have relevant permission.
        $user = new WP_User( $user_id );
        if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
            $dt_custom_roles_checked = false;

            // Determine if any of user's current roles has been set within custom dt roles.
            foreach ( $user->roles as $role ) {
                if ( !$dt_custom_roles_checked && isset( $dt_custom_roles[$role]['capabilities'][$permission] ) ) {
                    $dt_custom_roles_checked = true;
                    $has_permission = $dt_custom_roles[$role]['capabilities'][$permission];
                }
            }

            // If custom roles were not checked, then attempt to validate against existing app settings.
            if ( !$dt_custom_roles_checked ) {
                if ( isset( $app['roles'] ) && is_array( $app['roles'] ) ) {
                    foreach ( $user->roles as $role ) {
                        if ( !$has_permission ) {
                            $has_permission = in_array( $role, $app['roles'] );
                        }
                    }
                }
            }
        }

        return $has_permission;
    }

    /**
     * Determine if specified user is allowed to access plugin.
     *
     * @param int $user_id
     * @return bool
     */
    public function can_access_plugin( int $user_id = 0 ): bool
    {

        // Default to true if roles & permissions enforcement is currently disabled.
        if ( !$this->is_enabled() ) {
            return true;
        }

        $can_access_plugin = false;

        // Capture user id to be validated against.
        if ( $user_id === 0 ) {
            $user_id = get_current_user_id();
        }

        /**
         * To avoid breaks in existing flows (i.e. logouts), ensure zero (0) user ids, are ignored
         * and true is returned
         */

        if ( $user_id === 0 ) {
            return true;
        }

        // Capture user's currently assigned roles and determine if they have relevant permission.
        $user = new WP_User( $user_id );
        if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
            $permission = 'can_access_home_screen';
            $dt_custom_roles = get_option( $this::OPTION_KEY_CUSTOM_ROLES, [] );

            // Determine if any of user's current roles match the can_access permission, with admin users being granted default access.
            foreach ( $user->roles as $role ) {
                if ( ( $role == 'administrator' ) || ( !$can_access_plugin && isset( $dt_custom_roles[$role]['capabilities'][$permission] ) && $dt_custom_roles[$role]['capabilities'][$permission] ) ) {
                    $can_access_plugin = true;
                }
            }
        }

        return $can_access_plugin;
    }
}
