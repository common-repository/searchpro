<?php

class berq_WOOCommerce {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveArgScript']);
    }

    function moveArgScript($buffer) {
        $achorScript = '/plugins/woocommerce/assets/js/frontend/single-product.min.js';
        $appendMarkerMatch = '/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min.js';

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
new berq_WOOCommerce();