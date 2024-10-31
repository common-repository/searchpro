<?php
/**
 * Plugin Name:       BerqWP
 * Plugin URI:        https://berqwp.com
 * Description:       Automatically pass Core Web Vitals for WordPress and boost your speed score to 90+ for both mobile and desktop without any technical skills.
 * Version:           2.1.4
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Author:            BerqWP
 * Author URI:        https://berqwp.com
 * Text Domain:       searchpro
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit;

if (!defined('BERQWP_VERSION')) {
	define('BERQWP_VERSION', '2.1.4');
}

if (!defined('optifer_PATH')) {
	define('optifer_PATH', plugin_dir_path(__FILE__));
}

if (!defined('optifer_URL')) {
	define('optifer_URL', plugin_dir_url(__FILE__));
}

if (!defined('optifer_cache')) {
	define('optifer_cache', WP_CONTENT_DIR . '/cache/berqwp/');
}

if (!defined('BERQ_SERVER')) {
	define('BERQ_SERVER', 'https://berqwp.com/');
}

if (!defined('BERQ_SECRET')) {
	define('BERQ_SECRET', '64bc22f838e982.66069471');
}


if (!defined('BERQWP_MAX_WARMUP_REQUESTS')) {
	define('BERQWP_MAX_WARMUP_REQUESTS', 2); // The maximum number of requests in our rate limit window.
}

if (!defined('BERQWP_WARMUP_RATE_LIMIT_WINDOW')) {
	define('BERQWP_WARMUP_RATE_LIMIT_WINDOW', 125); // Time window for rate limit in seconds, i.e., 5 minutes.
}

if (!file_exists(optifer_cache)) {
	mkdir(optifer_cache, 0755, true);
}

// Initialize BerqWP SDK
require_once optifer_PATH . '/BerqWP/vendor/autoload.php';

require_once optifer_PATH . '/inc/crawler/berqDetectCrawler.php';
require_once optifer_PATH . '/inc/class-berqreverseproxy.php';
require_once optifer_PATH . '/inc/class-ignoreparams.php';
require_once optifer_PATH . '/inc/class-MemoryLeakDetector.php';
require_once optifer_PATH . '/vendor/autoload.php';
require_once optifer_PATH . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
require_once optifer_PATH . '/inc/class-berqlogs.php';
require_once optifer_PATH . '/inc/helper-functions.php';
require_once optifer_PATH . '/inc/classs-http.php';
require_once optifer_PATH . '/inc/photon/class-berqPageOptimizer.php';
require_once optifer_PATH . '/inc/photon/class-berqBufferOptimize.php';
require_once optifer_PATH . '/inc/photon/class-styleOptimizer.php';
require_once optifer_PATH . '/inc/photon/class-scriptOptimizer.php';
require_once optifer_PATH . '/inc/photon/class-berqImages.php';
require_once optifer_PATH . '/inc/photon/class-berqCDN.php';
require_once optifer_PATH . '/inc/photon/class-berqCriticalCSS.php';
require_once optifer_PATH . '/inc/photon/integration/index.php';
require_once optifer_PATH . '/inc/class-berqcache.php';
require_once optifer_PATH . '/inc/class-berqwp.php';
require_once optifer_PATH . '/inc/class-berqimages.php';
require_once optifer_PATH . '/inc/class-berqintegrations.php';
require_once optifer_PATH . '/inc/class-berqnotifications.php';
require_once optifer_PATH . '/inc/httpclient.php';


// Redirect to BerqWP admin page after activation
register_activation_hook(__FILE__, 'berqwp_activation');
register_deactivation_hook(__FILE__, 'berqwp_deactivate_plugin');

function berqwp_activation()
{
	// Specify the drop-in file path
	$dropin_file = ABSPATH . 'wp-content/advanced-cache.php';

	// Dynamically create the drop-in file
	$dropin_content = file_get_contents(optifer_PATH . 'advanced-cache.php');

	// Write the drop-in content to the file, replacing any existing file
	file_put_contents($dropin_file, $dropin_content);

	// Enable object cache in wp-config.php
    berqwp_enable_object_cache(true);

	if (function_exists('wp_cache_flush')) {
		wp_cache_flush(); // Clear the entire object cache.
	}

	set_transient( 'bqwp_hide_feedback_notice', true, 60*60 ); // Hide for one hour
	set_transient('berqwp_redirect', true, 1);
}

function berqwp_deactivate_plugin() {
    // Specify the drop-in file path
    $dropin_file = ABSPATH . 'wp-content/advanced-cache.php';

    // Check if the drop-in file exists and delete it
    if (file_exists($dropin_file)) {
        unlink($dropin_file);
    }

	// Disable object cache in wp-config.php
    berqwp_enable_object_cache(false);
}