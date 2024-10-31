<?php

class berqElementorOpt {
    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'loadAnimations']);
    }

    function loadAnimations($buffer) {
        if (strpos($buffer, 'elementor-invisible') !== false) {
            $script = "
            <script id='bwp-elementor-animations' data-berqwp async>
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

                    function loadElementorAnimations() {
                        const elements = document.querySelectorAll('.elementor-element[data-settings]');
                        elements.forEach(function (element) {
                            if (isInViewport(element)) {
                                const animationSettings = element.getAttribute('data-settings');
                                const dataObj = JSON.parse(animationSettings);
                                let animationName = dataObj._animation;

                                if (!animationName) {return;}
                                
                                let animationDelay = dataObj._animation_delay;
                                element.classList.add('animated');
                                element.classList.remove('elementor-invisible');
                                
                                if (animationDelay) {
                                    setTimeout(function() {
                                        element.classList.add(animationName);
                                    }, animationDelay);
                                        
                                } else {
                                    element.classList.add(animationName);
                                }

                                element.removeAttribute('data-settings');
                            }
                        });
                    }

                    // Initial load
                    loadElementorAnimations();
                    LoadedElements = true;
            
                });
            })()
        
        
            </script>
            ";

            $buffer = str_replace('</body>', $script . '</body>', $buffer);
        }
        return $buffer;
    }
}

new berqElementorOpt();