<?php

if (!defined('ABSPATH')) exit;

use BerqWP\BerqWP;

class berqElementor extends berqIntegrations {
    
    function __construct() {
        // add_action( 'elementor/editor/after_save', [$this, 'flush_page_cache'] );
    }

    function flush_page_cache($post_id, $editor_data) {

        $post_url = get_permalink( $post_id );
        
        // Parse the URL to get the path
        $parsed_url = wp_parse_url( $post_url );
        $post_path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

        berqCache::purge_page($post_path, true);

    }


}

new berqElementor();