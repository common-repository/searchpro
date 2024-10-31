<?php
if (!defined('ABSPATH'))
    exit;

use BerqWP\BerqWP;

function berqwp_is_slug_excludable($slug)
{
    if (empty($slug)) {
        return true;
    }

    $exclude_items = [
        "favicon.ico", 
        "moderation-hash=", 
        "elementor_library=", 
        "add_to_wishlist=", 
        "robots.txt", 
        ".html", 
        ".php", 
        "run_warmup=", 
        "et-compare-page=", 
        "add_to_compare=", 
        "min_price=", 
        "max_price=", 
        "view_mode=", 
        "view_mode_smart=", 
        "et_columns-count=", 
        "add_to_wishlist=", 
        "et-wishlist-page=", 
        "remove_wishlist=", 
        "stock_status=", 
        "page_id=", 
        "?p=", 
        "add-to-cart=", 
        "remove_item=", 
        "cart_item_key=", 
        "quantity=", 
        "my-account=", 
        "lost-password=", 
        "reset-password=", 
        "order-pay=", 
        "order-received=", 
        "view-order=", 
        "?s=", 
        "&s=", 
        "orderby=", 
        "product_tag=", 
        "product_cat=", 
        "min_price=", 
        "max_price=", 
        "rating=", 
        "filter_", 
        "edd_action=",
        "download_id=",
        "bbp_=",
        "bbp-search=",
        "&bp_=",
        "?bp_=",
        "?action=",
        "&action=",
        "gf_page=",
        "gf_token=",
        "gform_submit=",
        "wordfence_logHuman=",
        "wordfence_lh=",
        "wordfence_syncAttackData=",
        "?elementor=",
        "&elementor=",
        "?ld_=",
        "&ld_=",
        "lesson_id=",
        "quiz_id=",
        "wpforms=",
        "?ref=",
        "&ref=",
        "?aff=",
        "&aff=",
        "?llms_=",
        "&llms_=",
        "lesson_id=",
        "course_id=",
        "vc_editable=true",
        "et_fb=1",
        "et_fb_edit",
        "rcp_action=",
        "member_id=",
        "give_action=",
        "donation_id=",
        "giveDonationFormInIframe=",
        "tribe-bar-date=",
        "tribe_eventcategory=",
        "tribe_paged=",
        "tribe_organizer=",
        "tribe_venue=",
        "job_id=",
        "job_applications=",
        "job_alerts=",
        "mepr-process=",
        "mepr-transaction=",
        "mepr_coupon=",
        "popmake=",
        "pum_form_sub=",
        "pum_action=",
        "ngg_page=",
        "gallery_id=",
        "pid=",
        "wp_simple_pay=",
        "sp_dont_optimize=",
        "remove_from_wishlist=",
        "action=yith-woocompare",
        "?amp=",
        "&amp=",
        "?noamp=",
        "&noamp=",
        "subscription_id=",
        "renewal_order=",
        "edd_bk=",
        "wpgmza=",
        "poll_id=",
        "pollresult=",
        "swpquery=",
        "swpengine=",
        "monsterinsights=",
        "defender=",
        "wdf_scan=",
        "wc_bookings_field=",
        "wc_bookings_calendar=",
        "?na=",
        "&na=",
        "?newsletter=",
        "&newsletter=",
        "affwp_ref=",
        "affwp_campaign=",
    ];

    $exclude_items = apply_filters('berqwp_exclude_slug_match', $exclude_items);

    foreach ($exclude_items as $item) {
        if (strpos($slug, $item) !== false) {
            return true;
        }
    }

    return false;
}

