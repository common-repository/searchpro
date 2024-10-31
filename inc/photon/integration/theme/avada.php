<?php

class berq_avada {
    public $is_avada = false;

    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'preloadscript']);
        add_filter('berqwp_buffer_before_closing_body', [$this, 'preload_images']);
    }

    function preloadscript($buffer) {
        $avadamenuscript = 'var fusionNavIsCollapsed';
        
        if (strpos($buffer, 'id="fusion-scripts-js"') !== false) {
            $this->is_avada = true;
        }

        if (strpos($buffer, $avadamenuscript) !== false) {
            $buffer = $this->excludeScript($avadamenuscript, $buffer);
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

    function preload_images($scripts) {
        if ($this->is_avada) {
            $scripts .= "
            <script async>
            (function(){
                let LoadedElements = false;
                document.addEventListener('DOMContentLoaded', function () {

                    if (LoadedElements) {
                        return;
                    }
                        
                    // Function to check if an element is partially in the viewport
                    function isInViewport(element) {
                        const rect = element.getBoundingClientRect();
                        const windowHeight = (window.innerHeight || document.documentElement.clientHeight);
                        const windowWidth = (window.innerWidth || document.documentElement.clientWidth);

                        // Check if the element is within the viewport
                        return (
                            rect.top < windowHeight && 
                            rect.bottom > 0 && 
                            rect.left < windowWidth && 
                            rect.right > 0
                        );
                    }

                    // Function to set background-image for elements in the viewport
                    function loadAvadaBGImages() {
                        const elements = document.querySelectorAll('[data-bg]');
                        elements.forEach(function (element) {
                            if (isInViewport(element)) {
                                const bgValue = element.getAttribute('data-bg');
                                element.style.backgroundImage = 'url('+bgValue+')';
                            }
                        });
                    }

                    function loadAvadaAnimatedElements() {
                        const elements = document.querySelectorAll('.fusion-animated');
                        elements.forEach(function (element) {
                            if (isInViewport(element)) {
                                const ani_name = element.getAttribute('data-animationtype');
                                const ani_duration = element.getAttribute('data-animationduration');
                                const ani_delay = element.getAttribute('data-animationdelay');
                                element.style.visibility = 'visible';
                                element.style.animationName = ani_name;
                                element.style.animationDuration = ani_duration + 's';
                                element.style.animationDelay = ani_delay + 's';
                            }
                        });
                    }

                    // Initial load
                    loadAvadaBGImages();
                    loadAvadaAnimatedElements();
                    LoadedElements = true;
            
                });
            })()


            </script>
            ";
        }
        return $scripts;
    }


}
new berq_avada();