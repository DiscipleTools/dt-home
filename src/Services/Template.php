<?php

namespace DT\Home\Services;

use DT\Home\CodeZone\WPSupport\Router\ResponseRendererInterface;
use DT\Home\Psr\Http\Message\ResponseInterface;
use function DT\Home\namespace_string;
use function DT\Home\request;

/**
 * Class Template
 *
 * This class represents a template in a web application. It is responsible for rendering the template and managing assets.
 */
class Template implements ResponseRendererInterface
{

	/**
	 * @var Assets
	 */
	protected $assets;

	public function __construct( Assets $assets )
	{
		$this->assets = $assets;
	}

	/**
	 * Registers actions and filters for the Blank class.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'dt_override_header_meta', '__return_true' );
		add_filter( 'dt_blank_access', [ $this, 'blank_access' ] );
		add_action( 'dt_blank_head', [ $this, 'header' ] );
		add_action( 'dt_blank_footer', [ $this, 'footer' ] );
		add_filter( namespace_string( 'response_renderer' ), function () {
			return $this;
		} );
		$this->assets->enqueue();
	}

	/**
	 * Allow access to blank template
	 * @return bool
	 */
	public function blank_access(): bool {
		return true;
	}

	/**
	 * Render the header
	 */
	public function header() {
		$path = request()->getUri()->getPath();
		if ( preg_match( '#^/apps/launcher/([^/]+)#', $path, $matches ) ) {
			$key              = $matches[1];
			$manifest_url     = '/apps/launcher/' . $key . '/manifest.json';
			$app_title        = 'D.T Home';
			$theme_color      = '#3f729b';
			$background_color = '#ffffff';
			$favicon_path     = get_template_directory_uri() . '/dt-assets/favicons';

			// Manifest
			echo '<link rel="manifest" href="' . esc_url( $manifest_url ) . '">';

			// Apple PWA
			echo '<meta name="apple-mobile-web-app-capable" content="yes">';
			echo '<meta name="mobile-web-app-capable" content="yes">';
			echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
			echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( $app_title ) . '">';
			echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $favicon_path ) . '/apple-touch-icon.png">';

			// Standard Favicons
			echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $favicon_path ) . '/favicon-32x32.png">';
			echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( $favicon_path ) . '/favicon-16x16.png">';
			echo '<link rel="shortcut icon" href="' . esc_url( $favicon_path ) . '/favicon.ico">';

			// Microsoft
			echo '<meta name="msapplication-TileColor" content="' . esc_attr( $theme_color ) . '">';
			echo '<meta name="msapplication-TileImage" content="' . esc_url( $favicon_path ) . '/mstile-144x144.png">';
			echo '<meta name="msapplication-config" content="' . esc_url( $favicon_path ) . '/browserconfig.xml">';

			// Theme
			echo '<meta name="theme-color" content="' . esc_attr( $theme_color ) . '">';
			echo '<meta name="background-color" content="' . esc_attr( $background_color ) . '">';
		}
		wp_head();
	}

	/**
	 * Renders a template and stops the script execution.
	 *
	 * @param ResponseInterface $response The response object to render.
	 *
	 * @return void
	 */
	public function render( ResponseInterface $response ) {
		add_action( 'dt_blank_body', function () use ( $response ) {
			// phpcs:ignore
			echo $response->getBody();
		}, 11 );

		$path = get_theme_file_path( 'template-blank.php' );
		include $path;

		die();
	}

	/**
	 * Render the footer
	 * @return void
	 */
	public function footer() {
		wp_footer();
	}
}
