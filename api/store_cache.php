<?php
if (!defined('ABSPATH')) exit;

$status = sanitize_text_field($request->get_param('status'));
$key = sanitize_text_field($request->get_param('key'));
$html = base64_decode($request->get_param('html'));
$slug = $request->get_param('page_slug');

if ($status == 'success' && !empty($request->get_param('html'))) {

    // Allow other plugins to modify cache html
    $html = apply_filters( 'berqwp_cache_buffer', $html );

    // Define the cache directory
    $cache_directory = bwp_get_cache_dir();

    // Create the cache directory if it doesn't exist
    if (!file_exists($cache_directory)) {
        mkdir($cache_directory, 0755, true);
    }

    $cache_file = $cache_directory . md5($slug) . '.html';
    
    // update_option( md5($slug), $key );
    file_put_contents($cache_file, $html);
    
    if (bwp_is_gzip_supported()) {
        $cache_file = $cache_directory . md5($slug) . '.gz';
        $html = gzencode($html, 9);
        file_put_contents($cache_file, $html);
    }
    
    
    do_action('berqwp_stored_page_cache', $slug);
    
    global $berq_log;
    $berq_log->info("Stored cache for $slug");

    // Cache is stored which means connection is stable
    // Delete connection test error transient
    $check_connection = bwp_check_connection();
    if ( $check_connection['status'] == 'error' ) {
        delete_transient('berqwp_connection_status');
    }
    
    
}
unset($html); // release memory