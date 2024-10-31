<?php

class berq_jetelements {

    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveArgScript']);
    }

    function moveArgScript($buffer) {
        $achorScript = '/plugins/jet-elements/assets/js/jet-elements.min.js';
        $appendMarkerMatch = '/wp-content/plugins/elementor/assets/js/frontend.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendAfter($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        $achorScript = 'id="jet-elements-js-extra"';
        $appendMarkerMatch = 'id="jet-elements-js"';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        return $buffer;
    }
}
new berq_jetelements();