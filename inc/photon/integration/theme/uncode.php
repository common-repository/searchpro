<?php

class berq_uncode {

    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'load_borders']);
    }

    function excludeScript($scriptMatch, $buffer) {
        $html = str_get_html($buffer);
        $scriptTags = $html->find('script');

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $scriptMatch) !== false) {
                $script->setAttribute('data-earlyberqwp', '1');
            }
        }

        $buffer = $html->save();
        $html->clear();
        unset($html);

        return $buffer;
    }

    // Load borders upon initial page load
    // Use `berqwp_js_initialized` event which if fired when BerqWP begins to load JavaScript
    function load_borders($buffer) {

        if (strpos($buffer, '/wp-content/themes/uncode/library/js/init.js') !== false) {
            $script = "<script data-berqwp defer>
            window.addEventListener('berqwp_js_initialized', function() {
                if (document.body.classList.contains('has-body-borders')) {
                    var bodyBorderDiv = document.querySelectorAll('.body-borders .top-border');
                    if (bodyBorderDiv.length) {
                        bodyBorder = bodyBorderDiv[0].offsetHeight;
                    } else bodyBorder = 0;
                        
                    if (bodyBorder != 0) {
                       document.documentElement.style.paddingTop = bodyBorder + 'px';
                    }
                }
            });
            </script>";
            $buffer = str_replace('</body>', $script . '</body>', $buffer);
        }

        return $buffer;
    }


}
new berq_uncode();