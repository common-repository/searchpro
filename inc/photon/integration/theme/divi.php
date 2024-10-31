<?php

class berqDivi {
    public $isDivi = null;

    function __construct() {
        add_filter('berqwp_before_script_optimization', [$this, 'moveScript']);
        add_filter('berqwp_before_script_optimization', [$this, 'addScript']);
        add_filter('berqwp_before_script_optimization', [$this, 'excludeAnimationDataScript']);
        add_filter('berqwp_buffer_before_closing_body', [$this, 'diviAnimations']);
    }

    function moveScript($buffer) {
        $achorScript = '/themes/Divi/includes/builder/feature/dynamic-assets/assets/js/magnific-popup.js';
        $appendMarkerMatch = '/themes/Divi/js/scripts.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }


        // Divi map not loading issue
        $achorScript = 'maps.googleapis.com/maps/api/js';
        $appendMarkerMatch = '/wp-content/themes/Divi/js/scripts.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        /**
         * Ensure scripts are in right sequence
         * Fix BG video not loading issue with Divi
         * Later on load Divi modules after dely JS is loaded
         */

        $achorScript = '/wp-includes/js/mediaelement/wp-mediaelement.min.js';
        $appendMarkerMatch = '/wp-content/themes/Divi/js/scripts.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        $achorScript = 'id="mediaelement-js-extra"';
        $appendMarkerMatch = '/wp-content/themes/Divi/js/scripts.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        $achorScript = '/wp-includes/js/mediaelement/mediaelement-migrate.min.js';
        $appendMarkerMatch = '/wp-content/themes/Divi/js/scripts.min.js';

        if (strpos($buffer, $achorScript) !== false && strpos($buffer, $appendMarkerMatch) !== false) {
            $scriptSeqManipulate = new scriptSeqManipulate();
            $scriptSeqManipulate->setBuffer($buffer);
            $scriptSeqManipulate->appendBefore($achorScript, $appendMarkerMatch);
            $buffer = $scriptSeqManipulate->getBuffer();
            $scriptSeqManipulate->clean();
            unset($scriptSeqManipulate);
        }

        $achorScript = '/wp-includes/js/mediaelement/mediaelement-and-player.min.js';
        $appendMarkerMatch = '/wp-content/themes/Divi/js/scripts.min.js';

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

    function excludeAnimationDataScript($buffer) {
        $scriptMark = 'var et_animation_data';

        if (strpos($buffer, $scriptMark) !== false) {
            $buffer = $this->excludeScript($scriptMark, $buffer);
        }

        return $buffer;
    }

    function excludeScript($scriptMatch, $buffer) {
        $html = str_get_html($buffer);
        $scriptTags = $html->find('script');

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $scriptMatch) !== false) {
                $script->setAttribute('data-berqwp', '1');
            }
        }

        $buffer = $html->save();
        $html->clear();
        unset($html);

        return $buffer;
    }

    function addScript($buffer) {

        // Initialize Divi modules after JS loaded

        // Detect Divi theme
        if (strpos($buffer, '/wp-content/themes/Divi/js/scripts.min.js') !== false) {
            $this->isDivi = true;

            $script = "<script>
            window.addEventListener('berqwp_after_delay_js_loaded', function() {
                if (window.et_pb_init_modules) {
                    window.et_pb_init_modules();
                }
            });
            </script>";

            $buffer = str_replace('</body>', $script . '</body>', $buffer);
        }

        return $buffer;
    }

    function diviAnimations($scripts) {
        if ($this->isDivi) {

            $script = "<script id='bwp-divi-animations' data-berqwp defer>
            (function() {
                let animationsLoaded = false;
                document.addEventListener('DOMContentLoaded', function () {
                    if (animationsLoaded) {
                        return;
                    }
                    animationsLoaded = true;

                    let animationHandler = {
                        // Get a list of all animation classes
                        getAnimationClasses: function () {
                            return [
                                'et_animated', 'et-waypoint', 'animated', 'animating', 'infinite', 'fade', 'fadeTop', 'fadeRight', 
                                'fadeBottom', 'fadeLeft', 'slide', 'slideTop', 'slideRight', 
                                'slideBottom', 'slideLeft', 'bounce', 'bounceTop', 'bounceRight', 
                                'bounceBottom', 'bounceLeft', 'zoom', 'zoomTop', 'zoomRight', 
                                'zoomBottom', 'zoomLeft', 'flip', 'flipTop', 'flipRight', 
                                'flipBottom', 'flipLeft', 'fold', 'foldTop', 'foldRight', 
                                'foldBottom', 'foldLeft', 'roll', 'rollTop', 'rollRight', 
                                'rollBottom', 'rollLeft'
                            ];
                        },
        
                        // Remove animation from element
                        removeAnimation: function (element) {
                            if (!element.classList.contains('infinite')) {
                                const animationClasses = this.getAnimationClasses();
                                element.classList.remove(...animationClasses);
                                element.style.animationDelay = '';
                                element.style.animationDuration = '';
                                element.style.animationTimingFunction = '';
                                element.style.opacity = '';
                                element.style.transform = '';
                                element.classList.add('had_animation');
                            }
                        },
        
                        // Remove data attributes related to animations
                        removeAnimationData: function (element) {
                            Array.from(element.attributes).forEach(attr => {
                                if (attr.name.startsWith('data-animation-')) {
                                    element.removeAttribute(attr.name);
                                }
                            });
                        },
        
                        // Process animation intensity based on type and direction
                        processAnimationIntensity: function (type, direction, intensity) {
                            let styles = {};
                            const intensityValue = intensity * 2;
        
                            switch (type) {
                                case 'slide':
                                    if (direction === 'top') {
                                        styles.transform = 'translate3d(0, '+intensityValue+'%, 0)';
                                    } else if (direction === 'right') {
                                        styles.transform = 'translate3d('+intensityValue+'%, 0, 0)';
                                    } else if (direction === 'bottom') {
                                        styles.transform = 'translate3d(0, '+intensityValue+'%, 0)';
                                    } else if (direction === 'left') {
                                        styles.transform = 'translate3d('+intensityValue+'%, 0, 0)';
                                    }
                                    break;
                                case 'zoom':
                                    const scaleValue = 1 - (intensity / 100);
                                    styles.transform = 'scale('+scaleValue+')';
                                    break;
                                case 'flip':
                                    const angle = Math.ceil(0.9 * intensity);
                                    if (direction === 'right') {
                                        styles.transform = 'perspective(2000px) rotateY('+angle+'deg)';
                                    } else if (direction === 'left') {
                                        styles.transform = 'perspective(2000px) rotateY('+angle+'deg)';
                                    } else if (direction === 'top') {
                                        styles.transform = 'perspective(2000px) rotateX('+angle+'deg)';
                                    } else if (direction === 'bottom') {
                                        styles.transform = 'perspective(2000px) rotateX('+angle+'deg)';
                                    }
                                    break;
                                // Other cases like 'fold' and 'roll' can be added here
                            }
                            return styles;
                        },
        
                        // Apply animation to an element based on data attributes
                        animateElement: function (element, animationData) {
                            if (!element.classList.contains('et_had_animation')) {
                                const { style, duration, delay, intensity, speed_curve, starting_opacity } = animationData;
                                
                                // Ensure the animation class is valid
                                const validAnimationClasses = this.getAnimationClasses();
                                const matchedAnimationClass = validAnimationClasses.find(animClass => style.includes(animClass));
        
                                if (matchedAnimationClass) {
                                    element.style.animationDuration = duration;
                                    element.style.animationDelay = delay;
                                    element.style.animationTimingFunction = speed_curve;
                                    element.style.opacity = starting_opacity;
        
                                    const animationType = style.replace(/[^a-z]/gi, '');
                                    const animationDirection = style.match(/[a-z]+/i)[0];
                                    const intensityValue = parseInt(intensity, 10);
        
                                    // Set styles based on animation
                                    const intensityStyles = this.processAnimationIntensity(animationType, animationDirection, intensityValue);
                                    Object.assign(element.style, intensityStyles);
        
                                    element.classList.add('et_animated', 'et_is_animating', matchedAnimationClass);
                                    element.classList.add('et_had_animation');
        
                                    // Remove animation after it's done
                                    setTimeout(() => {
                                        // this.removeAnimation(element);
                                    }, parseInt(duration) + parseInt(delay));
                                }
                            }
                        },
        
                        addEtAnimatedClassToElements: function () {
                            const animationClasses = this.getAnimationClasses();
        
                            // Build a CSS selector string that targets any element with these classes
                            const selector = animationClasses.map(className => '.'+className).join(',');
        
                            // Query all elements that match the selector (have any of the animation classes)
                            const elements = document.querySelectorAll(selector);
        
                            // Add 'et-animated' class to each of them
                            elements.forEach(element => {
                                element.classList.add('et-animated');
                            });
                        },

                        // Loop through animation data and apply animations
                        processAnimationData: function (animationDataArray) {
                            animationDataArray.forEach(animationData => {
                                const elements = document.getElementsByClassName(animationData.class);
                                Array.from(elements).forEach(element => {
                                    this.animateElement(element, animationData);
                                });
                            });
                        }
                    };
    
                    animationHandler.processAnimationData(et_animation_data);
                    animationHandler.addEtAnimatedClassToElements();
                
                })
                                
            
            })()
            
            </script>";

            $scripts .= $script;
        }

        return $scripts;
    }
}
new berqDivi();