<?php

if (!defined('ABSPATH')) exit;

use BerqWP\BerqWP;

class berqWooCommerce extends berqIntegrations {
    function __construct() {
        add_action( 'save_post_product', [$this, 'flush_product_cache'] );
        add_action( 'woocommerce_update_product', [$this, 'flush_product_cache'] );
        add_action( 'woocommerce_product_set_stock_status', [$this, 'flush_product_cache'] );
        
        // Flush critical css when needed
        add_action( 'save_post_product', [$this, 'flush_product_critical_css'] );
        add_action( 'woocommerce_update_product', [$this, 'flush_product_critical_css'] );
    }

    function flush_product_critical_css($post_id) {
        // Ensure it's a product
        if ( get_post_type( $post_id ) != 'product' ) {
            return;
        }

        // Get the full URL of the product
        $product_url = get_permalink( $post_id );
        $berqwp = new BerqWP(get_option('berqwp_license_key'), null, null);
        $berqwp->purge_criticlecss_url($product_url);
        
    }

    function flush_product_cache($post_id) {
        // Ensure it's a product
        if ( get_post_type( $post_id ) != 'product' ) {
            return;
        }

        // Get the full URL of the product
        $product_url = get_permalink( $post_id );
        
        // Parse the URL to get the path
        $parsed_url = wp_parse_url( $product_url );
        $product_path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

        berqCache::purge_page($product_path);
    }


}

new berqWooCommerce();