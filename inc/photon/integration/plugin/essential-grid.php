<?php

class berq_essentialGrid {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveArgScript']);
    }

    function moveArgScript($buffer) {
        $achorScript = 'function eggbfc';
        $appendMarkerMatch = '/plugins/essential-grid/public/assets/js/esg.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendAfter($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        return $buffer;
    }
}
new berq_essentialGrid();