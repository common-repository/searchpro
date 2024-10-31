<?php
if (!defined('ABSPATH')) exit;

$images_to_store = [];
$images_store_endpoint_urls = [];
$image_to_webp_queue = [];


// If the browser supports WebP, replace image URLs
$buffer = preg_replace_callback(
    '/<img(.*?)src=["\'](.*?)["\'](.*?)>/is',
    function ($matches) use (&$images_to_store, &$images_store_endpoint_urls, &$image_to_webp_queue) {

        $image_url = $matches[2];
        // $is_writeable = !str_contains($image_url, '/wp-content/themes/') && !str_contains($image_url, '/wp-content/plugins/');
        
        if (empty($image_url)) {
            return $matches[0];
        }
        
        // if (substr($image_url, -4) === ".svg") {
        //     return $matches[0];
        // }

        if (strpos($image_url, 'data:') === 0) {
            return $matches[0];
        }
        
        
        // Check if the img is inside a JSON
        if (str_contains($matches[0], '=\"') || str_contains($matches[0], ':\/\/')) {
            return $matches[0];
        }
        
        if (strpos($image_url, '//') === 0 && !str_contains($image_url, '://')) {
            $image_url = str_replace('//', 'https://', $image_url);
        }
        
        
        if (!str_contains($image_url, home_url()) && strpos($image_url, '/') === 0 && substr($image_url, 0, 2) !== "//") {
            $image_url = home_url() . $image_url;
        }
        
        $is_writeable = str_contains($image_url, home_url()) && str_contains($image_url, "/wp-content/");

        if (
            (stripos($image_url, '.svg') !== false ||
                stripos($image_url, '.jpeg') !== false ||
                stripos($image_url, '.jpeg') !== false ||
                stripos($image_url, '.png') !== false) && $is_writeable
        ) {
            // $image_to_webp_queue[] = $image_url;
            // var_dump($image_url);

            global $berqCDN;
            $berqCDN->add_file_in_queue($image_url);
            // $this->add_into_download_queue($image_url);

            // $webp_path = $this->convert_url_to_webp($image_url);
            // var_dump($webp_path, $image_url);
            // $ph_webp_path = $this->gen_placeholder_img($image_url); // Disable placeholder images
            $webp_url = $image_url;

            if (str_contains($image_url, home_url())) {

                // Extract the file extension
                $file_extension = pathinfo($image_url, PATHINFO_EXTENSION);

                if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg') {
                    // $webp_url = str_replace(".$file_extension", '.webp', $image_url);
                    /* $webp_url = update_image_url_extension($image_url, 'webp'); */
                }

                // // Replace image URL with WebP version
                // $webp_url = str_replace(array('.jpg', '.jpeg', '.png'), '.webp', $image_url);
            }
            // $ph_webp_url = str_replace(array('.jpg', '.jpeg', '.png'), '-ph.webp', $image_url); // Disable placeholder images
    
            // if (!empty($webp_url) && !empty($webp_path)) {
    
            //     $images_store_endpoint_urls[] = "home_url()/wp-json/optifer/v1/store-webp";
            //     $images_to_store[] = json_encode(
            //         array(
            //             'image' => base64_encode(file_get_contents($webp_path)),
            //             // Base64 encode the image content
            //             'url' => $webp_url,
            //         )
            //         );
            //     $store_webp = true;
            // }
    
            // $images_to_store[] = [$webp_url, $webp_path];
            // var_dump($webp_url, $webp_path);
    
            // $start = microtime(true);
            // $store_webp = $this->store_image($webp_url, $webp_path);
            // $end = microtime(true);
            // // Calculate the runtime in milliseconds
            // $runtime = ($end - $start) * 1000;
            // var_dump($runtime);
    
            // $store_ph_webp = $this->store_image($ph_webp_url, $ph_webp_path); // Disable placeholder images
    

            $img_in_srcset = $webp_url;
            $webp_url = $webp_url . '?t=' . $this->time;
            // $ph_webp_url = $ph_webp_url . '?t=' . $this->time; // Disable placeholder images
    
            // if (file_exists($ph_webp_path)) { // Disable placeholder images
            //     unlink($ph_webp_path);
            // }
    
        } else {
            $webp_url = $matches[2];
        }

        // // Fix relative image url
        // if (!str_contains($webp_url, home_url()) && strpos($webp_url, '/') === 0) {
        //     $webp_url = home_url() . $webp_url;
        // }
    
        if (pathinfo($image_url, PATHINFO_EXTENSION) !== 'svg') {
            // Load <img> HTML content
            $html = str_get_html($matches[0]);
    
            foreach ($html->find('img') as $img_html) {
                // Check if img tag has both width and height attributes
                if ($img_html->hasAttribute('width') && $img_html->hasAttribute('height')) {
                    // Extract width and height attributes
                    $width = $img_html->getAttribute('width');
                    $height = $img_html->getAttribute('height');
    
                    
                    // Add width and height attributes to the image tag
                    $matches[1] .= ' width="' . $width . '" height="' . $height . '"';
                    
                } else {
                    // Get image dimensions
                    list($width, $height) = getimagesize($webp_url);
                    
                    // Add width and height attributes to the image tag
                    $matches[1] .= ' width="' . $width . '" height="' . $height . '"';
                }
    
                if ($img_html->hasAttribute('srcset')) {
                    // Extract srcset attribute value
                    $srcset = $img_html->getAttribute('srcset');
            
                    // Split srcset into individual sources
                    $sources = explode(',', $srcset);
            
                    // Extract each image URL
                    foreach ($sources as $source) {
                        // Extract URL and remove leading/trailing whitespace
                        $srcset_img_url = trim(explode(' ', trim($source))[0]);
                        
                        if (!empty($srcset_img_url)) {

                            global $berqCDN;
                            $berqCDN->add_file_in_queue($srcset_img_url);

                            // $this->add_into_download_queue($srcset_img_url);
                        }
    
                    }
                }
            }
    
            // Clear Simple HTML DOM object
            $html->clear();
            unset($html);
        }


        // // Check if the image tag already has width and height attributes
        // if (preg_match('/\b(?:width|height)\b/', $matches[1]) === 0) {
        //     // Get image dimensions
        //     list($width, $height) = getimagesize($webp_url);

        //     // Add width and height attributes to the image tag
        //     $matches[1] .= ' width="' . $width . '" height="' . $height . '"';
        // }

        if (isset($img_in_srcset) && str_contains($matches[3], $img_in_srcset)) {
            $matches[3] = str_replace($img_in_srcset, $webp_url, $matches[3]);
        }


        // Build the updated image tag
        // $updated_image_tag = '<img' . $matches[1] . 'data-src="' . $webp_url . '" src="'.$webp_url.'"' . ' loading="lazy" ' . $matches[3] . '>';
        $file_path = str_replace(home_url(), ABSPATH, $webp_url);
        $lazy_tag = '';

        if ($this->image_lazy_loading) {
            $lcp_elements = [];

            if ((isset($lcp_elements['mobile']) && $lcp_elements['mobile'] == $webp_url) || (isset($lcp_elements['desktop']) && $lcp_elements['desktop'] == $webp_url)) {
                $lazy_tag = 'loading="eager" fetchpriority="high"';
            } else {
                // $lazy_tag = 'loading="lazy" fetchpriority="low"';
                $lazy_tag = '';
            }
        }

        if (str_contains($matches[3], ' srcset=') && $lazy_tag !== 'loading="eager"') {
            $matches[3] = str_replace(' srcset=', ' data-berqwp-srcset=', $matches[3]);
        }

        if (str_contains($matches[1], ' srcset=') && $lazy_tag !== 'loading="eager"') {
            $matches[1] = str_replace(' srcset=', ' data-berqwp-srcset=', $matches[1]);
        }

        $ph_webp_url = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' %3E%3C/svg%3E"; // Disable placeholder images
        // Placeholder image
        // $img_srcs = $lazy_tag == 'loading="lazy" fetchpriority="low"' ? '/wp-includes/js/tinymce/skins/lightgray/img/loader.gif' : $webp_url;

        // Load <img> HTML content
        $html = str_get_html($matches[0]);
    
        foreach ($html->find('img') as $img_html) {
            // Check if img tag has both width and height attributes
            if ($img_html->hasAttribute('width') && $img_html->hasAttribute('height')) {
                // Extract width and height attributes
                $width = $img_html->getAttribute('width');
                $height = $img_html->getAttribute('height');

                $svg = '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg" version="1.1">';
                $svg .= '<rect width="100%" height="100%" fill="none" />';
                $svg .= '</svg>';

                $base64Svg = base64_encode($svg);

                // Create data URI
                $ph_webp_url = 'data:image/svg+xml;base64,' . $base64Svg;

                
            } else {
                $ph_webp_url = 'data:image/gif;placeholder=MjQxOjM1NQ==-1;base64,R0lGODlhAQABAIABAAAAAP///yH5BAEAAAEALAAAAAABAAEAAAICTAEAOw==';
            }

        }

        // Clear Simple HTML DOM object
        $html->clear();
        unset($html);

        $img_srcs = $lazy_tag == '' ? $ph_webp_url : $webp_url;


        // if (file_exists($file_path) && filesize($file_path) > 0) {
        if (
            (stripos($image_url, '.jpg') !== false ||
                stripos($image_url, '.jpeg') !== false ||
                stripos($image_url, '.png') !== false) && !empty($webp_url)
        ) {


            $updated_image_tag = '<img decoding="async"' . $matches[1] . ' data-berqwpsrc="' . esc_attr($webp_url) . '" src="' . esc_attr($img_srcs) . '"' . ' ' . $lazy_tag . ' ' . $matches[3] . '>';
            return $updated_image_tag;

        } elseif (!empty($image_url) && str_contains($image_url, '//')) {
            $updated_image_tag = '<img decoding="async"' . $matches[1] . ' data-berqwpsrc="' . esc_attr($image_url) . '" src="' . esc_attr($img_srcs) . '"' . ' ' . $lazy_tag . ' ' . $matches[3] . '>';
            return $updated_image_tag;

        } else {
            return str_replace('>', $lazy_tag . ' decoding="async">', $matches[0]);
        }

        return $updated_image_tag;
    },
    $buffer
);

// optimize image srcset in source tag

// Load HTML from the buffer
$html = str_get_html($buffer);

// Process <source> tags
foreach ($html->find('source') as $source) {
    // Extract srcset attribute value
    $srcset = $source->getAttribute('srcset');
        
    // Split srcset into individual sources
    $sources = explode(',', $srcset);

    // Extract each image URL
    foreach ($sources as $source) {
        // Extract URL and remove leading/trailing whitespace
        $srcset_img_url = trim(explode(' ', trim($source))[0]);
        
        if (!empty($srcset_img_url)) {
            global $berqCDN;
            $berqCDN->add_file_in_queue($srcset_img_url);
            // $this->add_into_download_queue($srcset_img_url);
        }

    }

    unset($srcset, $sources, $source);
}

// Clear Simple HTML DOM object
$html->clear();
unset($html);