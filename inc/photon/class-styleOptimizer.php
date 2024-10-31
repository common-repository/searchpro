<?php
if (!defined('ABSPATH')) exit;

class berqStyleOptimizer
{
    public $loading = 'delay';
    public $noscript = '';

    function set_loading($loading)
    {
        $this->loading = $loading;
    }

    function run_optimization($photonClass, $buffer)
    {
        /* if ($this->loading == 'default') { */
        /*     return $buffer; */
        /* } */

        // Load HTML content using Simple HTML DOM
        $html = str_get_html($buffer);

        // Process <link> tags with rel="stylesheet"
        foreach ($html->find('link[rel=stylesheet], link[rel=preload], link[rel="preload stylesheet"]') as $linkNode) {

            // Delay and preload use javascript to load CSS
            if ($this->loading == 'delay' || $this->loading == 'preload') {
                $this->noscript = $this->noscript.$linkNode->outertext;
            }

            // Skip if the link tag already has data-berqwp attribute
            if ($linkNode->hasAttribute('data-berqwp')) {
                continue;
            }

            $href = trim($linkNode->href);
            $excluded = false;

            // if (str_contains($href, 'http://')) {
            //     $href = str_replace('http://', 'https://', $href);
            // }

            // If tag has a match for exclude url list
            if (!empty($photonClass->js_css_exclude_urls)) {
                foreach ($photonClass->js_css_exclude_urls as $js_exclude_keyword) {
                    if (!empty($js_exclude_keyword)) {
						/* error_log("$href - $js_exclude_keyword"); */
                        if (strpos($href, trim($js_exclude_keyword)) !== false) {
                            $excluded = true;
                            break;
                        }
                    }
                }
            }

            if ($excluded) {
                continue;
            }



            if ($linkNode->rel == 'preload' && (!$linkNode->hasAttribute('as') || !$linkNode->hasAttribute('onload') || $linkNode->as !== 'style')) {
                continue;
            }


            if ($photonClass->use_cdn) {

                $kw_found = false;

                foreach ($photonClass->css_excluded_keywords as $keyword) {
                    if (stripos($href, $keyword) !== false) {
                        $kw_found = true;
                    }
                }
                // Check if the script source contains any excluded keywords
                if (!$kw_found) {
                    // Send the file URL to CDN as GET parameters
                    /* $cdnUrl = 'https://cdn.berqwp.com/'; */

                    global $berqCDN;
                    $berqCDN->add_file_in_queue($href);
                    // $cdnUrl = 'https://boost.berqwp.com/photon/cdn/';
                    // $cdnUrl .= '?url=' . urlencode($href);
                    // $cdnUrl .= '&domain=' . $photonClass->domain;

                    // $photonClass->add_into_cdn_queue($cdnUrl, $href);

                }
            }

			if ($this->loading == 'default') {
				continue;
			}

            // Process or modify <link> tags as needed
            $linkNode->setAttribute('data-berqwp-style-href', esc_attr($href));
            $linkNode->href = '';

            unset($linkNode);
        }

        // Process <style> tags
        foreach ($html->find('style') as $styleNode) {

            // Skip if the style tag already has data-berqwp-style attribute
            if ($styleNode->hasAttribute('data-berqwp')) {
                continue;
            }

            // Delay and preload use javascript to load CSS
            if ($this->loading == 'delay' || $this->loading == 'preload') {
                $this->noscript = $this->noscript.$styleNode->outertext;
            }

			if ($photonClass->use_cdn) {
				$baseUrl = bwp_getBaseUrl($photonClass->site_url);
				$styleContent = $styleNode->innertext;
				$matches = bwp_extractUrlsFromCss($styleContent);

				foreach ($matches as $url) {
					$absolute_url = bwp_rel2abs($url, $baseUrl);

                    global $berqCDN;
                    $berqCDN->add_file_in_queue($absolute_url);
                    
					// $cdn_url = "https://boost.berqwp.com/photon/cdn/?url=".urlencode($absolute_url)."&domain=$photonClass->domain";
                    // $photonClass->add_into_cdn_queue($cdn_url, $absolute_url);
				}
			
			}

			if ($this->loading == 'default') {
				continue;
			}

			// Commented to let style tags load initially - 26 Jun 2024
            // Process or modify <style> tags as needed
            $styleContent = $styleNode->innertext;
            // var_dump($styleContent);
			$nofontface = preg_replace('/@font-face\s*{[^}]+}/i', '', $styleContent);
            $styleNode->innertext = $nofontface;
            $styleNode->setAttribute('data-berqwp-style', esc_attr($styleContent));

            unset($styleNode);
        }

        // Output the modified HTML
        $buffer = $html->save();
		$html->clear();
		unset($html);

        add_filter('berqwp_buffer_before_closing_body', [$this, 'script']);

        // CSS compatibility for javascript disabled
        add_filter('berqwp_buffer_before_closing_body', [$this, 'noscript']);

        unset($photonClass);

        return $buffer;

    }

