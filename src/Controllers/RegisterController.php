<?php

namespace DT\Home\Controllers;

use DT\Home\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use function DT\Home\extract_request_input;
use function DT\Home\plugin_url;
use function DT\Home\redirect;
use function DT\Home\template;
use function DT\Home\route_url;

class RegisterController {

    /**
     * Show the register form.
     *
     * @param Request $request The HTTP request.
     *
     * @return ResponseInterface The rendered template.
     */
    public function show( Request $request ) {
        $params = $request->getQueryParams();
        $form_action = route_url( 'register' );
        $login_url   = route_url( 'login' );
        $error       = sanitize_text_field( $params['error'] ?? '' );
        $username    = sanitize_text_field( $params['username'] ?? '' );
        $email       = sanitize_email( $params['email'] ?? '' );
        $password    = $params['password'] ?? '';
        $logo_path   = plugin_url( 'resources/img/logo-color.png' );

        //@todo disable here?
        if ( ! get_option( 'users_can_register' ) ) {
            // return false;
        }

        return template( 'auth/register', [

            'form_action' => $form_action,
            'login_url'   => $login_url,
            'username'    => $username,
            'email'       => $email,
            'password'    => $password,
            'logo_path'   => $logo_path,
            'error'       => $error
        ] );
    }

    /**
     * Process the registration form.
     *
     * @param Request $request The HTTP request.
     *
     * @return ResponseInterface The response.
     */
	public function process( Request $request ) {
		// Check if registration is allowed
		if ( ! get_option( 'users_can_register' ) ) {
			return $this->show_error( __( 'Registration is not allowed.', 'dt-home' ) );
		}

		$input = extract_request_input( $request );

		$username         = sanitize_text_field( $input['username'] ?? '' );
		$email            = sanitize_email( $input['email'] ?? '' );
		$password         = $input['password'] ?? '';
		$confirm_password = $input['confirm_password'] ?? '';
        $old_input = [
            'username' => $username,
            'email'    => $email,
        ];

		if ( ! $username || ! $password || ! $email ) {
			return $this->show_error( __( 'Please fill out all fields.', 'dt-home' ), $old_input );
		}

		if ( $confirm_password !== $password ) {
            return $this->show_error( __( 'Passwords do not match.', 'dt-home' ), $old_input );
		}

		$user = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user ) ) {
			$error = $user->get_error_message();
            return $this->show_error( $error, $old_input );
		}

		$user_obj = get_user_by( 'id', $user );
		wp_set_current_user( $user );
		wp_set_auth_cookie( $user_obj->ID );

		if ( ! $user ) {
            return $this->show_error( __( 'An unexpected error has occurred.', 'dt-home' ), $old_input );
		}

		return redirect( route_url() );
	}

    /**
     * Show the register page with an error.
     *
     * @param string $error The error message to display.
     * @param array $params Additional parameters to include in the request.
     * @param string $method The HTTP method to use for the request. Default is "GET".
     * @param string $endpoint The endpoint for the request. If empty, it will default to the "register" route URL.
     * @param array $headers Additional headers to include in the request. Default is '[ "Content-Type" => "application/HTML" ]'.
     *
     * @return ResponseInterface The response from showing the error message.
     */
    private function show_error( $error, $params = [], $method = "GET", $endpoint = "", $headers = [
        'Content-Type' => 'application/HTML',
    ]): ResponseInterface {
        $params = array_merge( $params, [ 'error' => $error ] );
        if ( ! empty( $endpoint ) ) {
            $endpoint = route_url( 'register' );
        }
        return $this->show( ServerRequestFactory::request( $method, $endpoint, $params, $headers ) );
    }
}
