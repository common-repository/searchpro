<?php

namespace BerqWP;

class Utils {
    public static function is_gzip_supported() {
        return function_exists('gzencode') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
    }
}