function berqwp_get_page_params($slug, $is_forced = false) {
    if (empty($slug)) {
        return;
    }

    $url = home_url() . $slug;
    $slug_md5 = md5($slug);

    $cache_directory = bwp_get_cache_dir();
    $cache_file = $cache_directory . $slug_md5 . '.html';
    // $key = uniqid();
    $key = '';
    $cache_max_life = @filemtime($cache_file) + (18 * 60 * 60);

    // if (!file_exists($cache_file) || (file_exists($cache_file) && $cache_max_life < time()) || (file_exists($cache_file) && bwp_is_partial_cache($slug) === true)) {
    //     // Priority 1
    //     $key = '';
    // }

    $optimization_mode = get_option('berq_opt_mode');

    if ($optimization_mode == 4) {
        $optimization_mode = 'aggressive';
    } elseif ($optimization_mode == 3) {
        $optimization_mode = 'blaze';
    } elseif ($optimization_mode == 2) {
        $optimization_mode = 'medium';
    } elseif ($optimization_mode == 1) {
        $optimization_mode = 'basic';
    }

    // Data to send as POST parameters
    $post_data = array(
        'license_key'                   => get_option('berqwp_license_key'),
        'page_url'                      => $url,
        'page_slug'                     => $slug,
        'site_url'                      => home_url(),
        'webp_max_width'                => (int) get_option('berqwp_webp_max_width'),
        'webp_quality'                  => (int) get_option('berqwp_webp_quality'),
        'img_lazyloading'               => get_option('berqwp_image_lazyloading'),
        'youtube_lazyloading'           => get_option('berqwp_lazyload_youtube_embed'),
        'js_mode'                       => get_option('berqwp_javascript_execution_mode'),
        'key'                           => $key,
        'interaction_delay'             => get_option('berqwp_interaction_delay'),
        'cache_js'                      => true,
        'use_cdn'                       => get_option('berqwp_enable_cdn'),
        'opt_mode'                      => $optimization_mode,
        'disable_webp'                  => get_option('berqwp_disable_webp'),
        'js_css_exclude_urls'           => get_option('berq_exclude_js_css', []),
        'preload_fontfaces'             => get_option('berqwp_preload_fontfaces'),
        'use_cache_webhook'             => true,
        'enable_cwv'                    => get_option('berqwp_enable_cwv'),
        // 'mobile_lcp'                 => json_encode($mobile_lcp),
        // 'desktop_lcp'                => json_encode($desktop_lcp),
        'version'                       => BERQWP_VERSION
    );

    if (defined('BERQ_STAGING') || $is_forced) {
        $post_data['run_queue'] = 1;
        $post_data['doing_queue'] = true;
    }

    return $post_data;


}

function bwp_pass_account_requirement() {
    global $berqWP, $berq_log;

    $license_key = get_option('berqwp_license_key');
	$key_response = $berqWP->verify_license_key($license_key);

    if (empty($key_response->product_ref)) {
        return false;
    }

    if  ($key_response->result !== 'success' || $key_response->status !== 'active') {
        $berq_log->error("account requirement: license verification failed");
        return false;
    }

    if ($key_response->product_ref == 'Free Account' && bwp_cached_pages_count() >= 10) {
        return false;
    }

    if ($key_response->product_ref == 'Starter' && bwp_cached_pages_count() >= 100) {
        return false;
    }

    return true;
}

function warmup_cache_by_slug($slug, $is_forced = false)
{
    if (empty($slug)) {
        return;
    }

    if (berqwp_is_slug_excludable($slug)) {
        return;
    }

    $slug_md5 = md5($slug);

    $cache_directory = bwp_get_cache_dir();
    $cache_file = $cache_directory . $slug_md5 . '.html';
    $cache_max_life = @filemtime($cache_file) + (18 * 60 * 60);

    if (!file_exists($cache_file) && bwp_pass_account_requirement() === false) {
        return;
    }

    // Return if page is excluded from cache
    $pages_to_exclude = get_option('berq_exclude_urls', []);

    if (in_array(home_url() . $slug, $pages_to_exclude)) {
        return;
    }

    // Not needed anymore, using webhook instead
    // $check_rest = bwp_check_rest_api(true);
    // if ( $check_rest['status'] == 'error' ) {
    //     global $berq_log;
    //     $berq_log->error('Exiting cache warmup by slug, rest api not working.');
    //     return;
    // }

    $check_connection = bwp_check_connection(true);
    if ( $check_connection['status'] == 'error' ) {
        global $berq_log;
        $berq_log->error('Exiting cache warmup by slug, website is unaccessible.');
        return;
    }
    
    // Hook to modify cache lifespan
    $cache_max_life = apply_filters('berqwp_cache_lifespan', $cache_max_life);

    if (file_exists($cache_file) && bwp_is_partial_cache($slug) === false && $cache_max_life > time()) {
        return;
    }

    // API endpoint URL
    $api_endpoint = 'https://boost.berqwp.com/photon/';
    
    if (get_site_url() == 'http://berq-test.local') {
        $api_endpoint = 'http://dev-berqwp.local/photon/';
    }

    // Modify photon engine endpoint for testing purposes
    $api_endpoint = apply_filters( 'berqwp_photon_endpoint', $api_endpoint );

    $post_data = berqwp_get_page_params($slug, $is_forced);
    
    // Set up the request arguments
    $args = array(
        'body'              => $post_data,  // Pass the POST data here
        'method'            => 'POST',
        'sslverify'   => false,
        // 'blocking'          => false,
        'timeout'           => $is_forced === true ? 20 : 0.1,
        'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded', // Adjust content type if needed
        ),
    );

    // // Send the POST request
    // $response = wp_remote_post($api_endpoint, $args);

    // // Check for errors and handle the response
    // if (is_wp_error($response)) {
    //     // There was an error with the request
    //     error_log('Error: ' . $response->get_error_message());
    // }

    // $client = new HttpClient('https://boost.berqwp.com');
    // $client->setUserAgent('BerqWP');
    // $client->post('/photon/', $post_data, ['Content-Type' => 'application/x-www-form-urlencoded']);

    $berqwp = new BerqWP(get_option('berqwp_license_key'), null, optifer_cache);
    $berqwp->request_cache($post_data);

}

