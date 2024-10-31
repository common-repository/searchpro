<?php
if (!defined('ABSPATH')) exit;

class berq_Salient {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'fix_sticky_sidebar']);
    }

    function fix_sticky_sidebar($buffer) {
        $achorScript = '/salient/js/build/third-party/stickkit.js';
        $appendMarkerMatch = '/salient/js/build/init.js';

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
new berq_Salient();