<?php
if (!defined('ABSPATH')) exit;

class berqBufferOptimize {
    public $external_js_excluded_keywords = [
        'googleadservices',
        'pagead2.googlesyndication.com',
        'mediavine',
        'googletagmanager.com/gtag',
        'google.com/recaptcha',
        'platform.illow.io/banner.js',
        'code.etracker.com',
        'cdn.trustindex.io/loader.js',
        'static.getclicky.com',
        'form.jotform.com',
        'bunny.net',
        '.exactdn.com/',
        'maps.googleapis.com',
        'js.stripe.com',
        '.pressablecdn.com',
        '.cdn77.net',
        '.cloudflare.com',
        '.fastly.net',
        '.gcorelabs.com',
        '.googleapis.com',
        '.cloudfront.net',
        '.b-cdn.net',
        '.cachefly.net',
        '.imagekit.io',
        '.kxcdn.com',
        '.stackpathcdn.com',
        '.vo.msecnd.net',
        '.akamaihd.net',
        '.incapsula.com',
        '.sucuricdn.net',
        '.incapsula.com',
        '.cdn.meta.net',
        '.jet-stream.com',
        '.cachefly.net',
        '.cdnetworks.com',
        'amazon-adsystem.com',
        'adsbygoogle',
        'google-analytics.com',
        'maps.google.com',
        'recaptcha',
        'js.hsforms.net',
        'hbspt.forms.create',
        'ad-manager.min.js',
        'fast.wistia.com',
    ];

    public $css_excluded_keywords = [
        'bunny.net',
        '.exactdn.com/',
        'fonts.googleapis.com/css',
        '.pressablecdn.com',
        '.cdn77.net',
        '.cloudflare.com',
        '.fastly.net',
        '.gcorelabs.com',
        '.googleapis.com',
        '.cloudfront.net',
        '.b-cdn.net',
        '.cachefly.net',
        '.imagekit.io',
        '.kxcdn.com',
        '.stackpathcdn.com',
        '.vo.msecnd.net',
        '.akamaihd.net',
        '.incapsula.com',
        '.sucuricdn.net',
        '.incapsula.com',
        '.cdn.meta.net',
        '.jet-stream.com',
        '.cachefly.net',
        '.cdnetworks.com',
    ];

    public $img_excluded_keywords = [
        'i0.wp.com',
        '.exactdn.com/',
        '.smushcdn.com',
        '.b-cdn.net',
        '.pressablecdn.com',
        '.cdn77.net',
        '.cloudflare.com',
        '.fastly.net',
        '.gcorelabs.com',
        '.googleapis.com',
        '.cloudfront.net',
        '.b-cdn.net',
        '.cachefly.net',
        '.imagekit.io',
        '.kxcdn.com',
        '.stackpathcdn.com',
        '.vo.msecnd.net',
        '.akamaihd.net',
        '.incapsula.com',
        '.sucuricdn.net',
        '.incapsula.com',
        '.cdn.meta.net',
        '.jet-stream.com',
        '.cachefly.net',
        '.cdnetworks.com',
    ];

    public $skipOnLoadJS = [
        'googletagmanager.com/gtag', 
        'cdn-cookieyes.com/client_data', 
        'static.getclicky.com', 
        'clarity.ms/', 
        'google.com', 
        'doubleclick.net', 
        'stats.wp.com', 
        '/elementor/', 
        '/elementor-pro', 
        'sp-scripts.min.js',
        '/woocommerce-products-filter/',
    ];

    public $optimization_mode = null;
    public $js_css_exclude_urls = null;
    public $use_cdn = false;
    public $buffer = null;
    public $image_lazy_loading = null;
    public $lazy_load_youtube = null;
    public $js_mode = null;

    function __construct() {

        $optimization_mode = get_option('berq_opt_mode');

        if ($optimization_mode == 4) {
            $optimization_mode = 'aggressive';
        } elseif ($optimization_mode == 3) {
            $optimization_mode = 'blaze';
        } elseif ($optimization_mode == 2) {
            $optimization_mode = 'medium';
        } elseif ($optimization_mode == 1) {
            $optimization_mode = 'basic';
        }

        $this->optimization_mode = $optimization_mode;
        $this->js_css_exclude_urls = get_option('berq_exclude_js_css');
        $this->image_lazy_loading = get_option('berqwp_image_lazyloading');
        $this->lazy_load_youtube = get_option('berqwp_lazyload_youtube_embed');
        $this->js_mode = get_option('berqwp_javascript_execution_mode');
        // $this->use_cdn = get_option('berqwp_enable_cdn');

        if (berq_is_localhost()) {
            $this->use_cdn = null;
        }

    }

