<?php
/**
 * @var string $magic_link
 * @var string $data
 * @var string $app_url
 * @var string $subpage_url
 * @var WP_User $user
 * @var string $reset_apps
 */
$this->layout( 'layouts/plugin' );
?>

<header id="app-header" class="app-header">
    <h1 class="app-title">
        <?php echo esc_html__( 'Home Screen', 'dt-home' ); ?>
    </h1>
    <div class="header-controls">
        <dt-home-theme-toggle></dt-home-theme-toggle>
    </div>
</header>

<dt-home-app-grid id="appGrid" app-data='<?php echo esc_attr( htmlspecialchars( $data ) ); ?>'
                  app-url='<?php echo esc_url( $app_url ); ?>'>
    <!-- Add more app icons as needed -->
</dt-home-app-grid>

<div>
    <?php
    // phpcs:ignore
    echo $this->section( 'content' ) ?>
</div>

<?php $this->start( 'footer' ); ?>

<dt-home-footer id="hiddenApps"
                translations='<?php echo wp_json_encode( [
                    "hiddenAppsLabel" => __( "Hidden Apps", 'dt-home' ),
                    "buttonLabel"     => __( "Ok", 'dt-home' )
                ] ) ?>'
                hidden-data='<?php echo esc_attr( htmlspecialchars( $data ) ); ?>'
                app-url-unhide='<?php echo esc_url( $app_url ); ?>'
                reset-apps='<?php echo esc_attr( $reset_apps ); ?>'>
</dt-home-footer>

<?php $this->stop(); ?>

