<?php
if (!defined('ABSPATH')) exit;

class berq_Revolution_slider {
    function __construct() {
        // add_filter('berqwp_before_script_optimization', [$this, 'load_slider']);
    }

    function load_slider($buffer) {
        $script = "
        <script>
        document.querySelectorAll('rs-module').forEach(function(item){
            let id = item.getAttribute('id')
            if (id) {
                window.revapi4 = window.revapi4 === undefined || window.revapi4 === null || window.revapi4.length === 0 ? document.getElementById(id) : window.revapi4;
                window.revapi4 = jQuery(window.revapi4);
                let args = bwp_get_rev_args();
                revapi4.revolutionInit(args);
        
                function bwp_get_rev_args() {
                    // Get the script tag by ID
                    let scriptTag = document.getElementById('rs-initialisation-scripts');
                    
                    if (scriptTag) {
                        // Extract the content of the script tag
                        var scriptContent = scriptTag.innerHTML;
        
                        // Use a regular expression to find the argument object
                        var regex = /revapi4\.revolutionInit\((\{[\s\S]*?\})\);/;
                        var match = scriptContent.match(regex);
        
                        if (match && match[1]) {
                        // Extract the JSON string
                        var jsonString = match[1]
                            .replace(/(\w+)\s*:/g, '\"$1\":')  // Add double quotes around property names
                            .replace(/'/g, '\"')              // Replace single quotes with double quotes
                            .replace(/,\s*}/g, '}')          // Remove trailing commas before closing brace
                            .replace(/,\s*]/g, ']');         // Remove trailing commas before closing bracket
        
                        try {
                            // Parse the JSON string to an object
                            var initArgs = JSON.parse(jsonString);
        
                            return initArgs;
        
                            // Now you can use initArgs as needed
                        } catch (e) {
                            console.log('Error parsing JSON:', e);
                        }
                        } else {
                        console.log('Initialization arguments not found.');
                        }
                    } else {
                        console.log('Script tag with ID \"rs-initialisation-scripts\" not found.');
                    }
                }
            }
            
        })
        </script>
        ";

        if (str_contains($buffer, '<rs-module')) {
            $buffer = str_replace('</body>', $script . '</body>', $buffer);
        }

        return $buffer;
    }
}

new berq_Revolution_slider();