<?php

namespace Tests;

use DT\Home\Services\RolesPermissions;
use function DT\Home\container;

class RolesPermissionsTest extends TestCase {

    /**
     * @test
     */
    public function can_disable(): void {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( false );

        $this->assertFalse( $roles_permissions_srv->is_enabled() );
    }

    /**
     * @test
     */
    public function can_enable(): void {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( true );

        $this->assertTrue( $roles_permissions_srv->is_enabled() );
    }

    /**
     * @test
     */
    public function can_merge_default_capabilities(): void {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( true );

        $this->assertContains( 'can_access_home_screen', array_keys( $roles_permissions_srv->dt_capabilities( [] ) ) );
    }

    /**
     * @test
     */
    public function can_generate_permission_key(): void {
        $app = app_factory();
        $slug = $app['slug'] ?? '';
        $can_access_permission_key = container()->get( RolesPermissions::class )->generate_permission_key( $slug );

        $this->assertTrue( $can_access_permission_key === ( 'can_access_'. $slug .'_app' ) );
    }

    /**
     * @test
     */
    public function can_update_global_custom_roles(): void {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( true );

        $app = app_factory();
        $slug = $app['slug'] ?? '';
        $can_access_permission_key = $roles_permissions_srv->generate_permission_key( $slug );

        // Set initial capability placeholders.
        update_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [
            'multiplier' => [
                'slug' => 'multiplier',
                'capabilities' => []
            ]
        ] );

        // Assert update operation is successful.
        $this->assertTrue( $roles_permissions_srv->update( $slug, [ $can_access_permission_key ], [
            'multiplier'
        ] ) );

        // Source placeholder custom roles and confirm expected permission is present.....
        $custom_roles = get_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [] );
        $this->assertTrue( in_array( $can_access_permission_key, array_keys( $custom_roles['multiplier']['capabilities'] ) ) );

        //.....and enabled..!
        $this->assertTrue( $custom_roles['multiplier']['capabilities'][$can_access_permission_key] );
    }

    /**
     * @test
     * @dataProvider user_app_access_data
     */
    public function user_app_access( $permission_enabled, $expected_result ) {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( true );

        // Register a test user.
        $user = wp_user_factory();
        $user_id = register_wp_user( $user );

        // Generate sample app settings.
        $app = app_factory();
        $slug = $app['slug'] ?? '';
        $can_access_permission_key = $roles_permissions_srv->generate_permission_key( $slug );

        // Set initial capability placeholders.
        update_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [
            'multiplier' => [
                'slug' => 'multiplier',
                'capabilities' => [
                    $can_access_permission_key => $permission_enabled
                ]
            ]
        ] );

        // Assert expected result holds true.
        $this->assertTrue( $expected_result === $roles_permissions_srv->has_permission( $app, $user_id, get_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [] ) ) );
    }

    public function user_app_access_data() {
        return [
            [ true, true ],
            [ false, false ]
        ];
    }

    /**
     * @test
     * @dataProvider user_plugin_access_data
     */
    public function user_plugin_access( $permission_enabled, $expected_result ) {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( true );

        // Register a test user.
        $user_id = register_wp_user( wp_user_factory() );

        // Set initial capability placeholders.
        update_option( $roles_permissions_srv::OPTION_KEY_CUSTOM_ROLES, [
            'multiplier' => [
                'slug' => 'multiplier',
                'capabilities' => [
                    'can_access_home_screen' => $permission_enabled
                ]
            ]
        ] );

        $this->assertTrue( $expected_result === $roles_permissions_srv->can_access_plugin( $user_id ) );
    }

    public function user_plugin_access_data() {
        return [
            [ true, true ],
            [ false, false ]
        ];
    }

    /**
     * @test
     */
    public function user_can_access_plugin_when_restriction_disabled(): void {
        $roles_permissions_srv = container()->get( RolesPermissions::class );
        $roles_permissions_srv->enabled( false );

        $this->assertTrue( $roles_permissions_srv->can_access_plugin() );
    }
}
