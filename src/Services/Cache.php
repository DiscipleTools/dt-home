<?php

namespace DT\Home\Services;

class Cache {

    public function __construct() {
    }

    public function delete( $keys = [] ): void {
        if ( wp_cache_supports( 'delete_multiple' ) && !empty( $keys ) ) {
            wp_cache_delete_multiple( $keys );
        }
    }
}