function bwp_is_home_cached() {
    $slug_md5 = md5(bwp_url_into_path(bwp_admin_home_url('/')));
    $cache_directory = bwp_get_cache_dir();
    $cache_file = $cache_directory . $slug_md5 . '.html';

    return file_exists($cache_file);
}

function bwp_is_partial_cache($slug) {

    if (!function_exists('str_get_html')) {
        require_once optifer_PATH . '/simplehtmldom/simple_html_dom.php';
    }
    
    $cache_directory = bwp_get_cache_dir();
    $cache_key = md5($slug);
    $cache_file = $cache_directory . $cache_key . '.html';
    
    if (file_exists($cache_file)) {
        $buffer = file_get_contents($cache_file);

        if (!empty($buffer)) {
            $html = str_get_html($buffer);

            if ($html !== false) {
                $style_tag = $html->find('style#berqwp-critical-css', 0);
    
                if ($style_tag === null) {
                    return true;
                }
            }

        }
    }

    return false;
        
}

function berq_is_localhost()
{
    $whitelist = array('127.0.0.1', '::1');

    if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        return true;
    }

    return false;
}

function berqwp_remove_ignore_params($slug)
{
    // List of tracking parameters to remove
    $tracking_params = get_option('berq_ignore_urls_params', []);

    $tracking_params = apply_filters( 'berqwp_ignored_urls_params', $tracking_params );

    // Parse the provided slug
    $url_parts = parse_url($slug);
    
    // Get the current URL parameters
    $url_params = array();
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $url_params);
    }
    
    // Remove specified tracking parameters from the URL
    foreach ($tracking_params as $param) {
        $param = trim($param);
        if (isset($url_params[$param])) {
            unset($url_params[$param]);
        }
    }

    // Build the new query string
    $new_query_string = http_build_query($url_params);

    // Reconstruct the URL with the new query string
    $new_slug = $url_parts['path'];
    if (!empty($new_query_string)) {
        $new_slug .= '?' . $new_query_string;
    }

    return $new_slug;
}

function berqwp_is_sub_dir_wp()
{
    // remove http
    $site_url = explode('//', home_url())[1];
    $break_slash = explode('/', $site_url);

    return count($break_slash) > 1;
}

function berqwp_current_page_cache_file()
{
    $slug_uri = $_SERVER['REQUEST_URI'];

    // if wordpress is installed in a sub directory
    if (berqwp_is_sub_dir_wp()) {
        // Parse strings to extract paths
        $path1 = explode('/', parse_url(home_url(), PHP_URL_PATH));
        $path2 = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        // Find the common part of the paths
        $commonPath = implode('/', array_intersect($path1, $path2));

        // Subtract the common part from the first string
        $slug_uri = str_replace($commonPath, '', $_SERVER['REQUEST_URI']);
    }

    // Return if page is excluded from cache
    $pages_to_exclude = get_option('berq_exclude_urls', []);

    if (in_array(get_site_url() . $slug_uri, $pages_to_exclude)) {
        return;
    }


    $slug = berqwp_remove_ignore_params($slug_uri);

    if (isset($_GET['creating_cache'])) {
        return;
    }

    if (get_option('berqwp_enable_sandbox') == 1 && isset($_GET['berqwp'])) {
        $slug = explode('?berqwp', $slug_uri)[0];
    } elseif (get_option('berqwp_enable_sandbox') == 1 && !isset($_GET['creating_cache'])) {
        return;
    }


    // Attempt to retrieve the cached HTML from the cache directory
    $cache_directory = bwp_get_cache_dir();

    // Generate a unique cache key based on the current page URL
    $cache_key = md5($slug);
    $cache_file = $cache_directory . $cache_key . '.html';

    return $cache_file;

}

function berqwp_get_LCP_details($url, $device = 'mobile')
{
    $google_pagespeed_api_url = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&strategy=$device ";

    // Send a GET request to the Google PageSpeed Insights API
    // $response = wp_remote_get($google_pagespeed_api_url, array('timeout' => 60));
    $response = bwp_wp_remote_get($google_pagespeed_api_url, array('timeout' => 60));

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    }

    // Convert the JSON response to a PHP array
    $body = wp_remote_retrieve_body($response);
    $output = json_decode($body, true);

    // Get the LCP data        
    return $output['lighthouseResult']['audits']['largest-contentful-paint-element']['details']['items'][0]['items'][0]['node'];
}

