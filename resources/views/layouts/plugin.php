<?php

use function DT\Home\get_plugin_option;
use function DT\Home\magic_url;

$user = wp_get_current_user();
$home = DT\Home\route_url();
$dashboard = '/';
$menu_items = [];

// Adding default menu items
$menu_items[] = [ 'label' => __( 'Apps', 'dt-home' ), 'href' => $home ];
$menu_items[] = [ 'label' => __( 'Training', 'dt-home' ), 'href' => magic_url( 'training' ) ];

// Conditional invite option
if ( get_plugin_option( 'invite_others', true ) ) {
    $menu_items[] = [ 'label' => __( 'Invite', 'dt-home' ), 'href' => '#', 'action' => 'invite' ];
}

//if ( get_option( 'dt_home_require_login', true ) ) {
$menu_items[] = [ 'label' => __( 'Log Out', 'dt-home' ), 'href' => magic_url( 'logout' ) ];
//}


$menu_items_json = wp_json_encode( $menu_items );
$invite_translations = [
    'inviteTitle' => __( 'Invite', 'dt-home' ),
    'inviteExplanation' => __( 'Copy this link and share it with people you are coaching. They will create their own account and have their own Home Screen.', 'dt-home' ),
    'inviteCopyButton' => __( 'Copy', 'dt-home' ),
    'inviteCloseButton' => __( 'Close', 'dt-home' ),
    'inviteCopiedMessage' => __( 'Link copied!', 'dt-home' ),
];
$invite_translations_json = wp_json_encode( $invite_translations );
?>

<sp-theme
    color="light"
    scale="medium"
    id="main-theme"
>
    <div class="plugin cloak">
        <div class="plugin__main">
            <div class="container non-selectable">
                <dt-home-menu menuItems='<?php echo esc_js( $menu_items_json ); ?>' translations='<?php echo esc_js( $invite_translations_json ); ?>'></dt-home-menu>
                
                <?php
                // phpcs:ignore
                echo $this->section('content') ?>
            </div>
        </div>

        <div class="plugin__footer">
            <div class="container">
                <?php
                // phpcs:ignore
                echo $this->section('footer') ?>
            </div>
        </div>
    </div>
</sp-theme>
