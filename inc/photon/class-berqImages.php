<?php
if (!defined('ABSPATH')) exit;

class berqImagesOpt {
    public $time = null;
    public $image_lazy_loading = null;

    function optimize_images($buffer) {
        $this->time = time();
        
        require_once optifer_PATH . '/inc/photon/image-optimize/image_tag_opt.php';
        require_once optifer_PATH . '/inc/photon/image-optimize/bg_image_opt.php';
        require_once optifer_PATH . '/inc/photon/image-optimize/img_in_attr.php';
        require_once optifer_PATH . '/inc/photon/image-optimize/img_in_style.php';

        return $buffer;
    }
}