function berqwp_enable_object_cache($enable) {
    global $berq_log;
    $berq_log->info("Updating wp-config.php");

    // Specify the wp-config.php file path
    $wp_config_file = ABSPATH . 'wp-config.php';

    // Read the contents of wp-config.php
    $wp_config_content = file_get_contents($wp_config_file);

    // Find the position of the first PHP tag using a regular expression
    preg_match('/<\?php/', $wp_config_content, $matches, PREG_OFFSET_CAPTURE);

    // Check if the PHP opening tag exists
    if (!empty($matches)) {
        $first_php_comment_position = $matches[0][1] + 5; // Move past the length of '<?php'

        // Check if the WP_CACHE definition exists in the file
        if (strpos($wp_config_content, "define('WP_CACHE'") === false && strpos($wp_config_content, "define( 'WP_CACHE' ") === false) {
            // If not, add the definition right after the opening PHP tag
            $wp_config_content = substr_replace($wp_config_content, "\n"
                                . "// Enable or disable BerqWP object cache\n"
                                . "define('WP_CACHE', " . ($enable ? 'true' : 'false') . ");\n",
                                $first_php_comment_position, 0);
        } else {
            // Otherwise, enable or disable the existing definition
            // $wp_config_content = preg_replace(
            //     "/define\('WP_CACHE', [^\n]*\);/",
            //     "define('WP_CACHE', " . ($enable ? 'true' : 'false') . ");",
            //     $wp_config_content
            // );

            $wp_config_content = preg_replace(
                "/define\(\s*'WP_CACHE'\s*,\s*[^\n]*\);/",
                "define('WP_CACHE', " . ($enable ? 'true' : 'false') . ");",
                $wp_config_content
            );
        }

        // Write the modified content back to wp-config.php
        file_put_contents($wp_config_file, $wp_config_content);
    } else {

        global $berq_log;
        $berq_log->error("Error: PHP opening tag not found in wp-config.php");
        
    }
}

// Copied from Nginx Helper plugin
function berqwp_unlink_recursive( $dir ) {

    if ( ! is_dir( $dir ) ) {
        return;
    }

    $dh = opendir( $dir );

    if ( ! $dh ) {
        return;
    }

    // phpcs:ignore -- WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition -- Variable assignment required for recursion.
    while ( false !== ( $obj = readdir( $dh ) ) ) {

        if ( '.' === $obj || '..' === $obj ) {
            continue;
        }

        if ( ! @unlink( $dir . '/' . $obj ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
            berqwp_unlink_recursive( $dir . '/' . $obj, false );
        }
    }

    closedir( $dh );
}

function berqwp_get_last_modified_timestamp() {
    global $post;

    // Check if it's a single post or page
    if (is_singular()) {
        return get_the_modified_time('U', $post->ID); // 'U' format parameter returns Unix timestamp
    }

    // Check if it's a taxonomy term
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object(); // Get the current term object

        // For tags, get the last modified date of the most recent post associated with the tag
        if (is_tag()) {
            $args = array(
                'tag_id' => $term->term_id,
                'posts_per_page' => 1,
                'orderby' => 'modified',
                'order' => 'DESC',
                'fields' => 'ids', // Return only post IDs to reduce overhead
            );
            $posts = get_posts($args);
            if ($posts) {
                $latest_post_id = $posts[0];
                return get_the_modified_time('U', $latest_post_id); // 'U' format parameter returns Unix timestamp
            }
        }

        // For category archives, get the last modified date of the most recent post within the category
        if (is_category()) {
            $args = array(
                'category' => $term->term_id,
                'posts_per_page' => 1,
                'orderby' => 'modified',
                'order' => 'DESC',
                'fields' => 'ids', // Return only post IDs to reduce overhead
            );
            $posts = get_posts($args);
            if ($posts) {
                $latest_post_id = $posts[0];
                return get_the_modified_time('U', $latest_post_id); // 'U' format parameter returns Unix timestamp
            }
        }

        return strtotime($term->modified); // Convert modified date to timestamp
    }

    // Check if it's an archive
    if (is_archive()) {
        // For other archives
        $archive_id = get_queried_object_id(); // Get the ID of the current archive
        $archive = get_post($archive_id); // Get the archive post object
        return strtotime($archive->post_modified); // Convert modified date to timestamp
    }

    // For other cases (fallback)
    return false;
}

function bwp_is_gzip_supported() {
    return function_exists('gzencode') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
}

function bwp_cached_pages_count() {
    $cache_directory = optifer_cache . DIRECTORY_SEPARATOR . 'html';
    $cache_files = glob($cache_directory . DIRECTORY_SEPARATOR . "*.html");
    return count($cache_files);
}