    function optimize_buffer($buffer, $page_slug) {

        // if (!berq_is_localhost()) {
        //     $berqCriticalCSS = new berqCriticalCSS();
        //     $criticalcss = $berqCriticalCSS->get_css($page_slug);

        //     if (!empty($criticalcss)) {
        //         $buffer = $this->css_optimize($buffer, true, false);
        //         $buffer = $berqCriticalCSS->add_css_to_buffer($buffer, $criticalcss);
        //     } else {
        //         $buffer = $this->css_optimize($buffer);
        //     }
        // }

        $buffer = $this->lazyload_iframes($buffer);
        $buffer = $this->js_optimize($buffer);
        $buffer = $this->optimize_images($buffer);

        // global $berqCDN;
        // $buffer = $berqCDN->finish_queue($buffer, $page_slug);

        $script = "
            <script data-berqwp defer>
                var comment = document.createComment(' This website is optimized using the BerqWP plugin. @".time()." ');
                document.documentElement.insertBefore(comment, document.documentElement.firstChild);

                function isMobileDevice() {
                    return /Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
                }

                function astraHeaderClass() {
                    if (isMobileDevice() && window.screen.width <= 999) {
                        if (document.body.classList.contains('theme-astra') && !document.body.classList.contains('ast-header-break-point')) {
                            document.body.classList.add('ast-header-break-point');
                        }
                    }
                }

                function divimobilemenu() {
                    const divimenuele = document.querySelector('#et_mobile_nav_menu .mobile_menu_bar.mobile_menu_bar_toggle');

                    if (isMobileDevice() && divimenuele) {
                        divimenuele.innerHTML = '<div class=\"dipi_hamburger hamburger hamburger--spring\">     <div class=\"hamburger-box\">         <div class=\"hamburger-inner\"></div>     </div> </div>';
                    }
                }

                divimobilemenu();
                astraHeaderClass();
                window.addEventListener('resize', function() {
                    astraHeaderClass();
                });

                window.dispatchEvent(new Event('berqwp_js_initialized'));

            </script>

            <script data-berqwp>
            async function berqwp_add_assets_browser_cache(asset_urls) {
                if (!('caches' in window)) {
                    console.error('Cache API is not supported in this browser.');
                    return;
                }

                const fetchPromises = [];

                asset_urls.forEach(url => {
                    if (url) {
                        fetchPromises.push(
                            fetch(url, {
                                method: 'GET',
                                mode: 'cors' // Ensure CORS mode is set
                            })
                            .then(response => {
                                if (!response.ok) {
                                    console.error('Failed to fetch:', response);
                                }
                                return response;
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                return null;
                            })
                        );
                    }
                });

                try {
                    const responses = await Promise.all(fetchPromises);

                    // Open or create a cache
                    const cache = await caches.open('berqwp-assets-cache');

                    responses.forEach(response => {
                        if (response && response.ok) {
                            console.log('Preloading and caching:', response.url);

                            // Add the response to the cache
                            cache.put(response.url, response.clone());
                        }
                    });
                } catch (error) {
                    console.error('Error during fetching and caching:', error);
                }
            }

            </script>

        <script data-berqwp id='prefetch-links' defer>
            // Set to keep track of prefetched links
            const prefetchedLinks = new Set();
            
            // Get all anchor tags on the page
            const links = document.querySelectorAll('a');
            
            // Loop through each anchor tag
            links.forEach(link => {
                // Add mouseover event listener
                link.addEventListener('mouseover', () => {
                    // Check if the link has already been prefetched
                    if (!prefetchedLinks.has(link.href)) {
                        // Create a link element
                        const prefetchLink = document.createElement('link');
                        prefetchLink.rel = 'prefetch';
                        prefetchLink.href = link.href;
                        
                        // Append the link element to the head of the document
                        document.head.appendChild(prefetchLink);
                        
                        // Add the link to the set of prefetched links
                        prefetchedLinks.add(link.href);
                    }
                });
            });
        
        </script>
        ";

        $preload = '';
        $preload .= "
        <script data-berqwp aync>
                var berqwp_readyState = 'loading';
                var isDispatchingEvent = false; // Track if event is being dispatched

                // Define a custom readyState property on the document
                Object.defineProperty(document, 'readyState', {
                    get() { return berqwp_readyState; }
                });

                // Function to manually dispatch the 'readystatechange' event
                function triggerReadyStateChange(newState) {
                    if (!isDispatchingEvent) {
                        try {
                            isDispatchingEvent = true; // Set flag to indicate event is being dispatched
                            berqwp_readyState = newState; // Update the custom readyState
                            var event = new Event('readystatechange'); // Create a new 'readystatechange' event
                            document.dispatchEvent(event); // Dispatch the event
                        } finally {
                            isDispatchingEvent = false; // Reset flag after dispatch
                        }
                    } else {
                        console.warn('Event dispatch in progress. Skipping duplicate dispatch.');
                    }
                }
            </script>
        ";
        if ($this->image_lazy_loading) {
            $preload .= "
             <script data-berqwp id='berqwp-img-lazyload' async>
                document.addEventListener('DOMContentLoaded', function () {
                    var berq_img_lazy_options = {
                        root: null, // null means the viewport
                        rootMargin: '200px',
                        threshold: 0
                    };
                    
                    var img_observer = new IntersectionObserver(function (entries, observer) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                let img = entry.target;
                                let imgSrc = img.getAttribute('data-berqwpsrc');
                                let imgSrcset = img.getAttribute('data-berqwp-srcset');
                                
                                // Set the actual image source from data-berqwpsrc
                                if (imgSrc !== null) {
                                    img.src = imgSrc;
                                }

                                if (imgSrcset !== null) {
                                    img.srcset = imgSrcset;
                                }

                                if (img.getAttribute('data-srcset') !== null) {
                                    img.srcset = img.getAttribute('data-srcset');
                                }
                                
                                // You might want to remove the data-src attribute after loading
                                img.removeAttribute('data-berqwpsrc');
                                img.removeAttribute('data-berqwp-srcset');
                
                                img_observer.unobserve(img);
                            }
                        });
                    }, berq_img_lazy_options);

