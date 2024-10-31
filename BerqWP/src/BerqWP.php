<?php

namespace BerqWP;
use BerqWP\Cache;
use BerqWP\CriticalCSS;
use BerqWP\CDN;

class BerqWP
{
    protected $api_host = 'https://boost.berqwp.com';
    protected $license_key = null;
    protected $cache_directory = null;
    protected $storage_dir = null;

    function __construct($license_key, $cache_directory, $storage_dir) {
        $this->license_key = $license_key;
        $this->cache_directory = $cache_directory;
        $this->storage_dir = $storage_dir;
    }

    function request_cache($post_data) {
        $cache = new Cache($this->api_host, $this->cache_directory, $this->storage_dir);
        return $cache->request_cache($post_data);
    }

    function request_multi_cache($post_data_arr) {
        $cache = new Cache($this->api_host, $this->cache_directory, $this->storage_dir);
        return $cache->request_multi_cache($post_data_arr);
    }

    function purge_critilclcss($domain) {
        $critical = new CriticalCSS($this->api_host, $this->license_key);
        return $critical->purge_all($domain);
    }

    function purge_criticlecss_url($page_url) {
        $critical = new CriticalCSS($this->api_host, $this->license_key);
        return $critical->purge_url($page_url);
    }

    function purge_cdn($domain) {
        $critical = new CDN($this->api_host, $this->license_key);
        return $critical->purge_all($domain);
    }

    function request_cache_warmup($post_data) {
        $cache = new Cache($this->api_host, $this->cache_directory, $this->storage_dir);
        return $cache->request_cache_warmup($post_data);
    }

}