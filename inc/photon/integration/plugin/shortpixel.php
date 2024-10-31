<?php

class berq_shortpixel {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'preloadscript']);
    }

    function preloadscript($buffer) {
        $shortpixelscript = 'w.spaiDomain="cdn.shortpixel.ai"';

        if (strpos($buffer, $shortpixelscript) !== false) {
            $buffer = $this->excludeScript($shortpixelscript, $buffer);
        }

        $shortpixelscript = 'w.spaiDomain = "cdn.shortpixel.ai"';

        if (strpos($buffer, $shortpixelscript) !== false) {
            $buffer = $this->excludeScript($shortpixelscript, $buffer);
        }

        return $buffer;
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
}
new berq_shortpixel();