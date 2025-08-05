<?php
/**
 * Factory functions for tests
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
namespace Tests;

use Faker\Factory as Faker;

function app_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'key' => $faker->randomKey(),
        'name' => $faker->words( 3, true ),
        'type' => $faker->randomElement( [ 'Web View', 'Link' ] ),
        'creation_type' => 'custom',
        'icon' => $faker->imageUrl(),
        'url' => $faker->url,
        'sort' => $faker->numberBetween( 0, 50 ),
        'slug' => $faker->slug,
        'is_hidden' => $faker->boolean,
        'is_deleted' => false,
    ], $params );
}

function training_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'key' => $faker->randomKey(),
        'name' => $faker->words( 3, true ),
        'embed_video' => $faker->url,
        'anchor' => $faker->slug,
        'sort' => $faker->numberBetween( 0, 50 ),
    ], $params );
}

function wp_user_factory( $params = [] ) {
    $faker = Faker::create();

    return array_merge( [
        'user_login' => $faker->userName,
        'user_pass' => $faker->password,
        'user_nicename' => $faker->name,
        'user_email' => $faker->email,
        'user_url' => $faker->url,
        'user_registered' => $faker->dateTimeThisYear->format( 'Y-m-d H:i:s' ),
        'user_status' => 0,
        'display_name' => $faker->name,
        'user_activation_key' => ''
    ], $params );
}

function register_wp_user( $user = [] ) {
    if ( !isset( $user['user_login'], $user['user_pass'], $user['user_email'] ) ){
        return false;
    }

    $user_id = wp_create_user( $user['user_login'], $user['user_pass'], $user['user_email'] );
    update_option( 'dt_base_user', $user_id, false );
    wp_set_current_user( $user_id );

    return !is_null( wp_get_current_user() ) ? $user_id : false;
}

function wp_credentials_factory( $params = [] )
{
    $faker = Faker::create();

    return array_merge([
        'username' => $faker->userName,
        'password' => $faker->password,
        'email' => $faker->email,
    ], $params);
}

function registration_factory( $params = [] ) {
    $faker = Faker::create();

    $password = $faker->password;
    return array_merge( [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => $password,
        'confirm_password' => $password,
    ], $params );
}