                    function berqwp_lazyload_images() {
                        let lazyImages = document.querySelectorAll('img[data-berqwpsrc]');
                        lazy_img_int = 1000;
                        
                        lazyImages.forEach(function (img) {
                            img_observer.observe(img);
                        });
                    }
                    
                    berqwp_lazyload_images();
                    setInterval(berqwp_lazyload_images, 1000);
                    
                });

                document.addEventListener('DOMContentLoaded', function () {

                    // Set up IntersectionObserver to lazy load background images
                    let observer = new IntersectionObserver((entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                // Get the element that entered the viewport
                                const element = entry.target;

                                // Get the background image from the data-bg attribute
                                const bgImage = element.getAttribute('data-berqwpbg');
                                
                                // If there's a data-bg attribute, set the background image
                                if (bgImage) {
                                    element.style.backgroundImage = 'url('+bgImage+')';
                                    element.removeAttribute('data-berqwpbg'); // Remove the data-bg attribute after loading
                                }

                                // Stop observing the element once it's loaded
                                observer.unobserve(element);
                            }
                        });
                    });

                    function berqwp_lazyload_bg_images() {
                        const lazyElements = document.querySelectorAll('.lazy-berqwpbg');

                        // Observe each lazy-loaded element
                        lazyElements.forEach(element => {
                            observer.observe(element);
                        });
                    }
                    
                    berqwp_lazyload_bg_images();
                    setInterval(berqwp_lazyload_bg_images, 1000);

                });
            
            </script>

            <script data-berqwp >
                
                // Get the style tag element
                const styleTag = document.getElementById('berqwp-critical-css');

                if (styleTag) {
                    // Extract the CSS content from the style tag
                    const cssContent = styleTag.textContent;
                    
                    // Regular expression to match CSS rules
                    const ruleRegex = /([^{}]+)\{([^{}]+)\}/g;
                    
                    // Regular expression to match background or background-image properties with url() function
                    const backgroundRegex = /(?:background(?:-image)?:[^;]*?url\([^)]*\))/g;
                    
                    // Array to store the extracted CSS properties
                    var extractedProperties = [];
                    
                    // Match CSS rules in the CSS content
                    let ruleMatch;
                    while ((ruleMatch = ruleRegex.exec(cssContent)) !== null) {
                        const cssBlockSelector = ruleMatch[1].trim();
                        const cssProperties = ruleMatch[2];
                    
                        // Match background properties in the CSS properties
                        let backgroundMatch;
                        while ((backgroundMatch = backgroundRegex.exec(cssProperties)) !== null) {
                            let prop_value = backgroundMatch[0].trim().replace('background:', 'background-image:');
    
                            extractedProperties.push({ cssBlockSelector, backgroundValue: prop_value });
                            /* extractedProperties.push({ cssBlockSelector, backgroundValue: backgroundMatch[0].trim() }); */
                        }
                    }
                    
                    // console.log(extractedProperties);
                    
                    // Iterate through the extracted properties and modify background images directly in the style tag
                    let modifiedCssContent = cssContent;
                    extractedProperties.forEach(property => {
    
                        // Replace the original background value with 'none'
                        let bg_img_hash = btoa(property.backgroundValue);
                        const regex = /url\([^)]*\)/;
                        // const regex = /url\(([^)]*)\)/; // fix bg image error
    
    
                        // Replace the URL function with the new URL
                        /* const prop_value = property.backgroundValue.replace(regex, 'urlbgberq(#'+bg_img_hash+')'); */
                        // const prop_value = property.backgroundValue.replace(regex, 'url(data:image/svg+xml;base64,'+bg_img_hash+')');
                        const prop_value = property.backgroundValue.replace(regex, 'unset');
                        // let lcp_exists = lcp_ele.some(element => property.backgroundValue.includes(element));
                        let lcp_exists = false;
                        
                        if (lcp_exists) {
                            modifiedCssContent = modifiedCssContent.replace(property.backgroundValue, property.backgroundValue+' !important;');
                            // modifiedCssContent = modifiedCssContent.replace(property.backgroundValue, prop_value);
                        } else {
                            modifiedCssContent = modifiedCssContent.replace(property.backgroundValue, prop_value);
    
                        }
    
                    });
                    
                    // Update the text content of the style tag with the modified CSS content
                    styleTag.textContent = modifiedCssContent;
                }
                
                   
            </script>
            ";
        }

        $preload .= "
        <script id='load-early-js' async>
                (function(){
                                let scriptsLoaded = false;
                window.addEventListener('DOMContentLoaded', function () {

                                if (scriptsLoaded) {
                                    return;
                                }

                                scriptsLoaded = true;

                    var scripts = document.querySelectorAll('script[data-earlyberqwp=\"1\"][type=\"text/bwp-script\"]');
                    
            
                        // Function to dynamically load scripts
                        async function loadScript(index) {
                            if (index >= scripts.length) {
            
                                // setTimeout(function() {
                                    // After all scripts are loaded, dispatch events
                                    let event = new Event('DOMContentLoaded', {
                                        bubbles: true,
                                        cancelable: true
                                    });
                                    document.dispatchEvent(event);
            
                                    window.dispatchEvent(new Event('load'));
            
                                    triggerReadyStateChange('complete');
            
                                    // Create a new resize event
                                    var resizeEvent = new Event('resize');
            
                                    // Dispatch the resize event
                                    window.dispatchEvent(resizeEvent);
            
                                    console.log('scripts early loaded.')
                                
                                // }, 1000)
                                return;
                            }
            
                            // Create a new script element
                            var script = scripts[index];
                            var newScript = document.createElement('script');
                            // newScript.type = 'text/javascript';
                            newScript.type = script.getAttribute('data-type');
            
                            // Copy the content or src of the original script
                            if (script.src) {
                                newScript.src = script.src;
            
                                if (script.hasAttribute('data-berqwpskiponload')) {
                                    loadScript(index + 1);
                                } else {
            
                                    // Set a timeout to proceed even if onload doesn't fire
                                    var scriptTimeout = setTimeout(function() {
                                        console.warn('Script load timeout:', script.src);
                                        loadScript(index + 1);
                                    }, 5000); // 5 seconds timeout
            
                                    
                                    newScript.onload = function() {
                                        clearTimeout(scriptTimeout); // Clear timeout if script loads successfully
                                        loadScript(index + 1);
                                    };
            
                                    newScript.onerror = function() {
                                        clearTimeout(scriptTimeout); // Clear timeout if there's an error loading the script
                                        console.warn('Error loading script:', script.src);
                                        loadScript(index + 1); // Proceed to the next script
                                    };
                                }
                                
            
                            } else {
                                newScript.text = script.textContent;
                                setTimeout(function() {
                                    loadScript(index + 1);
                                }, 0); // Delay to simulate async load
                            }
            
                            // Copy other attributes if necessary
                            Array.from(script.attributes).forEach(function(attr) {
                                if (attr.name !== 'type' && attr.name !== 'src') {
                                    newScript.setAttribute(attr.name, attr.value);
                                }
                            });
            
                            // Replace the old script with the new script
                            script.parentNode.replaceChild(newScript, script);
                        }
            
                        (async () => {
                            // triggerReadyStateChange('complete');
            
                            // Start loading scripts from the first one
                            loadScript(0);
                            
                        })();
                
                })
                })()
                </script>
        ";

        if ($this->lazy_load_youtube == 1) {

            $script .= "
             <script data-berqwp defer>
                // document.addEventListener('DOMContentLoaded', function () {
                    var options = {
                        root: null, // null means the viewport
                        rootMargin: '0px', // adjust as needed
                        threshold: 0.1 // adjust as needed
                    };
                    
                    var observer = new IntersectionObserver(function (entries, observer) {
                        entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            let item = entry.target;
                            let iframe = item.getAttribute('data-embed');
                            
                            let wrapper = document.createElement('div');
                            wrapper.innerHTML = iframe;
                            let iframeElement = wrapper.firstChild;

                            // Insert the iframe next to the item
                            item.insertAdjacentElement('afterend', iframeElement);

                            // Remove the original item
                            item.remove();

                            observer.unobserve(item);
                        }
                    });
                }, options);
                
                    let yt_em = document.querySelectorAll('.berqwp-lazy-youtube');
                    yt_em.forEach(function (item) {
                        observer.observe(item);
                    });
                // });
            </script>
            ";
        }

        $afterHead = apply_filters( 'berqwp_buffer_after_head_open', '' );
        $buffer = preg_replace('/<head(\s[^>]*)?>/i', '<head$1>' . $afterHead, $buffer);

        $beforeHeadClosing = apply_filters( 'berqwp_buffer_before_closing_head', $preload );
        $buffer = str_replace('</head>', $beforeHeadClosing . '</head>', $buffer);

        $beforeBodyClose = apply_filters( 'berqwp_buffer_before_closing_body', $script );
        $buffer = str_replace('</body>', $beforeBodyClose . '</body>', $buffer);

        $this->buffer = $buffer;

        add_filter( 'berqwp_cache_buffer', [$this, 'update_buffer'] );
    }

    function lazyload_iframes($buffer) {
        // Lazyload YouTube embeds
        if ($this->lazy_load_youtube == 1) {
            $buffer = preg_replace_callback(
                '/<iframe(.*?)<\/iframe>/s',
                function ($matches) {
                    $attrs = str_replace('"', '\'', $matches[1]);
                    return '<div class="berqwp-lazy-youtube" data-embed="<iframe' . $attrs . '</iframe>"></div>';
                },
                $buffer
            );
        }
        return $buffer;
    }

    function update_buffer($buffer) {
        return $this->buffer;
    }

    function css_optimize($buffer, $disableCDN = null, $forceDefault = true) {

        $currentCDNstat = $this->use_cdn;

        if ($disableCDN) {
            $this->use_cdn = false;
        }

        // CSS optimization
        $styleOptimizer = new berqStyleOptimizer();

        if ($forceDefault) {
            $styleOptimizer->set_loading('default');
        } else {
            if ($this->optimization_mode == 'blaze' || $this->optimization_mode == 'medium') {
                $styleOptimizer->set_loading('preload');
            }
            
            if ($this->optimization_mode == 'basic') {
                $styleOptimizer->set_loading('default');
            }
        }


        $buffer = $styleOptimizer->run_optimization($this, $buffer);
        $this->use_cdn = $currentCDNstat;

        return $buffer;
    }

    function js_optimize($buffer) {
        // JavaScript optimization
        $scriptOptimizer = new berqScriptOptimizer();

        if ($this->optimization_mode == 'medium') {
            $scriptOptimizer->set_loading('preload');
        }

        if ($this->optimization_mode == 'basic') {
            $scriptOptimizer->set_loading('default');
        }

        $buffer = apply_filters( 'berqwp_before_script_optimization', $buffer );
        $buffer = $scriptOptimizer->run_optimization($this, $buffer);

        return $buffer;
    }

    function optimize_images($buffer) {
        if ($this->image_lazy_loading) {
            $berqImages = new berqImagesOpt();
            $berqImages->image_lazy_loading = $this->image_lazy_loading;
            $buffer = $berqImages->optimize_images($buffer);
        }
        return $buffer;
    }

    public static function parallelCurlRequests($urls, $postDataArray, $requestMethod = 'POST', $contentType = 'application/x-www-form-urlencoded', $userAgent = 'BerqWP Bot') {
        $curlHandles = [];
        $result = [];
        $requestCounter = 0; // Initialize the counter
        $maxRequestsBeforeSleep = 30;

        // Create cURL handles for each URL
        foreach ($urls as $index => $url) {
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestMethod);

            if ($requestMethod == 'POST') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataArray[$index]);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: ' . $contentType,
                'User-Agent: ' . $userAgent,
            ]);

            // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Add cURL handle to the array
            $curlHandles[$index] = $ch;
        }

        // Initialize multi cURL handler
        $multiHandle = curl_multi_init();

        // Add cURL handles to the multi cURL handler
        foreach ($curlHandles as $ch) {
            curl_multi_add_handle($multiHandle, $ch);
        }

        // Execute all cURL requests simultaneously
        do {
            curl_multi_exec($multiHandle, $running);

            $requestCounter++; // Increment the counter for each request
            if ($requestCounter >= $maxRequestsBeforeSleep) {
                usleep(2000000); // Sleep for 2 seconds (2000000 microseconds)
                $requestCounter = 0; // Reset the counter
            }

        } while ($running > 0);

        // Retrieve the results from each cURL handle
        foreach ($curlHandles as $index => $ch) {
            $result[$index] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($multiHandle, $ch);
        }

        // Close the multi cURL handler
        curl_multi_close($multiHandle);

        return $result;
    }

    function optimize_external_js($tag)
    {
        $src = $this->get_src_from_script($tag);

        if ($src !== false) {
            if (strpos($src, 'http') === 0 || strpos($src, '//') === 0) {
                $parsed_url = parse_url($src);
                if (isset ($parsed_url['host']) && $parsed_url['host'] !== $this->domain) {
                    $kw_found = false;

                    foreach ($this->external_js_excluded_keywords as $keyword) {
                        if (stripos($src, $keyword) !== false) {
                            $kw_found = true;
                        }
                    }

                    if (!$kw_found) {
                        $cache = $this->cache_scripts([$src]);
                        $json_data = json_decode($cache);

                        if ($json_data !== null) {
                            if (isset ($json_data->status) && $json_data->status == 'success') {

                                // var_dump(md5($src));
                                // var_dump($json_data->urls->{md5($src)});
                                // exit;
                                $newSRC = $json_data->urls->{md5($src)};


                                // Create a Simple HTML DOM object
                                $html = str_get_html($tag);

                                // Find all script tags
                                foreach ($html->find('script') as $scriptTag) {
                                    $scriptTag->src = $newSRC;
                                }

                                $tag = $html->save();

                                // Clear Simple HTML DOM object
                                $html->clear();
                                unset($html);

                            }
                        }

                    }

                }


            }
        }

        return $tag;
    }

    function get_src_from_script($html)
    {
        if (empty($html)) {
            return false;
        }
        
        // Create a Simple HTML DOM object
        $html = str_get_html($html);
        
        // Find all script tags
        foreach ($html->find('script') as $scriptTag) {
            $src = $scriptTag->src;
        }
        
        // Clear Simple HTML DOM object
        $html->clear();
        unset($html);
        
        if (empty($src)) {
            return false;
        }
        
        return $src;
    }
}