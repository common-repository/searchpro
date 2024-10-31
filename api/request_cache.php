<?php

if (!defined('ABSPATH')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['berqwp_request_cache']) && !empty($_POST['page_slug'])) {
    
    if (defined('DOING_CRON') && DOING_CRON) {
        return;
    }

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    $path = $_POST['page_slug'];

    if (bwp_can_warmup_cache($path)) {
        warmup_cache_by_slug($path);
        echo json_encode(['status' => 'success']);
    }
    
    exit;
}