function bwp_wp_remote_get($url, $args = array()) {
    // Default arguments
    $defaults = array(
        'headers' => array(
            'User-Agent' => 'BerqWP Bot', // Customize user agent if needed
        ),
    );

    // Merge provided arguments with defaults
    $args = wp_parse_args($args, $defaults);

    if (empty($args['timeout'])) {
        $args['timeout'] = 30;
    }

    // Initialize cURL session
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set to return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, $args['timeout']);

    // Set the user-agent
    curl_setopt($ch, CURLOPT_USERAGENT, $args['headers']['User-Agent']);

    // Include header in the output
    curl_setopt($ch, CURLOPT_HEADER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error_message = curl_error($ch);
        curl_close($ch);
        return new WP_Error('curl_error', $error_message);
    }

    // Close cURL session
    curl_close($ch);

    // Separate headers and body
    list($headers, $body) = explode("\r\n\r\n", $response, 2);

    // Parse headers into array
    $header_lines = explode("\r\n", $headers);
    $headers = array();
    foreach ($header_lines as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) == 2) {
            $headers[trim($parts[0])] = trim($parts[1]);
        }
    }

    // Construct response array similar to wp_remote_get
    $response = array(
        'headers' => $headers,
        'body' => $body,
        'response' => array(
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'message' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ),
        'cookies' => array(),
        'filename' => '',
    );

    return $response;
}

function bwp_is_openlitespeed_server() {
    $is_litespeed = false;
    $is_litespeed =  isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false;

    $headers = getallheaders();
    foreach ($headers as $header => $value) {
        if (stripos($value, 'LiteSpeed') !== false) {
            $is_litespeed = true;
        }
    }

    return $is_litespeed;
}

function verify_request_origin($request) {
    // Check the referrer header to ensure the request is coming from the same site
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $site_url = get_site_url();

    // Optionally, check the origin header
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    // Ensure the request comes from the same site
    if (strpos($referrer, $site_url) !== 0 && strpos($origin, $site_url) !== 0) {
        return new WP_Error('rest_forbidden', esc_html__('You cannot access this resource.', 'searchpro'), array('status' => 403));
    }

    // Ensure the origin is a subdomain of berqwp.com
    if (!preg_match('/^https?:\/\/([a-z0-9-]+\.)?berqwp\.com$/', $origin)) {
        return new WP_Error('rest_forbidden', esc_html__('You cannot access this resource.', 'searchpro'), array('status' => 403));
    }

    return true;
}

function berq_rest_permission_callback(WP_REST_Request $request) {
    // Get the nonce from the request
    $nonce = $request->get_header('X-WP-Nonce');

    // Verify the nonce
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('rest_invalid_nonce', __('Invalid nonce', 'searchpro'), array('status' => 403));
    }

    return true; // Return true to allow the request
}

function berq_rest_verify_license_callback(WP_REST_Request $request) {
    $license_key_hash = sanitize_text_field($request->get_param('license_key_hash'));

    if (empty($license_key_hash) || $license_key_hash !== md5(get_option('berqwp_license_key'))) {
        global $berq_log;
        $berq_log->error("Exiting... Invalid license key.");
        return new WP_Error('rest_invalid_nonce', __('Invalid license key', 'searchpro'), array('status' => 403));

    }

    return true; // Return true to allow the request
}

function bwp_dash_notification($msg = '', $status = 'warning') {
    ?>
    <div class="berqwp-notification <?php echo esc_attr($status)?>">
        <?php 

        echo "<div class='icon'>";
        if ($status == 'warning') {
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480L40 480c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24l0 112c0 13.3 10.7 24 24 24s24-10.7 24-24l0-112c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>';
        } elseif ($status == 'error') {
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 0c53 0 96 43 96 96l0 3.6c0 15.7-12.7 28.4-28.4 28.4l-135.1 0c-15.7 0-28.4-12.7-28.4-28.4l0-3.6c0-53 43-96 96-96zM41.4 105.4c12.5-12.5 32.8-12.5 45.3 0l64 64c.7 .7 1.3 1.4 1.9 2.1c14.2-7.3 30.4-11.4 47.5-11.4l112 0c17.1 0 33.2 4.1 47.5 11.4c.6-.7 1.2-1.4 1.9-2.1l64-64c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-64 64c-.7 .7-1.4 1.3-2.1 1.9c6.2 12 10.1 25.3 11.1 39.5l64.3 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c0 24.6-5.5 47.8-15.4 68.6c2.2 1.3 4.2 2.9 6 4.8l64 64c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-63.1-63.1c-24.5 21.8-55.8 36.2-90.3 39.6L272 240c0-8.8-7.2-16-16-16s-16 7.2-16 16l0 239.2c-34.5-3.4-65.8-17.8-90.3-39.6L86.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l64-64c1.9-1.9 3.9-3.4 6-4.8C101.5 367.8 96 344.6 96 320l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64.3 0c1.1-14.1 5-27.5 11.1-39.5c-.7-.6-1.4-1.2-2.1-1.9l-64-64c-12.5-12.5-12.5-32.8 0-45.3z"/></svg>';
        } elseif ($status == 'info') {
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M96 64c0-17.7-14.3-32-32-32S32 46.3 32 64l0 256c0 17.7 14.3 32 32 32s32-14.3 32-32L96 64zM64 480a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>';
        } else {
            return;
        }
        echo "</div>";
        echo esc_html($msg); 
        ?>
    </div>
    <?php
}

