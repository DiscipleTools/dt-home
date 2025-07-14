<?php
/**
 * @var array $app
 * @var string $url
 * @var array $apps_array
 */
$this->layout( 'layouts/web-view' );
?>


<iframe src="<?php echo esc_url( $url ); ?>" width="100%" height="650" frameborder="0"></iframe>

<?php
$this->insert( 'partials/launcher-bottom-nav', ['apps' => $apps_array] );
?>
