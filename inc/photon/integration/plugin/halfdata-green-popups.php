<?php
if (!defined('ABSPATH')) exit;

class berq_HalfdataGreenPopups {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveArgScript']);
    }

    function moveArgScript($buffer) {
        $achorScript = 'lepopup_ajax_url';
        $appendMarkerMatch = '/wp-content/plugins/halfdata-green-popups/js/lepopup.min.js';

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

new berq_HalfdataGreenPopups();