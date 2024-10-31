<?php

class berq_realcookie {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'preloadscript']);
    }

    function preloadscript($buffer) {
        $realcookiescriptid = 'id="real-cookie-banner-pro-vendor-real-cookie-banner-pro-banner-js"';

        if (strpos($buffer, $realcookiescriptid) !== false) {
            $buffer = $this->excludeScript($realcookiescriptid, $buffer);
            $buffer = $this->excludeScript('real-cookie-banner-pro', $buffer);
            $buffer = $this->excludeScript('window.realCookieBanner', $buffer);
            $buffer = $this->excludeScript('real-cookie-banner-pro-banner-js-before', $buffer);
            $buffer = $this->excludeScript('real-cookie-banner-pro-banner-js', $buffer);
            // $buffer = $this->excludeScript('js-cookie-pys-js', $buffer);
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
new berq_realcookie();