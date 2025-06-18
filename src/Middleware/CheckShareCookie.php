<?php

namespace DT\Home\Middleware;

use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Psr\Http\Message\ServerRequestInterface;
use DT\Home\Psr\Http\Server\MiddlewareInterface;
use DT\Home\Psr\Http\Server\RequestHandlerInterface;
use function DT\Home\config;

class CheckShareCookie implements MiddlewareInterface {

    /**
     * If the user is not logged in, the request handler is directly called and the response is returned.
     * If the 'dt_home_share' cookie exists, sanitize and assign its value to $leader_id, otherwise set $leader_id to null.
     * If $leader_id is not null, attempt to add the leader with the given ID.
     * If an exception occurs during adding the leader, remove the 'dt_home_share' cookie.
     * Finally, call the request handler and return the response.
     *
     * @param ServerRequestInterface $request The request object.
     * @param RequestHandlerInterface $handler The request handler object.
     *
     * @return ResponseInterface The response object.
     */
    public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        if ( ! is_user_logged_in() ) {
            return $handler->handle( $request );
        }

        if ( isset( $_COOKIE[ config( 'plugin.share_cookie' ) ] ) ) {
            $leader_id = sanitize_text_field( wp_unslash( $_COOKIE[ config( 'plugin.share_cookie' ) ] ) );
        } else {
            $leader_id = null;
        }

        if ( $leader_id ) {
            try {
                $this->add_leader( $leader_id );
            } catch ( \Exception $e ) {
                $this->remove_cookie();
            }
        }

        return $handler->handle( $request );
    }

	/**
	 * Add a leader to a contact's coached_by field and update assigned_to field.
	 *
	 * @param int $leader_id The ID of the leader to be added.
	 *
	 * @return void
	 */
	public function add_leader( $leader_id ) {
		if ( ! $leader_id ) {
			return;
		}

		$contact = \Disciple_Tools_Users::get_contact_for_user( get_current_user_id() );

		if ( $leader_id == $contact ) {
			$this->remove_cookie();

			return;
		}

		$contact_record = \DT_Posts::get_post( 'contacts', $contact, true, false );
		$leader         = \DT_Posts::get_post( 'contacts', $leader_id, true, false );

		if ( ! $contact_record || ! $leader ) {
			$this->remove_cookie();
		}

		if ( ! count( $contact_record['coached_by'] ?? [] ) ) {
			$fields = [
				"coached_by"  => [
					"values"       => [
						[ "value" => (string) $leader_id ],
					],
					"force_values" => false
				],
				'assigned_to' => (string) $leader['corresponds_to_user']
			];


			\DT_Posts::update_post( 'contacts', $contact, $fields, true, false );
		}

		$this->remove_cookie();
	}

	/**
	 * Removes the 'dt_home_share' cookie if it exists.
	 *
	 * @return void
	 */
	public function remove_cookie() {
		if ( isset( $_COOKIE[ config( 'plugin.share_cookie' ) ] ) ) {
			unset( $_COOKIE[ config( 'plugin.share_cookie' ) ] );
			setcookie( 'dt_home_share', '', time() - 3600, '/' );
		};
	}
}