    function preload_script()
    {
        $script = "
        <script id='preload-styles' defer>
        let initialized_style_preload = false;

        window.addEventListener('load', function () {
            if (initialized_style_preload) {
                return;
            }

            initialized_style_preload = true;

            let berqwp_styles = [];
            let berqwp_linkTags = document.querySelectorAll('link[data-berqwp-style-href]');
            
            // Iterate through each link tag
            berqwp_linkTags.forEach(function (linkTag, index) {
                berqwp_styles.push(linkTag.getAttribute('data-berqwp-style-href'))
            });

            (async () => {

                berqwp_preload_css();

            })();

        });


			function BerqWPcacheResource(resourceUrl) {
				return caches.open('berqwp-cache').then(cache => {
					return fetch(resourceUrl).then(response => {
						return cache.put(resourceUrl, response);
					});
				});
			}

			    function berqwp_preload_css() {
                    // Get all link tags containing data-berqwp-style-href
                    let berqwp_linkTags = document.querySelectorAll('link[data-berqwp-style-href]');

                    // Iterate through each link tag
                    berqwp_linkTags.forEach(function (linkTag, index) {
                        // Set the href attribute of each link tag
                        linkTag.setAttribute('href', linkTag.getAttribute('data-berqwp-style-href'));

                    });

                    // Get all style tags containing data-berqwp-style
                    var berqwp_styleTags = document.querySelectorAll('style[data-berqwp-style]');

                    // Loop through each style tag
                    berqwp_styleTags.forEach(function (styleTag) {
                        // Get the value of data-berqwp-style
                        var berqwpStyle = styleTag.getAttribute('data-berqwp-style');

                        // Set the content of the style tag with the value of data-berqwp-style
                        styleTag.textContent = berqwpStyle;
                    });

                    // Trigger event when styles are loaded
                    let berqwp_styles_event = new CustomEvent('berqwpStylesLoaded');
        
                    // Dispatch the custom event
                    window.dispatchEvent(berqwp_styles_event);

                
                }

        </script>
        ";

        return $script;

    }

    function delay_script()
    {
        $script = "
        <script id='delay-styles' defer>

			function BerqWPcacheResource(resourceUrl) {
				return caches.open('berqwp-cache').then(cache => {
					return fetch(resourceUrl).then(response => {
						return cache.put(resourceUrl, response);
					});
				});
			}

			    function berqwp_css_handleUserInteraction() {
                    // Get all link tags containing data-berqwp-style-href
                    let berqwp_linkTags = document.querySelectorAll('link[data-berqwp-style-href]');

                    // Iterate through each link tag
                    berqwp_linkTags.forEach(function (linkTag, index) {
                        // Set the href attribute of each link tag
                        linkTag.setAttribute('href', linkTag.getAttribute('data-berqwp-style-href'));

                    });

                    // Get all style tags containing data-berqwp-style
                    var berqwp_styleTags = document.querySelectorAll('style[data-berqwp-style]');

                    // Loop through each style tag
                    berqwp_styleTags.forEach(function (styleTag) {
                        // Get the value of data-berqwp-style
                        var berqwpStyle = styleTag.getAttribute('data-berqwp-style');

                        // Set the content of the style tag with the value of data-berqwp-style
                        styleTag.textContent = berqwpStyle;
                    });

                    // Trigger event when styles are loaded
                    let berqwp_styles_event = new CustomEvent('berqwpStylesLoaded');
        
                    // Dispatch the custom event
                    window.dispatchEvent(berqwp_styles_event);


                    // After running the function, remove all event listeners to ensure it runs only once
                    for (let eventType of berqwp_css_interactionEventTypes) {
                        document.removeEventListener(eventType, berqwp_css_handleUserInteraction);
                    }
                
                }

                const berqwp_css_interactionEventTypes = ['click', 'mousemove', 'keydown', 'touchstart', 'scroll'];

                for (let eventType of berqwp_css_interactionEventTypes) {
                    document.addEventListener(eventType, berqwp_css_handleUserInteraction);
                }

        </script>
        ";

        return $script;

    }

    function script($script_html)
    {
        if ($this->loading == 'delay') {
            $script_html .= $this->delay_script();
        }

        if ($this->loading == 'preload') {
            $script_html .= $this->preload_script();
        }

        return $script_html;
    }

    function noscript($script_html)
    {
        $script_html .= "<noscript>$this->noscript</noscript>";

        return $script_html;
    }

}
