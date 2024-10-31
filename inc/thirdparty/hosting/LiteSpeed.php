<?php
if (!defined('ABSPATH')) exit;

class LiteSpeed extends berqIntegrations {
    function __construct() {
        add_action('berqwp_flush_all_cache', [$this, 'flush_cache']);
        add_action('berqwp_stored_page_cache', [$this, 'flush_page_cache']);
        add_action('berqwp_flush_page_cache', [$this, 'flush_page_cache']);
    }

    function flush_page_cache($slug) {

        if (empty($slug)) {
            $slug = '/';
        }
        
        if (!$this->is_litespeed()) {
            return;
        }

        @header('X-LiteSpeed-Purge: '.$slug);

        $page_url = home_url() . $slug;

        do_action( 'litespeed_purge_url', $page_url );
    }
    

    function flush_cache() {

        if (!$this->is_litespeed()) {
            return;
        }

        @header('X-LiteSpeed-Purge: *', true);

        do_action( 'litespeed_purge_all' );
    }

    function is_litespeed() {
        return isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false;
    }    

}

new LiteSpeed();