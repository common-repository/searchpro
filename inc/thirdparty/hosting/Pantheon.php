<?php
if (!defined('ABSPATH')) exit;

class Pantheon extends berqIntegrations {
    public $page_url = null;

    function __construct() {
        add_action('berqwp_flush_all_cache', [$this, 'flush_cache']);
        add_action('berqwp_stored_page_cache', [$this, 'flush_page_cache']);
        add_action('berqwp_flush_page_cache', [$this, 'flush_page_cache']);
    }

    function flush_cache() {
        if (class_exists('Pantheon_Cache')) {
            $Pantheon_Cache = new Pantheon_Cache();
            $Pantheon_Cache->flush_site();
        }
    }

    function flush_page_cache($slug) {

        if (!class_exists('Pantheon_Cache')) {
            return;
        }

        if (empty($slug)) {
            $slug = '/';
        }

        $this->page_url = home_url() . $slug;

        add_filter('pantheon_final_clean_urls', [$this, 'add_page_flush_queue']);
    }

    function add_page_flush_queue($paths) {
        $paths[] = $this->page_url;

        return $paths;
    }
}

new Pantheon();