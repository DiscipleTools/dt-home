<?php

$this->layout( 'layouts/plugin' );
/**
 * @var string $data
 */
?>

<header id="app-header" class="app-header">
    <h1 class="app-title">
        <?php echo esc_html__( 'Training', 'dt-home' ); ?>
    </h1>
    <div class="header-controls">
        <dt-home-theme-toggle></dt-home-theme-toggle>
    </div>
</header>

<dt-tile>
    <dt-home-video-list training-data='<?php echo htmlspecialchars( $data ); // phpcs:ignore ?>'></dt-home-video-list>
</dt-tile>

<?php
$this->insert( 'partials/return-to-launcher-button' );
?>

