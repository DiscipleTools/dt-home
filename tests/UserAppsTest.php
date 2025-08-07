<?php

namespace Tests;

use DT\Home\Services\Apps;
use DT\Home\Sources\SettingsApps;
use DT\Home\Sources\UserApps;
use function DT\Home\container;

class UserAppsTest extends TestCase
{
    /**
     * @test
     */
    public function it_retrieves_raw_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $user_apps_services = container()->get( UserApps::class );
        $apps = [ app_factory(), app_factory() ];
        $user_apps_services->save_for( $user_id, $apps );

        $user_apps = container()->get( UserApps::class )->raw( [ 'user_id' => $user_id ] );

        // Extract slugs from both arrays
        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $user_apps, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }

        // Verify the count of retrieved user apps
        $this->assertCount( 2, $user_apps );
    }

    /**
     * @test
     */
    public function it_retrieves_all_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $user_apps_services = container()->get( UserApps::class );
        $apps = [ app_factory(), app_factory() ];
        $user_apps_services->save_for( $user_id, $apps );

        $user_apps = container()->get( UserApps::class )->for( $user_id );

        // Extract slugs from both arrays
        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $user_apps, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }

        // Verify the count of retrieved user apps
        $this->assertCount( 2, $user_apps );
    }

    /**
     * @test
     */
    public function it_saves_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [ app_factory(), app_factory() ];
        $result = container()->get( UserApps::class )->save_for( $user_id, $apps );
        $get_apps = container()->get( UserApps::class )->for( $user_id );

        // Extract slugs from both arrays
        $app_slugs = array_column( $apps, 'slug' );
        $get_app_slugs = array_column( $get_apps, 'slug' );

        // Verify each slug is present in the retrieved apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $get_app_slugs );
        }
        // Verify the saving operation was successful
        $this->assertTrue( $result );
    }

    /**
     * @test
     */
    public function it_saves_user_apps_with_default_user_id()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [ app_factory(), app_factory() ];
        $result = container()->get( UserApps::class )->save( $apps );
        $get_apps = container()->get( UserApps::class )->for( $user_id );

        // Extract slugs from both arrays
        $app_slugs = array_column( $apps, 'slug' );
        $get_app_slugs = array_column( $get_apps, 'slug' );

        // Verify each slug is present in the retrieved apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $get_app_slugs );
        }
        // Verify the saving operation was successful
        $this->assertTrue( $result );
    }

    /**
     * @test
     */
    public function it_merge_user_apps()
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
        $user_apps_services = container()->get( UserApps::class );
        $merged_apps = $user_apps_services->merge( $existing_apps, $new_app );

        // Assert that the merged apps match the expected result
        $this->assertEquals( $expected_merged_apps, $merged_apps );

        // Assert that the new app's slug is present in the merged apps
        $merged_slugs = array_column( $merged_apps, 'slug' );

        $this->assertContains( 'new-app-slug', $merged_slugs );
    }


    /**
     * @test
     */
    public function it_hides_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Get the first app
        $apps_service = container()->get( Apps::class );
        $apps = $apps_service->for( $user_id );

        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );

        // Hide the app
        $result = $source->hide( $apps[0]['slug'] );

        // Assert app has been hidden
		$this->assertTrue( $result['is_hidden'] );
    }

    /**
     * @test
     */
    public function it_unhide_user_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Get the first app
        $apps_service = container()->get( Apps::class );
        $apps = $apps_service->for( $user_id );
        $data = $apps[0];

        // Hide the app
        $source      = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );
        $hidden_item = $source->hide( $data['slug'] );

        // Unhide the app
        $unhidden_item = $source->unhide( $data['slug'] );

        // Assert that the item is unhidden
        $this->assertFalse( $unhidden_item['is_hidden'] );

        // Assert that the item is found
        $found_item = $source->find( $data['slug'] );
        $this->assertEquals( $unhidden_item, $found_item );
    }

    /**
     * @test
     */
    public function it_checks_if_app_is_visible()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Get the first app
        $apps_service = container()->get( Apps::class );
        $apps = $apps_service->for( $user_id );
        $data = $apps[0];

        // Hide the app
        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );
        $hidden_item = $source->hide( $data['slug'] );

        // Assert that the item is hidden
        $this->assertTrue( $hidden_item['is_hidden'] );

        // Assert that the item is hidden
        $this->assertTrue( $source->is_visible( $data ) );
    }

    /**
     * @test
     */
    public function it_checks_if_app_is_hidden()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_hidden' => false ] ),
            app_factory( [ 'slug' => 'app2', 'is_hidden' => true ] ),
            app_factory( [ 'slug' => 'app3', 'is_hidden' => false ] ),
            app_factory( [ 'slug' => 'app4', 'is_hidden' => true ] )
        ];

        $source = container()->get( UserApps::class );

        // Save the apps
        $source->save_for( $user_id, $apps );

        // Retrieve all apps
        $all_apps = $source->for( $user_id );

        // Extract slugs from both arrays
        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $all_apps, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }

        // Filter hidden apps
        $hidden_apps = $source->hidden( $all_apps );

        // Assert that only hidden apps are in the hidden_apps array
        $this->assertCount( 2, $hidden_apps );

        // Assert that all hidden apps are hidden
        foreach ( $hidden_apps as $app ) {
            $this->assertTrue( $app['is_hidden'] );
        }
    }

    /**
     * @test
     */
    public function it_retrieves_all_deleted_apps()

    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'is_deleted' => true ] ),
            app_factory( [ 'slug' => 'app3', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app4', 'is_deleted' => true ] )
        ];

        $source = container()->get( UserApps::class );

        // Save the apps
        $source->save_for( $user_id, $apps );

        // Retrieve all apps
        $all_apps = $source->for( $user_id );

        // Filter deleted apps
        $deleted_apps = $source->deleted( $all_apps );

        // Assert that only hidden apps are in the deletedApps array
        $this->assertCount( 2, $deleted_apps );

        // Assert that all deleted apps are deleted
        foreach ( $deleted_apps as $app ) {
            $this->assertTrue( $app['is_deleted'] );
        }
    }

    /**
     * @test
     */
    public function it_retrieves_all_undeleted_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app2', 'is_deleted' => true ] ),
            app_factory( [ 'slug' => 'app3', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'app4', 'is_deleted' => true ] ),
            app_factory( [ 'slug' => 'app5', 'is_deleted' => false ] ),
        ];

        $source = container()->get( UserApps::class );

        // Save the apps
        $source->save_for( $user_id, $apps );
        // Retrieve all apps
        $all_apps = $source->for( $user_id );
        // Filter undeleted apps
        $undelted_apps = $source->undeleted( $all_apps );
        // Assert that only hidden apps are in the undeletedApps array
        $this->assertCount( 3, $undelted_apps );
        // Assert that all undeleted apps are undeleted
        foreach ( $undelted_apps as $app ) {
            $this->assertFalse( $app['is_deleted'] );
        }
    }

    /**
     * @test
     */
    public function it_soft_deletes_an_item()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'test-app1', 'is_deleted' => false ] ),
            app_factory( [ 'slug' => 'test-app2', 'is_deleted' => false ] ),
        ];

        $source = container()->get( UserApps::class );
        // Save the apps
        $source->save_for( $user_id, $apps );

        // Soft delete an item
        $slug = 'test-app1';
        $soft_delete_app = $source->delete( $slug );
        $this->assertTrue( $soft_delete_app );

        // Retrieve all apps
        $all_apps = $source->for( $user_id );

        // Filter deleted apps
        $deleted_apps = $source->deleted( $all_apps );

        // Assert that only hidden apps are in the deleted_apps array
        $this->assertCount( 1, $deleted_apps );

        $found_item = $source->find( 'test-app1' );
        $this->assertTrue( $found_item['is_deleted'] );
    }

    /**
     * @test
     */
    public function it_destroy_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] ),
        ];

        $source = container()->get( UserApps::class );
        // Save the apps
        $source->save_for( $user_id, $apps );

        // Soft delete an item
        $slug = 'app1';
        $destroy_app = $source->destroy( $slug );

        $this->assertTrue( $destroy_app );

        // Retrieve all apps
        $all_apps = $source->for( $user_id );

        // Filter deleted apps
        $deleted_apps = $source->deleted( $all_apps );

        // Assert that only hidden apps are in the deleted_apps array
        $this->assertCount( 0, $deleted_apps );

        $found_item = $source->find( 'app1' );
        $this->assertNotContains( $found_item, $all_apps );
    }

    /**
     * @test
     */
    public function it_finds_an_item()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        // Create apps with different visibility statuses
        $apps = [
            app_factory( [ 'slug' => 'app1', 'source' => 'user' ] ),
            app_factory( [ 'slug' => 'app2', 'source' => 'user' ] ),
        ];

        $source = container()->get( UserApps::class );
        // Save the apps
        $source->save_for( $user_id, $apps );

        $found_item = $source->find( 'app1' );

        $this->assertEquals( $found_item, $apps[0] );
    }

    /**
     * @test
     */
    public function it_allowed_an_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] ),
        ];

        $source = container()->get( UserApps::class );
        $allowed = $source->is_allowed( $apps );

        $this->assertTrue( $allowed );
    }

    /**
     * @test
     */
    public function it_disallowed_an_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        $source = container()->get( UserApps::class );
        $disallowed = $source->disallowed( $apps );

        // Assert that the disallowed array contains only apps that are not allowed
        $this->assertCount( 0, $disallowed );
    }

    /**
     * @test
     */
    public function it_filter_an_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        $source = container()->get( UserApps::class );
        $filtered = $source->filter( $apps );

        // Assert that the filtered array contains only the app that matches the filter
        $this->assertEquals( 'app1', $filtered[0]['slug'] );
    }

    /**
     * @test
     */
    public function it_retrieves_all_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
            app_factory( [ 'slug' => 'app2' ] )
        ];

        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );
        $all_apps = $source->all( [ 'filter' => false ] );

        $app_slugs = array_column( $apps, 'slug' );
        $user_app_slugs = array_column( $all_apps, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $user_app_slugs );
        }
        // Assert that the all_apps array contains all the apps
        $this->assertCount( 2, $all_apps );
    }

    /**
     * @test
     */
    public function it_fetch_for_save_an_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] )
        ];

        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );
        $fetch_for_save = $source->fetch_for_save();

        $app_slugs = array_column( $apps, 'slug' );
        $fetch_for_save_slugs = array_column( $fetch_for_save, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $fetch_for_save_slugs );
        }
    }

    /**
     * @test
     */
    public function it_merged_for_all_apps()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1' ] ),
        ];

        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );

        $merged = $source->merged();

        $app_slugs = array_column( $apps, 'slug' );
        $merged_slugs = array_column( $merged, 'slug' );

        // Verify each slug is present in the retrieved user apps
        foreach ( $app_slugs as $slug ) {
            $this->assertContains( $slug, $merged_slugs );
        }
    }

    /**
     * @test
     */
    public function it_find_index_of_an_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2 ] )
        ];

        $source = container()->get( UserApps::class );
        $source->save_for( $user_id, $apps );

        $index = $source->find_index( 'app1' );
        $this->assertEquals( 0, $index );

        $index = $source->find_index( 'app2' );
        $this->assertEquals( 1, $index );
    }

    /**
     * @test
     */
    public function it_sorts_an_apps_array()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app3', 'sort' => 3 ] )
        ];

        $source->save_for( $user_id, $apps );

        $items = $source->for( $user_id );

        // Test sorting in ascending order
        $sorted_apps = $source->sort( $items, [ 'key' => 'sort', 'asc' => true ] );
        $this->assertEquals( 'app2', $sorted_apps[0]['slug'] );
        $this->assertEquals( 'app1', $sorted_apps[1]['slug'] );
        $this->assertEquals( 'app3', $sorted_apps[2]['slug'] );

        // Test sorting in descending order
        $sorted_apps = $source->sort( $items, [ 'key' => 'sort', 'asc' => false ] );
        $this->assertEquals( 'app3', $sorted_apps[0]['slug'] );
        $this->assertEquals( 'app1', $sorted_apps[1]['slug'] );
        $this->assertEquals( 'app2', $sorted_apps[2]['slug'] );

        // Test sorting with reset
        $sorted_apps = $source->sort( $items, [ 'key' => 'sort', 'asc' => true, 'reset' => true ] );
        $this->assertEquals( 0, $sorted_apps[0]['sort'] );
        $this->assertEquals( 1, $sorted_apps[1]['sort'] );
        $this->assertEquals( 2, $sorted_apps[2]['sort'] );

        // Test sorting with missing key
        $items_with_missing_key = [
            app_factory( [ 'slug' => 'app1' ] ), // 'sort' key is omitted
            app_factory( [ 'slug' => 'app2' ] )  // 'sort' key is omitted
        ];
        // Manually remove the 'sort' key
        foreach ( $items_with_missing_key as &$item ) {
            unset( $item['sort'] );
        }
        unset( $item ); // Break the reference with the last element

        $sorted_apps = $source->sort( $items_with_missing_key, [ 'key' => 'sort', 'asc' => true ] );

        $this->assertEquals( $items_with_missing_key, $sorted_apps );
    }

    /**
     * @test
     */
    public function it_sets_an_apps_values()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        // Create apps
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 1 ] )
        ];

        // Save the apps
        $source->save_for( $user_id, $apps );

        // Set a new value for an existing key
        $new_sort_value = 10;
        $result = $source->set( 'app1', 'sort', $new_sort_value );

        // Verify the value is updated correctly
        $this->assertEquals( $new_sort_value, $result );

        // Verify the value in the item
        $item = $source->find( 'app1' );
        $this->assertEquals( $new_sort_value, $item['sort'] );

        // Test setting a value for a non-existent key
        $non_existent_key = $source->set( 'non-existent-app', 'sort', 5 );
        $this->assertFalse( $non_existent_key );
    }

    /**
     * @test
     */
    public function it_toggles_an_apps_value()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        // Create apps
        $apps = [
            app_factory( [ 'slug' => 'app1', 'is_hidden' => false ] ),
            app_factory( [ 'slug' => 'app2', 'is_hidden' => true ] )
        ];

        // Save the apps
        $source->save_for( $user_id, $apps );

        // Toggle the 'is_hidden' value for 'app1'
        $toggled_value = $source->toggle( 'app1', 'is_hidden' );

        // Verify the value is toggled correctly
        $this->assertTrue( $toggled_value );

        // Verify the value in the item
        $item = $source->find( 'app1' );
        $this->assertTrue( $item['is_hidden'] );

        // Toggle the 'is_hidden' value for 'app2'
        $toggled_value = $source->toggle( 'app2', 'is_hidden' );

        // Verify the value is toggled correctly
        $this->assertFalse( $toggled_value );

        // Verify the value in the item
        $item = $source->find( 'app2' );
        $this->assertFalse( $item['is_hidden'] );
    }

    /**
     * @test
     */
    public function it_retrieves_apps_value()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        // Create apps
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 1 ] )
        ];

        // Save the apps
        $source->save_for( $user_id, $apps );

        // Retrieve the value for an existing key
        $sort_value = $source->value( 'app1', 'sort' );
        $this->assertEquals( 2, $sort_value );

        // Retrieve the value for a non-existent key
        $non_existent_value = $source->value( 'app1', 'non_existent_key' );
        $this->assertNull( $non_existent_value );

        // Retrieve the value for a non-existent item
        $non_existent_item_value = $source->value( 'non_existent_app', 'sort' );
        $this->assertNull( $non_existent_item_value );
    }

    /**
     * @test
     */
    public function it_retrieves_the_first_app()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        // Test with a non-empty array
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 2 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 1 ] )
        ];
        // Save the apps
        $source->save_for( $user_id, $apps );
        // Retrieve the first item
        $first_item = $source->first( $apps );
        // Verify the first item is correct
        $this->assertEquals( $apps[0], $first_item );

        // Test with an empty array
        $items = [];
        $first_item = $source->first( $items );
        $this->assertNull( $first_item );
    }


    /**
     * @test
     */
    public function it_updates_an_apps_value()
    {
        $user_id = 1;
        $this->acting_as( $user_id );

        $source = container()->get( UserApps::class );

        // Create and save initial apps
        $apps = [
            app_factory( [ 'slug' => 'app1', 'sort' => 1 ] ),
            app_factory( [ 'slug' => 'app2', 'sort' => 2 ] )
        ];

        $source->save_for( $user_id, $apps );

        // Verify the initial state of the apps
        $saved_apps = $source->for( $user_id );

        $this->assertCount( 2, $saved_apps );
        $this->assertEquals( 'app1', $saved_apps[0]['slug'] );
        $this->assertEquals( 'app2', $saved_apps[1]['slug'] );

        // Update an existing item
        $updated_item = app_factory( [ 'slug' => 'app1', 'sort' => 3, 'source' => 'user' ] );
        $result       = $source->update( 'app1', $updated_item );

        // Verify the update result
        $this->assertTrue( $result );

        // Verify the item is updated
        $item = $source->find( 'app1' );

        $this->assertEquals( $updated_item, $item );

        // Attempt to update a non-existent item
        $non_existent_update = $source->update( 'non_existent_app', $updated_item );
        $this->assertFalse( $non_existent_update );
    }
}
