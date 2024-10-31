<?php
if (!defined('ABSPATH')) exit;

class berqPageOptimizer {
    public $page_slug = null;
    
    function start_cache() {
        add_action('template_redirect', [$this, 'buffer_start'], 2);
    }

    function set_slug($slug) {
        $this->page_slug = $slug;
    }

    function buffer_start() {
        ob_start([$this, 'buffer_end']);
    }

    function store_cache($buffer) {
        // Define the cache directory
        $cache_directory = bwp_get_cache_dir();

        // Create the cache directory if it doesn't exist
        if (!file_exists($cache_directory)) {
            mkdir($cache_directory, 0755, true);
        }

        $cache_file = $cache_directory . md5($this->page_slug) . '.html';
        
        // update_option( md5($slug), $key );
        file_put_contents($cache_file, $buffer);
        
        if (bwp_is_gzip_supported()) {
            $cache_file = $cache_directory . md5($this->page_slug) . '.gz';
            $buffer = gzencode($buffer, 9);
            file_put_contents($cache_file, $buffer);
        }
        
        
        do_action('berqwp_stored_page_cache', $this->page_slug);
        
        global $berq_log;
        $berq_log->info("Stored cache for $this->page_slug from PageOptimizer class");
    }

    function buffer_end($buffer) {

        if (empty($buffer)) {
            return $buffer;
        }

        if (!function_exists('str_get_html')) {
            require_once optifer_PATH . '/simplehtmldom/simple_html_dom.php';
        }
        
        if (!class_exists('scriptSeqManipulate')) {
            require_once optifer_PATH . '/inc/class-scriptSeqManipulate.php';
        }

        $buffer = $buffer.'<!-- Optimized with BerqWP\'s instant cache. --->';
        
        $berqBufferOptimize = new berqBufferOptimize();
        $berqBufferOptimize->optimize_buffer($buffer, $this->page_slug);
        
        $buffer = apply_filters( 'berqwp_cache_buffer', $buffer );
        $this->store_cache($buffer);

        return $buffer;
    }
}