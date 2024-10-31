<?php
if (!defined('ABSPATH')) exit;

class berqCDN {
    public $queue = [];

    function __construct() {
        add_action('berqwp_update_cdn_urls', [$this, 'update_urls'], 10, 2);
    }

    function add_file_in_queue($file_url) {

        if (empty($file_url)) {
            return false;
        }

        // Parse the URL to extract the domain
        $parsed_url = wp_parse_url(home_url());

        // Get the domain
        $domain = $parsed_url['host'];

        $cdnUrl = 'https://boost.berqwp.com/photon/cdn/';
        $cdnUrl .= '?url=' . urlencode($file_url);
        $cdnUrl .= '&domain=' . $domain;
        $cdnUrl .= '&license_key=' . get_option('berqwp_license_key');

        $this->queue[] = [$file_url, $cdnUrl];

    }

    function finish_queue($buffer, $page_slug) {

         // Generate a unique key for the queue data
        $queue_key = 'berqwp_queue_' . md5($page_slug) . '_' . uniqid();

        // Store the queue data in a transient
        set_transient($queue_key, $this->queue, 24 * HOUR_IN_SECONDS);

        // Schedule the async action with the key instead of the full queue
        as_enqueue_async_action('berqwp_update_cdn_urls', [$queue_key, $page_slug]);

        // as_enqueue_async_action('berqwp_update_cdn_urls', [$this->queue, $page_slug]);

        $this->queue = [];

        return $buffer;

    }

    function update_urls($queue_key, $page_slug) {
        // Retrieve the queue data using the key
        $urls = get_transient($queue_key);

        if ($urls === false) {
            // Handle error: queue data not found
            return;
        }

        $cache_directory = bwp_get_cache_dir();

        // Create the cache directory if it doesn't exist
        if (!file_exists($cache_directory)) {
            mkdir($cache_directory, 0755, true);
        }

        $cache_file = $cache_directory . md5($page_slug) . '.html';
        $buffer = file_get_contents($cache_file);

        $chunks = array_chunk($urls, 50);

        foreach ($chunks as $chunk) {
            $cdn_file_urls = [];

            foreach ($chunk as $arr) {
                $cdn_file_urls[] = $arr[1];
            }

            $cdn_file_responses = berqBufferOptimize::parallelCurlRequests($cdn_file_urls, [], 'GET');
    
            for ($i = 0; $i < count($cdn_file_responses); $i++) {
                $original_file = $chunk[$i][0];
                $cdnData = json_decode($cdn_file_responses[$i]);

                if (!empty ($cdnData->filePath)) {
                    $webp_url = $cdnData->filePath;
                    $buffer = str_replace($original_file, $webp_url, $buffer);
                }
            }
        }

        // update cache
        $berqPageOptimizer = new berqPageOptimizer();
        $berqPageOptimizer->set_slug($page_slug);
        $berqPageOptimizer->store_cache($buffer);

        delete_transient( $queue_key );

    }
}

global $berqCDN;
$berqCDN = new berqCDN();