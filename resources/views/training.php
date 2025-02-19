<?php

$this->layout( 'layouts/plugin' );
/**
 * @var string $data
 */
?>
<h1 class="training"> <?php esc_html_e( 'Training', 'dt-home' ); ?></h1>
<dt-tile>
    <dt-home-video-list training-data='<?php echo htmlspecialchars( $data ); // phpcs:ignore ?>'></dt-home-video-list>
</dt-tile>

<?php
$this->insert( 'partials/return-to-launcher-button' );
?>