function bwp_can_warmup_cache($slug) {

    if (get_transient( 'bwp_warmup_lock_'.md5($slug) ) === false) {

        set_transient( 'bwp_warmup_lock_'.md5($slug), true, 100 );
        return true;
        
    }

    return false;
}

function bwp_clear_warmup_lock($slug) {
    delete_transient( 'bwp_warmup_lock_'.md5($slug) );
}

function bwp_extractUrlsFromCss($cssContent) {
    $urls = [];
    $pattern = '/url\((.*?)\)/i';

    preg_match_all($pattern, $cssContent, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
            // Trim any surrounding quotes and whitespace
            $urls[] = trim($match, '\'" ');
        }
    }

    return $urls;
}

function bwp_getBaseUrl($fileUrl) {
    // Parse the URL and get its components
    $parsedUrl = parse_url($fileUrl);
    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
    $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

    // Remove the file name from the path to get the base URL
    $basePath = preg_replace('/\/[^\/]+$/', '/', $path);

    // Construct the base URL
    return $scheme . $host . $basePath;
}

function bwp_rel2abs($rel, $base)
{
    /* return if already absolute URL */
    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

    /* queries and anchors */
    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

    /* parse base URL and convert to local variables:
       $scheme, $host, $path */
    extract(parse_url($base));

    /* remove non-directory element from path */
    $path = preg_replace('#/[^/]*$#', '', $path);

    /* destroy path if relative url points to root */
    if ($rel[0] == '/') $path = '';

    /* dirty absolute URL */
    $abs = "$host$path/$rel";

    /* replace '//' or '/./' or '/foo/../' with '/' */
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
    for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

    /* absolute URL is ready! */
    return $scheme.'://'.$abs;
}

function update_image_url_extension($image_url, $file_extension) {
    $url_arr = explode('.', $image_url);
    $last_index = count($url_arr) - 1;

    // Extract the file extension
    $current_file_extension = pathinfo($image_url, PATHINFO_EXTENSION);

    $url_arr[$last_index] = str_replace("$current_file_extension", $file_extension, $url_arr[$last_index]);
    $new_image_url = implode('.', $url_arr);

    return $new_image_url;

}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) !== false;
    }
}

function bwp_check_rest_api($force_check = false) {
    // Allow cache busting by passing $force_check = true
    if ($force_check) {
        delete_transient('berqwp_rest_api_status');
    }

    // Check if the result is cached
    $cached_status = get_transient('berqwp_rest_api_status');

    if ($cached_status && !$force_check) {
        return $cached_status;
    }

    // Perform the actual REST API check
    $response = wp_safe_remote_get( rest_url(), ['timeout' => 10] );

    if ( is_wp_error( $response ) ) {
        $result = array(
            'status' => 'error',
            'message' => 'The REST API is not working. Error: ' . $response->get_error_message(),
        );
    } else {
        $status_code = wp_remote_retrieve_response_code( $response );

        if ( $status_code == 200 ) {
            $result = array(
                'status' => 'success',
                'message' => 'The REST API is working correctly.',
            );
        } else {
            $result = array(
                'status' => 'error',
                'message' => 'The REST API returned an unexpected status code: ' . $status_code,
            );
        }
    }

    set_transient('berqwp_rest_api_status', $result, 60 * 60 * 24);

    return $result;
}

