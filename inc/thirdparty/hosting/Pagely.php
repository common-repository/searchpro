<?php 
if (!defined('ABSPATH')) exit;

class Pagely extends berqIntegrations {
    function __construct() {
        add_action('berqwp_stored_page_cache', [$this, 'flush_page_cache']);
        add_action('berqwp_flush_page_cache', [$this, 'flush_page_cache']);
        add_action('berqwp_flush_all_cache', [$this, 'flush_all_cache']);
    }

    function flush_all_cache() {
        if (class_exists('PagelyCachePurge')) {
            $purger = new PagelyCachePurge();
            $purger->purgeAll();
        }
    }

    function flush_page_cache($slug) {

        if (empty($slug)) {
            $slug = '/';
        }

        if (class_exists('PagelyCachePurge')) {
            $purger = new PagelyCachePurge();
            $purger->purgePath($slug);
        }
    }

}

new Pagely();