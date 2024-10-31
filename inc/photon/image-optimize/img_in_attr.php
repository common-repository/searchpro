<?php
if (!defined('ABSPATH')) exit;

// Create a new HTML object
$html = str_get_html($buffer);

// Specify the tags you want to target (div, span, section)
$tagsToTarget = ['div', 'span', 'section', 'link[rel="icon"]'];

// Iterate through each tag and find image URLs within their attributes
foreach ($tagsToTarget as $tag) {
    $elements = $html->find($tag);

    foreach ($elements as $element) {
        foreach ($element->getAllAttributes() as $attrName => $attrValue) {
            // Check if the attribute contains an image URL
            if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $attrValue)) {
                $image_url = $attrValue;

                // var_dump($image_url);

                if (!str_contains($image_url, home_url()) && strpos($image_url, '/') === 0) {
                    $image_url = home_url() . $image_url;
                }

                $is_writeable = !str_contains($image_url, '/wp-content/themes/') && !str_contains($image_url, '/wp-content/plugins/');

                if (
                    (stripos($image_url, '.jpg') !== false ||
                    stripos($image_url, '.jpeg') !== false ||
                    stripos($image_url, '.png') !== false) && $is_writeable
                ) {

                    global $berqCDN;
                    $berqCDN->add_file_in_queue($image_url);
                    // $this->add_into_download_queue($image_url);
					continue;

                    // $webp_path = $this->convert_url_to_webp($image_url);

                    if (str_contains($image_url, home_url())) {
                        // Replace image URL with WebP version
                        // $webp_url = str_replace(array('.jpg', '.jpeg', '.png'), '.webp', $image_url);

                        // Extract the file extension
                        $file_extension = pathinfo($image_url, PATHINFO_EXTENSION);

                        if ($file_extension == 'png' || $file_extension == 'jpg' || $file_extension == 'jpeg') {
                            $webp_url = update_image_url_extension($image_url, 'webp');
                        }

                    }

                    // $store_webp = $this->store_image($webp_url, $webp_path);

                    $webp_url = $webp_url . '?t=' . $this->time;
                    // $ph_webp_url = $ph_webp_url . '?t=' . $this->time; // Disable placeholder images

                    // Delete the webp image
                    if (file_exists($webp_path)) {
                        unlink($webp_path);
                    }
                    
                    // if (file_exists($ph_webp_path)) { // Disable placeholder images
                    //     unlink($ph_webp_path);
                    // }

                    // Update the attribute with the processed image URL
                    $element->$attrName = $webp_url;
                }
                
            }
        }
    }
}

// Output the final formatted HTML
$buffer = $html->save();
$html->clear();
unset($html);

// // Create a new DOMDocument
// $dom = new DOMDocument();

// // Load the HTML content
// libxml_use_internal_errors(true);
// $dom->loadHTML($buffer, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
// libxml_clear_errors();

// // Create a new DOMXPath instance
// $xpath = new DOMXPath($dom);

// // Specify the tags you want to target (div, span, section)
// $tagsToTarget = ['div', 'span', 'section'];

// // Iterate through each tag and find image URLs within their attributes
// foreach ($tagsToTarget as $tag) {
//     $elements = $xpath->query("//{$tag}");

//     foreach ($elements as $element) {
//         foreach ($element->attributes as $attrName => $attrValue) {
//             // Check if the attribute contains an image URL
//             if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $attrValue->nodeValue)) {
//                 $image_url = $attrValue->nodeValue;

//                 if (!str_contains($image_url, home_url()) && strpos($image_url, '/') === 0) {
//                     $image_url = home_url() . $image_url;
//                 }

//                 if (
//                     stripos($image_url, '.jpg') !== false ||
//                     stripos($image_url, '.jpeg') !== false ||
//                     stripos($image_url, '.png') !== false
//                 ) {
//                     $webp_path = $this->convert_url_to_webp($image_url);

//                     // Replace image URL with WebP version
//                     $webp_url = str_replace(array('.jpg', '.jpeg', '.png'), '.webp', $image_url);

//                     $store_webp = $this->store_image($webp_url, $webp_path);

//                     $webp_url = $webp_url . '?t=' . $this->time;
//                     // $ph_webp_url = $ph_webp_url . '?t=' . $this->time; // Disable placeholder images

//                     // Delete the webp image
//                     if (file_exists($webp_path)) {
//                         unlink($webp_path);
//                     }
//                     // if (file_exists($ph_webp_path)) { // Disable placeholder images
//                     //     unlink($ph_webp_path);
//                     // }

//                 }

//                 // Update the attribute with the processed image URL
//                 $element->setAttribute($attrName, $webp_url);
//             }
//         }
//     }
// }

// // Output the final formatted HTML
// $buffer = $dom->saveHTML();
// exit();

// $buffer = preg_replace_callback(
//     '/<(div|span|section)([^>]*)\b([a-zA-Z0-9_-]+)=["\'](.*?\.(?:jpg|jpeg|png|gif|bmp))["\'](.*?)>/is',
//     function ($matches) {
//         // var_dump($matches);
//         $tag = $matches[0];
//         $tag_name = $matches[1];
//         $attr_name = $matches[2];
//         $image_url = $matches[4];

//         // Process or modify tags with image URLs as needed
//         if (!str_contains($image_url, home_url()) && strpos($image_url, '/') === 0) {
//             $image_url = home_url() . $image_url;
//         }

//         var_dump($image_url);

//         // Rest of your logic for processing image URLs within other HTML tags

//         // Example: Replace the original image URL with the processed one
//         $tag = str_replace($attr_name . '="' . $image_url . '"', $attr_name . '="' . $processed_image_url . '"', $tag);

//         return $tag;
//     },
//     $buffer
// );