function bwp_check_connection($force_check = false) {
    // Allow cache busting by passing $force_check = true
    if ($force_check) {
        delete_transient('berqwp_connection_status');
    }

    // Check if the result is cached
    $cached_status = get_transient('berqwp_connection_status');

    if ($cached_status && !$force_check) {
        return $cached_status;
    }

    // Perform the actual REST API check
    $response = wp_safe_remote_get(  'https://boost.berqwp.com/photon/?connection_test=1&url='.home_url('/'), ['timeout' => 20] );

    if ( is_wp_error( $response ) ) {
        $result = array(
            'status' => 'error',
            'message' => 'The site is not accessible. Error: ' . $response->get_error_message(),
        );
    } else {
        $body = wp_remote_retrieve_body( $response );

        if ( $body == 'pingpong' ) {
            $result = array(
                'status' => 'success',
                'message' => 'Your website is accessible by BerqWP server.',
            );
        } else {
            $result = array(
                'status' => 'error',
                'message' => 'BerqWP server is unable to access your website, please whitelist our server IP address.',
            );
        }
    }

    set_transient('berqwp_connection_status', $result, 60 * 60 * 24);

    return $result;
}

function bwp_sendPostRequestInBackground($url, $params)
{
    $userAgent = 'BerqWP';
    $urlParts = parse_url($url);

    // Ensure we have a valid path and handle query strings
    $path = isset($urlParts['path']) ? $urlParts['path'] : '/';
    if (isset($urlParts['query'])) {
        $path .= '?' . $urlParts['query'];
    }
    
    $postString = http_build_query($params);

    $host = $urlParts['host'];
    $scheme = isset($urlParts['scheme']) ? $urlParts['scheme'] : 'http';
    $port = ($scheme === 'https') ? 443 : 80;
    $transport = ($scheme === 'https') ? 'ssl://' : '';

    $request = "POST $path HTTP/1.1\r\n";
    $request .= "Host: $host\r\n";
    $request .= "User-Agent: $userAgent\r\n";
    $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $request .= "Content-Length: " . strlen($postString) . "\r\n";
    $request .= "Connection: Close\r\n\r\n";
    $request .= $postString;

    $fp = fsockopen($transport . $host, $port, $errno, $errstr, 30);

    if ($fp) {
        fwrite($fp, $request);
        fclose($fp); // Close immediately to prevent waiting for the response
    } else {
        // If fsockopen fails, fallback to wp_remote_post
        $args = array(
            'timeout' => 0.01,
            'headers'    => array(
                'User-Agent' => $userAgent,
            ),
            'body' => $params
        );

        wp_remote_post($url, $args);
    }
}

function bwp_is_webpage() {
    $headers = headers_list();
    $contentType = '';
    
    foreach ($headers as $header) {
        if (stripos($header, 'Content-Type') !== false) {
            $contentType = $header;
            break;
        }
    }

    if (stripos($contentType, 'text/html') !== false) {
        return true;
    }

    return false;
}

function bwp_isGzipEncoded() {
    // Check if the Content-Encoding header is set to gzip
    if (isset($_SERVER['HTTP_CONTENT_ENCODING']) && strtolower($_SERVER['HTTP_CONTENT_ENCODING']) === 'gzip') {
        return true;
    }

    foreach (headers_list() as $header) {
        if (stripos($header, 'Content-Encoding: gzip') !== false) {
            return true;
        }
    }

    return false;
}

function bwp_url_into_path($url) {
    $parsed_url = parse_url($url);
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query = isset($parsed_url['query']) ? $parsed_url['query'] : '';

    if (!empty($query)) {
        $path = $path . '?' . $query;
    }

    $is_multisite = function_exists('is_multisite') && is_multisite();

    if (berqwp_is_sub_dir_wp() && !$is_multisite) {
        // Parse strings to extract paths
        $path1 = explode('/', parse_url(home_url(), PHP_URL_PATH));
        $path2 = explode('/', parse_url($path, PHP_URL_PATH));

        // Find the common part of the paths
        $commonPath = implode('/', array_intersect($path1, $path2));

        // Subtract the common part from the first string
        $path = str_replace($commonPath, '', $path);
    }

    return $path;
}

function bwp_sluguri_into_path($slug_uri) {
    $is_multisite = function_exists('is_multisite') && is_multisite();

    if (berqwp_is_sub_dir_wp() && !$is_multisite) {
        // Parse strings to extract paths
        $path1 = explode('/', parse_url(home_url(), PHP_URL_PATH));
        $path2 = explode('/', parse_url($slug_uri, PHP_URL_PATH));

        // Find the common part of the paths
        $commonPath = implode('/', array_intersect($path1, $path2));

        // Subtract the common part from the first string
        $slug_uri = str_replace($commonPath, '', $slug_uri);
    }

    return $slug_uri;
}

function bwp_get_cache_dir() {
    $cache_directory = optifer_cache . '/html/';

    if (function_exists('is_multisite') && is_multisite()) {
        $site_id = get_current_blog_id();
        $cache_directory .= 'site-' . $site_id . '/';
    }

    if (!is_dir($cache_directory)) {
        wp_mkdir_p($cache_directory);
    }

    return $cache_directory;
}

