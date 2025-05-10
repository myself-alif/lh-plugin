<?php

namespace LH\Filters;

class SEO_Tags 
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return SEO_Tags
	 */
	public function __construct() {
        add_action('wp_head', [$this,'wp_head'], -999);
    }

    /**
     * Prints scripts or data in the head tag on the front end.
     *
     * @return void
     */
    public function wp_head() {
        $google_analytics_settings = get_field('lh_google_analytics_settings', 'option');

        if (!empty($google_analytics_settings['add_google_analytics_js_script']) && !empty($google_analytics_settings['tag_id'])) {
    ?>
        <!-- Google Analytics 4 property (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php print $google_analytics_settings['tag_id']; ?>"></script>
        <script type="text/javascript">
            window.dataLayer = window.dataLayer || [];

            function gtag(){dataLayer.push(arguments);}

            gtag('js', new Date());
            gtag('config', '<?php print $google_analytics_settings['tag_id']; ?>');
        </script>
        <!-- Google Analytics 4 property (gtag.js) - END -->
    <?php
        }
    }
}