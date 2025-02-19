<?php

namespace DT\Home\Controllers\MagicLink;

use Disciple_Tools_Users;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Analytics;
use function DT\Home\config;
use function DT\Home\redirect;
use function DT\Home\route_url;

class ShareController
{

    private Analytics $analytics;

    public function __construct( Analytics $analytics )
    {
        $this->analytics = $analytics;
    }

    /**
     * Show method.
     *
     * This method retrieves the current user ID and fetches the contact associated
     * with that user. If a contact is found, it sets a cookie with the contact information
     * that expires after 30 days. Finally, it redirects the user to the route URL.
     *
     * @return ResponseInterface The redirect response.
     */
    public function show()
    {
        $user_id = get_current_user_id();
        $contact = Disciple_Tools_Users::get_contact_for_user( $user_id );

        $this->set_cookie( $contact );
        $this->analytics->event( 'share-link', [ 'action' => 'snapshot', 'lib_name' => __CLASS__ ] );
        return redirect( route_url() );
    }

    /**
     * Set Cookie method.
     *
     * This method sets a cookie with the provided contact information. The cookie is set to expire
     * after 30 days. If the contact information is empty, the method will return without setting the cookie.
     *
     * @param mixed $contact The contact information to be set in the cookie.
     *
     * @return void
     */
    public function set_cookie( $contact ): void
    {
        if ( !$contact ) {
            return;
        }

        setcookie( config( 'plugin.share_cookie' ), $contact, time() + ( 86400 * 30 ), "/" );
    }
}
