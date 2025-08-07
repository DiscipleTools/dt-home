<?php

/**
 * @var $config DT\Plugin\CodeZone\WPSupport\Config\ConfigInterface
 */

use function DT\Home\plugin_path;

$config->merge([
    'assets' => [
        'allowed_styles' => [
            'material-font-icons',
            'material-font-icons-local',
            'dt-home',
        ],
        'allowed_scripts' => [
            'dt-home',
        ],
        'javascript_global_scope' => '$home',
        'javascript_globals' => [
            'translations' => function () {
                return [
                    'installAppLabel' => __( 'Install as App', 'dt-home' ),
                    'hiddenAppLabel' => __( 'Hidden Apps', 'dt-home' ),
                    'buttonLabel' => __( 'Ok', 'dt-home' ),
                    'remove_app_confirmation' => __( 'Are you sure you want to remove this app?', 'dt-home' ),
                    'reset_app_confirmation' => __( 'Are you sure you want to reset all apps?', 'dt-home' ),
                    'no_hidden_apps' => __( 'No hidden apps available', 'dt-home' ),
                    'custom_app_label' => __( 'Custom App', 'dt-home' ),
                    'add_custom_app_label' => __( 'Add Custom App', 'dt-home' ),
                    'edit_custom_app_label' => __( 'Edit Custom App', 'dt-home' ),
                    'reset_apps_label' => __( 'Reset Apps', 'dt-home' ),
                    'name_label' => __( 'Name', 'dt-home' ),
                    'open_new_tab_label' => __( 'Open NewTab', 'dt-home' ),
                    'url_label' => __( 'URL', 'dt-home' ),
                    'icon_label' => __( 'Icon', 'dt-home' ),
                    'type_label' => __( 'Type', 'dt-home' ),
                    'slug_label' => __( 'Slug', 'dt-home' ),
                    'submit_label' => __( 'Save', 'dt-home' ),
                    'close_label' => __( 'Close', 'dt-home' ),
                    'remove_menu_label' => __( 'Remove', 'dt-home' ),
                    'edit_menu_label' => __( 'Edit', 'dt-home' ),
                    'select_type_label' => __( 'Select Type', 'dt-home' ),
                ];
            }
        ],
        'manifest_dir' => plugin_path( '/dist' )
    ]
]);
