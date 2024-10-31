<?php

if ($this->image_lazy_loading) {

    // Load the HTML from the $buffer variable
    $html = str_get_html($buffer);
    
    // Loop through all elements that have a style attribute
    foreach ($html->find('*[style]') as $element) {
        // Get the current style attribute
        $style = $element->style;
    
        // Check if the style contains a background-image
        if (preg_match('/background-image\s*:\s*url\((.*?)\)/', $style, $matches)) {
            // Extract the image URL
            $imageUrl = trim($matches[1], "'\"");

            global $berqCDN;
            $berqCDN->add_file_in_queue($imageUrl);
    
            // Remove only the background-image part from the style attribute
            $newStyle = preg_replace('/background-image\s*:\s*url\(.*?\);?/', '', $style);
    
            // Reapply the cleaned style attribute
            $element->style = trim($newStyle);
    
            // Add a data attribute for the background image to be lazy-loaded
            $element->setAttribute('data-berqwpbg', $imageUrl);
    
            // Optionally, add a class to indicate lazy loading
            $element->class .= ' lazy-berqwpbg';
        }
    }
    
    // Output the modified HTML
    $buffer = $html->save();
    $html->clear(); // Clear memory
    unset($html);

}