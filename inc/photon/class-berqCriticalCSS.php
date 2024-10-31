<?php
if (!defined('ABSPATH')) exit;

class berqCriticalCSS {
    function __construct() {
        add_action('berqwp_get_critical_css', [$this, 'make_request'], 10, 1);
        add_action('berqwp_flush_page_cache', [$this, 'delete_css_file']);
        add_action('berqwp_flush_all_cache', [$this, 'delete_all_css_files']);
    }

    function get_css($page_slug) {

        if ($this->can_make_request($page_slug)) {
            as_enqueue_async_action('berqwp_get_critical_css', [$page_slug]);

            return false;
        }

        $css_file = $this->get_file_path($page_slug);
        return file_get_contents($css_file);

    }

    function make_request($page_slug, $page_url = null) {

        if (!empty($page_url)) {
            $page_url = $page_url . "?generating_critical_css";

        } else {
            $page_url = home_url() . $page_slug . "?generating_critical_css";

        }


        // $page_url = "https://wordpress-1288044-4671067.cloudwaysapps.com/?generating_critical_css";
        $url = 'https://api-critical.hamzamairaj.dev/';

        $params = [
            'url' => rawurlencode($page_url),
            'w' => '1440',
            'h' => '100000',
        ];
    
        $api_url_with_params = add_query_arg($params, $url);
        $response = wp_remote_get($api_url_with_params, array('timeout' => 100, 'sslverify' => true));
    
        // Check for errors
        if (is_wp_error($response) && wp_remote_retrieve_response_code($response) !== 200) {
            $error_message = $response->get_error_message();
            return false;
        } else {
            // Retrieve the response body
            $response_body = wp_remote_retrieve_body($response);
    
            if (isset (json_decode($response_body, true)['status']) && json_decode($response_body, true)['status'] == 'error') {
                return false;
            }
    
    
            if (!empty (trim($response_body)) && !str_contains($response_body, '</title>')) {
                $this->store_css($page_slug, $response_body);
                return $response_body;
            } else {
                as_enqueue_async_action('berqwp_get_critical_css', [$page_slug]);
            }
    
            return false;
    
        }
    }

    function delete_css_file($page_slug) {
        $file_path = $this->get_file_path($page_slug);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    function delete_all_css_files() {
        $css_directory = optifer_cache . '/critical-css/';
        berqwp_unlink_recursive($css_directory);
    }

    function store_css($page_slug, $critical_css) {
        $css_file = $this->get_file_path($page_slug);
        file_put_contents($css_file, $critical_css);

        $this->update_cache($page_slug);
    }

    function get_file_path($page_slug) {
        $css_directory = optifer_cache . '/critical-css/';

        if (!file_exists($css_directory)) {
            mkdir($css_directory, 0755, true);
        }
        
        $css_file = $css_directory . md5($page_slug) . '.css';

        return $css_file;
    }

    function can_make_request($page_slug) {
        $file_path = $this->get_file_path($page_slug);

        if (!file_exists($file_path)) {
            return true;
        }

        $file_mod_time = filemtime($file_path);
        $current_time = time();
        $file_age = $current_time - $file_mod_time;
        $seven_days_in_seconds = 7 * 24 * 60 * 60;

        // Check if the file is older than 7 days
        if ($file_age > $seven_days_in_seconds) {
            return true;
        }

        return false;
    }

    function add_css_to_buffer($buffer, $css) {
        $criticalcss = sprintf('<style data-berqwp id="berqwp-critical-css">%s</style>', $css);
        $buffer = preg_replace('/<head(\s[^>]*)?>/i', '<head$1>' . $criticalcss, $buffer);
        return $buffer;
    }

    function update_cache($page_slug) {
        if (!function_exists('str_get_html')) {
            require_once optifer_PATH . '/simplehtmldom/simple_html_dom.php';
        }

        $cache_directory = bwp_get_cache_dir();

        // Create the cache directory if it doesn't exist
        if (!file_exists($cache_directory)) {
            mkdir($cache_directory, 0755, true);
        }

        $cache_file = $cache_directory . md5($page_slug) . '.html';
        $css_file = $this->get_file_path($page_slug);

        if (file_exists($cache_file) && file_exists($css_file)) {
            $buffer = file_get_contents($cache_file);
    
            // Load the HTML buffer into Simple HTML DOM
            $html = str_get_html($buffer);
    
            // Check if there's a <style> tag with id="berqwp-critical-css"
            $style_tag = $html->find('style#berqwp-critical-css', 0);

            if ($style_tag === null) {
                $criticalcss = file_get_contents($css_file);
                $buffer = $this->add_css_to_buffer($buffer, $criticalcss);

                // update cache
                $berqPageOptimizer = new berqPageOptimizer();
                $berqBufferOptimize = new berqBufferOptimize();

                $buffer = $berqBufferOptimize->css_optimize($buffer, true, false);
                $berqPageOptimizer->set_slug($page_slug);

                $beforeBodyClose = apply_filters( 'berqwp_buffer_before_closing_body', '' );
                $buffer = str_replace('</body>', $beforeBodyClose . '</body>', $buffer);

                $berqPageOptimizer->store_cache($buffer);
            }

            unset($html);
        }


    }

}

// new berqCriticalCSS();