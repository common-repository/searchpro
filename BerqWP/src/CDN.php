<?php

namespace BerqWP;

class CDN {
    protected $api_host = null;
    protected $license_key = null;

    function __construct($api_host, $license_key) {
        $this->api_host = $api_host;
        $this->license_key = $license_key;
    }

    function purge_all($domain) {
        $post_data = ['flush_cdn' => $domain, 'license_key' => $this->license_key];
        $client = new HttpClient($this->api_host);
        $client->setUserAgent('BerqWP');
        $client->post('/photon/', $post_data, ['Content-Type' => 'application/x-www-form-urlencoded']);
        
        if ($client->ok()) {
            return true;
        }

        return false;
    }
}