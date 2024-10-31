<?php

class berq_premiumaddonselementor {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveArgScript']);
    }

    function moveArgScript($buffer) {
        $achorScript = '/plugins/premium-addons-for-elementor/assets/frontend/min-js/slick.min.js';
        $appendMarkerMatch = '/uploads/premium-addons-elementor/pa-frontend-';

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
new berq_premiumaddonselementor();