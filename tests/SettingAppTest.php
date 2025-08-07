<?php


namespace Tests;

use DT\Home\Sources\SettingsApps;
use function DT\Home\container;

class SettingAppTest extends TestCase
{

    /**
     * @test
     */
    public function it_raw()
    {
        $user_apps_services = container()->get( SettingsApps::class );
        $apps = [ app_factory(), app_factory() ];

        $user_apps_services->save( $apps );
        $setting_apps = container()->get( SettingsApps::class )->raw();

        foreach ( $apps as $app ) {
            $this->assertNotNull( $user_apps_services->find( $app['slug'] ) );
        }

        $this->assertCount( 2, $setting_apps );
    }

    /**
     * @test
     */

    public function it_is_allowed()
    {
        $apps = app_factory( [ 'is_deleted' => true ] );
        $setting_apps = container()->get( SettingsApps::class )->is_allowed( $apps );

        $this->assertFalse( $setting_apps );
    }

    /**
     * @test
     */
    public function it_saves()
    {
        $apps = [ app_factory(), app_factory() ];
        $settings_apps_service = container()->get( SettingsApps::class );

        $settings_apps_service->save( $apps );
        $app_slugs = array_column( $apps, 'slug' );

        $get_apps = container()->get( SettingsApps::class )->raw();
        $get_app_slugs = array_column( $get_apps, 'slug' );

        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $get_app_slugs );
        }
        $setting_apps = $settings_apps_service->raw();

        $this->assertCount( 2, $setting_apps );
    }

    /**
     * @test
     */

    public function it_hides()
    {
        $app = app_factory( [ 'is_hidden' => true, 'slug' => 'test-app5', 'is_deleted' => false ] );

        $source = container()->get( SettingsApps::class );

        $source->save( [ $app ] );

        $source->hide( 'test-app5' );

        $fetched = $source->find_unfiltered( 'test-app5' );

        $this->assertTrue( $fetched['is_hidden'] );
    }

    /**
     * @test
     */
    public function it_unhides()
    {
        $app = app_factory( [ 'slug' => 'test-app1', 'is_hidden' => false, 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );

        $settings_apps_service->save( [ $app ] );
        $settings_apps_service->unhide( 'test-app1' );

        $unhidden_app = $settings_apps_service->find( 'test-app1' );

        $this->assertFalse( $unhidden_app['is_hidden'] );
    }
    /**
     * @test
     */
    /**
     * @test
     */
    public function it_deletes()
    {
        $app = app_factory( [ 'slug' => 'test-app2', 'is_deleted' => false ] );

        $settings_apps_service = container()->get( SettingsApps::class );

        $settings_apps_service->save( [ $app ] );

        $settings_apps_service->delete( 'test-app2', [ 'slug' => 'test-app2' ] );

        $deleted_app = $settings_apps_service->find_unfiltered( 'test-app2' );

        $this->assertTrue( $deleted_app['is_deleted'] );
    }

    /**
     * @test
     */
    public function it_undeletes()
    {

        $app = app_factory( [ 'slug' => 'test-app3', 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );

        $settings_apps_service->save( [ $app ] );
        $settings_apps_service->undeleted( [ 'slug' => 'test-app3' ] );

        $undeleted_app = $settings_apps_service->find( 'test-app3' );
        $this->assertFalse( $undeleted_app['is_deleted'] );
    }

    /**
     * @test
     */
    public function it_fetches_for_save()
    {
        $apps = app_factory( [ 'slug' => 'new-test-app' ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $apps ] );
        $result = $settings_apps_service->find_unfiltered( 'new-test-app', [ 'merged' => true ] );
        $this->assertNotNull( $result );
    }

    /**
     * @test
     */
    public function it_merges()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Existing apps
        $setting_apps_services = container()->get( SettingsApps::class );
        $existing_apps = $setting_apps_services->all();

        // New app to merge with a specific value
        $new_app = [ app_factory( [ 'slug' => 'new-app-slug' ] ) ];

        // Expected result after merge
        $expected_merged_apps = array_merge( $existing_apps, $new_app );

        // Perform the merge
        $new_setting_apps = container()->get( SettingsApps::class );
        $merged_apps = $new_setting_apps->merge( $existing_apps, $new_app );

        // Assert that the merged apps match the expected result
        $this->assertEquals( $expected_merged_apps, $merged_apps );

        // Assert that the new app's slug is present in the merged apps
        $merged_slugs = array_column( $merged_apps, 'slug' );

        $this->assertContains( 'new-app-slug', $merged_slugs );
    }

    /**
     * @test
     */
    public function it_retrieves_all_apps()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        $source = container()->get( SettingsApps::class );
        $source->save( $apps );
        $all_apps = $source->all( [ 'filter' => false ] );

        $app_slugs = array_column( $apps, 'slug' );
        $setting_app_slugs = array_column( $all_apps, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $setting_app_slugs );
        }


        // Assert that the all_apps array contains all the apps
        $this->assertCount( 2, $all_apps );
    }

    /**
     * @test
     */
    public function its_filter()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'is_deleted' => true ] )
        ];

        $settings_apps_service = container()->get( SettingsApps::class );
        $filtered_apps = $settings_apps_service->filter( $apps );

        $this->assertCount( 1, $filtered_apps );
        $this->assertEquals( 'app1', $filtered_apps[0]['slug'] );
    }

    /**
     * @test
     */
    public function it_allowed()
    {
        $app = app_factory( [ 'slug' => 'app-test' ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $allowed = $settings_apps_service->is_allowed( $app );

        $this->assertTrue( $allowed );
    }

    /**
     * @test
     */
    public function it_disallowed()
    {
        $app = app_factory( [ 'slug' => 'app-test', 'is_deleted' => true ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $disallowed = $settings_apps_service->disallowed( [ $app ] );

        $this->assertCount( 1, $disallowed );
        $this->assertEquals( 'app-test', $disallowed[0]['slug'] );
    }

    /**
     * @test
     */
    public function it_delete()
    {
        $app = app_factory( [ 'slug' => 'app1', 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $app ] );
        $settings_apps_service->delete( 'app1' );

        $deleted_app = $settings_apps_service->find_unfiltered( 'app1' );

        $this->assertTrue( $deleted_app['is_deleted'] );
    }

    /**
     * @test
     */
    public function it_destroy()
    {
        $app = app_factory( [ 'slug' => 'app-test', 'is_deleted' => true ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $app ] );
        $settings_apps_service->destroy( 'app-test' );

        $deleted_app = $settings_apps_service->find( 'app-test' );

        $this->assertNull( $deleted_app );
    }

    /**
     * @test
     */
    public function it_unfiltered()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_deleted' => false, 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'is_deleted' => false, 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app3', 'is_deleted' => false, 'sort' => 3 ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $unfiltered_apps = $settings_apps_service->unfiltered();

        $this->assertCount( 3, $unfiltered_apps );

        $this->assertEquals( 'app1', $unfiltered_apps[0]['slug'] );
        $this->assertEquals( 'app2', $unfiltered_apps[1]['slug'] );
        $this->assertEquals( 'app3', $unfiltered_apps[2]['slug'] );
    }

    /**
     * @test
     */
    public function it_find_unfiltered()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_deleted' => false, 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'is_deleted' => false, 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app3', 'is_deleted' => true, 'sort' => 3 ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $unfiltered_app = $settings_apps_service->find_unfiltered( 'app1' );

        $this->assertEquals( 'app1', $unfiltered_app['slug'] );
    }

    /**
     * @test
     */
    public function it_is_visible()
    {
        $app = app_factory( [ 'is_hidden' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $visible = $settings_apps_service->is_visible( $app );

        $this->assertTrue( $visible );
    }

    /**
     * @test
     */
    public function it_sort_key()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $sort_key = $settings_apps_service->sort_key();

        $this->assertEquals( 'sort', $sort_key );
    }

    /**
     * @test
     */
    public function it_find_key()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $find_key = $settings_apps_service->find_key();

        $this->assertEquals( 'slug', $find_key );
    }

    /**
     * @test
     */
    public function it_formatted()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $formatted_apps = $settings_apps_service->formatted();

        $this->assertCount( 3, $formatted_apps );

        foreach ( $formatted_apps as $app ) {
            $this->assertArrayHasKey( 'name', $app );
            $this->assertArrayHasKey( 'type', $app );
            $this->assertArrayHasKey( 'creation_type', $app );
            $this->assertArrayHasKey( 'source', $app );
            $this->assertArrayHasKey( 'icon', $app );
            $this->assertArrayHasKey( 'url', $app );
            $this->assertArrayHasKey( 'sort', $app );
            $this->assertArrayHasKey( 'slug', $app );
            $this->assertArrayHasKey( 'is_hidden', $app );
            $this->assertArrayHasKey( 'is_deleted', $app );


        }
    }

    /**
     * @test
     */
    public function it_sort()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 3, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 1, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $sorted_apps = $settings_apps_service->sort( $apps, [ 'key' => 'sort', 'asc' => true ] );

        $this->assertEquals( 'app3', $sorted_apps[0]['slug'] );
        $this->assertEquals( 'app2', $sorted_apps[1]['slug'] );
        $this->assertEquals( 'app1', $sorted_apps[2]['slug'] );

        // Test descending order
        $sorted_apps_desc = $settings_apps_service->sort( $apps, [ 'key' => 'sort', 'asc' => false ] );
        $this->assertEquals( 'app1', $sorted_apps_desc[0]['slug'] );
        $this->assertEquals( 'app2', $sorted_apps_desc[1]['slug'] );
        $this->assertEquals( 'app3', $sorted_apps_desc[2]['slug'] );

        // Test reset functionality
        $sorted_apps_reset = $settings_apps_service->sort( $apps, [ 'key' => 'sort', 'asc' => true, 'reset' => true ] );
        $this->assertEquals( 0, $sorted_apps_reset[0]['sort'] );
        $this->assertEquals( 1, $sorted_apps_reset[1]['sort'] );
        $this->assertEquals( 2, $sorted_apps_reset[2]['sort'] );
    }

    /**
     * @test
     */
    public function it_find()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $found_app = $settings_apps_service->find( 'app1' );

        $this->assertEquals( 'app1', $found_app['slug'] );
    }

    /**
     * @test
     */
    public function it_find_index()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $found_index = $settings_apps_service->find_index( 'app1' );

        $this->assertEquals( 0, $found_index );
    }

    /**
     * @test
     */
    public function it_set()
    {
        $app = app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $app ] );

        $result = $settings_apps_service->set( 'app1', 'sort', 2 );

        $updated_app = $settings_apps_service->find( 'app1' );

        $this->assertEquals( 2, $updated_app['sort'] );
        $this->assertEquals( 2, $result );
    }

    /**
     * @test
     */
    public function it_toggle()
    {
        $app = app_factory( [ 'slug' => 'app1', 'is_hidden' => false, 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $app ] );

        $settings_apps_service->toggle( 'app1', 'is_hidden' );

        $updated_app = $settings_apps_service->find( 'app1' );

        $this->assertTrue( $updated_app['is_hidden'] );

        // Toggle back to false
        $settings_apps_service->toggle( 'app1', 'is_hidden' );

        $updated_app = $settings_apps_service->find( 'app1' );

        $this->assertFalse( $updated_app['is_hidden'] );
    }

    /**
     * @test
     */
    public function it_value()
    {
        $app = app_factory( [ 'slug' => 'app1', 'is_hidden' => false, 'is_deleted' => false ] );
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( [ $app ] );

        $result = $settings_apps_service->value( 'app1', 'is_hidden' );

        $this->assertFalse( $result );

        // Additional test cases
        $result = $settings_apps_service->value( 'app1', 'non_existent_key' );
        $this->assertNull( $result );

        $result = $settings_apps_service->value( 'non_existent_slug', 'is_hidden' );
        $this->assertNull( $result );
    }

    /**
     * @test
     */
    public function it_first()
    {
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2, 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3, 'is_deleted' => false ] )
        ];
        $settings_apps_service = container()->get( SettingsApps::class );
        $settings_apps_service->save( $apps );
        $first_app = $settings_apps_service->first( $apps );

        $this->assertEquals( 'app1', $first_app['slug'] );

        // Test with an empty array
        $first_app_empty = $settings_apps_service->first( [] );
        $this->assertNull( $first_app_empty );
    }
}