function bwp_store_cache_webhook() {
    require_once optifer_PATH . '/api/store_cache_webhook.php';
}

function bwp_handle_request_cache() {
    require_once optifer_PATH . '/api/request_cache.php';
}

function bwp_get_translatepress_urls($page_url) {
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = get_option('trp_settings', array());
    $languages = $trp->get_component( 'languages' );
    $url_converter = $trp->get_component( 'url_converter' );
    $publish_languages = $languages->get_language_names( $trp_settings['publish-languages'], 'english_name' );
    $urls = [];


    if (!empty($publish_languages)) {
        foreach($publish_languages as $key => $value) {
            $translation_url = $url_converter->get_url_for_language( $key, $page_url, '' );
            $urls[] = $translation_url;
        }
    }

    return $urls;
}

function bwp_can_optimize_page_url($page_url) {
    $slug = bwp_url_into_path($page_url);
    if (berqwp_is_slug_excludable($slug)) {
        return false;
    }

    // Return if page is excluded from cache
    $pages_to_exclude = get_option('berq_exclude_urls', []);

    if (in_array($page_url, $pages_to_exclude)) {
        return false;
    }

    return true;
}

// function bwp_url_with_get_home_url($url) {
//     $url_path = bwp_url_into_path($url);
//     $url = get_home_url().$url_path;
//     return $url;
// }

function bwp_get_sitemap() {
    if (isset($_GET['berqwp_sitemap'])) {

        // Get post types to optimize
        $post_types = get_option('berqwp_optimize_post_types');
        
        // Set the number of posts per batch to handle
        $posts_per_page = 10000; // Adjust this based on server capacity

        // Get current page from query string (for pagination)
        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

        // Build the query arguments with pagination
        $args = array(
            'post_type'      => $post_types,
            'post_status'    => array('publish'), // Only published posts
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'fields'         => 'ids', // Only retrieve IDs to save memory
        );

        // Run the query
        $query = new WP_Query($args);
        $total_posts = $query->found_posts; // Get the total number of posts
        
        // Calculate total pages
        $total_pages = ceil($total_posts / $posts_per_page);

        // Output JSON header
        header('Content-Type: application/json');

        $post_params = berqwp_get_page_params('/');
        unset($post_params['license_key']);
        unset($post_params['page_slug']);
        unset($post_params['page_url']);
        $post_params['key'] = '';

        if ($query->have_posts()) {
            $sitemap_urls = array();

            // Loop through the posts and generate URLs
            while ($query->have_posts()) {
                $query->the_post();
                $url = get_permalink();

                // if (class_exists('TRP_Translate_Press')) {
                //     $translated_urls = bwp_get_translatepress_urls($url);

                //     if (!empty($translated_urls)) {
                //         foreach ($translated_urls as $translated_url) {
                //             if (bwp_can_optimize_page_url($translated_url)) {
                //                 $sitemap_urls[] = $translated_url;
                //             }
                //         }

                //         continue;
                //     }
                // }
                
                if (!bwp_can_optimize_page_url($url)) {
                    continue;
                }
		
                $sitemap_urls[] = $url;
            }

            // Return response with pagination info
            $response = array(
                'paged'        => $paged,
                'post_params'  => $post_params,
                'total_pages'  => $total_pages,
                'total_posts'  => $total_posts,
                'urls'         => $sitemap_urls,
            );

            echo json_encode($response);
        } else {
            // If no posts found
            echo json_encode(array('error' => 'No posts found'));
        }

        // Clean up
        wp_reset_postdata();
        exit;
    }
}

function bwp_show_account() {
    if (!defined('BERQWP_HIDE_ACCOUNT')) {
        return true;
    }

    if (defined('BERQWP_HIDE_ACCOUNT') && BERQWP_HIDE_ACCOUNT) {
        return false;
    }

    return true;
    
}

function bwp_show_docs() {

    if (!defined('BERQWP_HIDE_DOCS')) {
        return true;
    }

    if (defined('BERQWP_HIDE_DOCS') && BERQWP_HIDE_DOCS) {
        return false;
    }

    return true;
    
}

function bwp_admin_home_url($relative_path = '') {
    $home_url = home_url();

    if (class_exists('TRP_Translate_Press')) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = get_option('trp_settings', array());
        $default_lang = $trp_settings['default-language'];
        $url_converter = $trp->get_component( 'url_converter' );
        $url_slugs = $trp_settings['url-slugs'];
        
        if (!empty($default_lang) && $trp_settings['add-subdirectory-to-default-language'] == 'yes' && !empty($url_slugs[$default_lang])) {
            // var_dump($trp_settings);
            return $home_url . '/' . $url_slugs[$default_lang] . $relative_path;
        }
    }

    return $home_url . $relative_path;
}