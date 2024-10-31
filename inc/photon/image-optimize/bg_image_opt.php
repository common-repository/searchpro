<?php
if (!defined('ABSPATH')) exit;

$buffer = preg_replace_callback(
    '/background(?:-image)?\s*:\s*url\(["\']?(\/[^"\'\)]+\.(?:jpg|jpeg|png)|[^"\'\)]+\.(?:jpg|jpeg|png))["\']?\)/i',
    function ($matches) {
        $webpUrl = $matches[1]; // Adjusted index to $matches[1]

        if (strpos($webpUrl, 'data:') === 0) {
            return $matches[0];
        }
    
        // Fix relative bg image url
        if (!str_contains($webpUrl, home_url()) && strpos($webpUrl, '/') === 0) {
            $webpUrl = home_url() . $webpUrl;
        }

        
        if (str_contains($webpUrl, home_url())) {

            global $berqCDN;
            $berqCDN->add_file_in_queue($webpUrl);

            // $this->add_into_download_queue($webpUrl);

            // $webp_path = $this->convert_url_to_webp($webpUrl);
            // $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/', '.webp', $webpUrl); // Adjusted index to $matches[1]

            // Extract the file extension
            $file_extension = pathinfo($webpUrl, PATHINFO_EXTENSION);

            if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg') {
                $webpUrl = update_image_url_extension($webpUrl, 'webp');
            }

            $file_path = str_replace(home_url(), ABSPATH, $webpUrl);
            // $store_webp = $this->store_image($webpUrl, $webp_path);
            $webpUrl = $webpUrl . '?t=' . $this->time;
            // var_dump($store_webp,  $webp_path);
        
            if (file_exists($webp_path) && filesize($webp_path) > 0 && $store_webp) {
                unlink($webp_path);
                return 'background-image: url(' . $webpUrl . ')'; // Adjusted return string
            }
        }
    

        return $matches[0]; // Return the original string if the conditions aren't met
    },
    $buffer
);

// important webp bg images
$buffer = str_replace(".webp?t=$this->time);", ".webp?t=$this->time)!important;", $buffer);


$buffer = preg_replace_callback(
    '/url(\(((?:[^()]+|(?1))+)\))/i',
    function ($matches) {

        if (!str_contains($matches[0], '.png') && !str_contains($matches[0], '.jpg') && !str_contains($matches[0], '.jpeg')) {
            return $matches[0];
        }

        $webpUrl = str_replace("'", '', $matches[2]); // Adjusted index to $matches[1]
        // var_dump($matches, strpos($webpUrl, '//'));

        if (strpos($webpUrl, 'data:') === 0) {
            return $matches[0];
        }

        if (str_contains($webpUrl, '//') && !str_contains($webpUrl, '://')) {
            $webpUrl = str_replace('//', 'https://', $webpUrl);
        }

        // Fix relative image url
        if (!str_contains($webpUrl, home_url()) && strpos($webpUrl, '/') === 0 && strpos($webpUrl, '//') === false) {
            $webpUrl = home_url() . $webpUrl;
        }

        $is_writeable = !str_contains($webpUrl, '/wp-content/themes/') && !str_contains($webpUrl, '/wp-content/plugins/');

        if (!$is_writeable) {
            return $matches[0];
        }

        if (str_contains($webpUrl, home_url())) {

            global $berqCDN;
            $berqCDN->add_file_in_queue($webpUrl);
            // $this->add_into_download_queue($webpUrl);

            // $webp_path = $this->convert_url_to_webp($webpUrl);
            // $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/', '.webp', $webpUrl); // Adjusted index to $matches[1]
            
            // Extract the file extension
            $file_extension = pathinfo($webpUrl, PATHINFO_EXTENSION);

            if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg') {
                $webpUrl = update_image_url_extension($webpUrl, 'webp');
            }

            $file_path = str_replace(home_url(), ABSPATH, $webpUrl);
            // $store_webp = $this->store_image($webpUrl, $webp_path);
            $webpUrl = $webpUrl . '?t=' . $this->time;
    
            // var_dump($store_webp, $webp_path);
    
            if (file_exists($webp_path) && filesize($webp_path) > 0 && $store_webp) {
                unlink($webp_path);
                // Adjusted return string to include url() function without background or background-image property
                return 'url(' . $webpUrl . ')';
            }
        }



        return $matches[0]; // Return the original string if the conditions aren't met
    },
    $buffer
);

// echo $buffer;
// exit;
