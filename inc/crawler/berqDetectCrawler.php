<?php
if (!defined('ABSPATH')) exit;

class berqDetectCrawler {
    public static function is_crawler() {
        // Load the JSON data
        $json_file = optifer_PATH . '/inc/crawler/crawler-user-agents.json';
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];


        $crawler_data = json_decode(file_get_contents($json_file), true);
    
        if (!$crawler_data) {
            error_log('Failed to load or decode the JSON file.');
            return false; // Return false if the JSON file couldn't be loaded or decoded
        }
    
        // Loop through each crawler pattern in the JSON file
        foreach ($crawler_data as $crawler) {
            if (isset($crawler['pattern'])) {
                // Escape backslashes and forward slashes for accurate pattern matching
                $pattern = str_replace(['\\/', '/'], ['/', '\/'], $crawler['pattern']);

                // Check if the user agent matches the pattern
                if (preg_match('/' . $pattern . '/i', $user_agent)) {
                    return true; // User agent matches a crawler pattern
                }
            }
        }
    
        return false; // No match found, user agent is not a crawler
    }
}