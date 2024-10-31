<?php

namespace BerqWP;
use BerqWP\HttpClient;
use BerqWP\MultiHttp;
use BerqWP\Utils;
use BerqWP\RateLimiter;

class Cache {
    protected $cache_directory = null;
    protected $api_host = null;
    protected $storage_dir = null;

    function __construct($api_host = null, $cache_directory = null, $storage_dir = null) {
        $this->cache_directory = $cache_directory;
        $this->api_host = $api_host;
        $this->storage_dir = $storage_dir;
    }

    function request_cache($post_data) {

        $rateLimiter = new RateLimiter(5, 60, $this->storage_dir);
        $clientIdentifier = $_SERVER['REMOTE_ADDR'];

        if ($rateLimiter->isRateLimited($clientIdentifier)) {
            return false;
        }

        $client = new HttpClient($this->api_host);
        $client->setUserAgent('BerqWP');
        $client->post('/photon/', $post_data, ['Content-Type' => 'application/x-www-form-urlencoded']);
        
        if ($client->ok()) {
            return true;
        }

        return false;
    }

    function request_multi_cache($post_data_arr) {
        if (empty($post_data_arr)) {
            return;
        }

        $rateLimiter = new RateLimiter(5, 60, $this->storage_dir);
        $clientIdentifier = $_SERVER['REMOTE_ADDR'];

        if ($rateLimiter->isRateLimited($clientIdentifier)) {
            return false;
        }
        
        $httpMulti = new MultiHttp(5);
        foreach ($post_data_arr as $post_data) {
            $httpMulti->addRequest('POST', $this->api_host . '/photon/', $post_data);
        }

        $responses = $httpMulti->execute();
    }

    function store_cache($page_path, $html) {
        
        // Create the cache directory if it doesn't exist
        if (!file_exists($this->cache_directory)) {
            mkdir($this->cache_directory, 0755, true);
        }

        $cache_file = $this->cache_directory . md5($page_path) . '.html';

        file_put_contents($cache_file, $html);

        if (Utils::is_gzip_supported()) {
            $cache_file = $this->cache_directory . md5($page_path) . '.gz';
            $html = gzencode($html);
            file_put_contents($cache_file, $html);
        }
    }

    function request_cache_warmup($post_data) {
        $client = new HttpClient($this->api_host);
        $client->setUserAgent('BerqWP');
        // $client->setTimeout(1);
        $client->post('/photon/', $post_data, ['Content-Type' => 'application/x-www-form-urlencoded']);
    }
    
}