<?php

namespace LH;

class Setup
{
    static $posts_post_type     = 'post';
    static $categories_taxonomy = 'category';

    /**
	 * Setup action & filter hooks
	 *
	 * @return Setup
	 */
	public function __construct() {
        $this->register_install_uninstall_hooks();
        $this->register_init_hooks();
        
        new Admin_Interfaces();
        new Ajax();

        new Filters\Favicon();
        new Filters\JavaScript_Scripts();
        new Filters\Post();
        new Filters\Security();
        new Filters\SEO_Tags();

        $this->other_plugins_integrations();
    }

    /**
     * Register Install / uninstall hooks
     *
     * @return Void
     */
    private function register_install_uninstall_hooks() {
        register_activation_hook(LH_PLUGIN_FILE, ['\LH\Install_Uninstall', 'activation_hook']);
        register_deactivation_hook(LH_PLUGIN_FILE, ['\LH\Install_Uninstall', 'deactivation_hook']);
        register_uninstall_hook(LH_PLUGIN_FILE, ['\LH\Install_Uninstall', 'uninstall_hook']);
    }

    /**
     * Register init hooks
     *
     * @return Void
     */
    private function register_init_hooks() {
        add_action('init',          [$this, 'init']);
        add_action('init',          [$this, 'rewrite_rules'],    0);
        // add_action('admin_init',    [$this, 'admin_init']);

        // add_filter('redirect_canonical', [$this, 'redirect_canonical']);
    }

    /**
     * Initialize other plugins integrations
     *
     * @return Void
     */
    private function other_plugins_integrations() {
        new Plugins_Integrations\Advanced_Custom_Fields_Integration();
        // new Plugins_Integrations\Google_Captcha_Integration();
        // new Plugins_Integrations\Gravity_Forms_Integration();
    }

    /**
     * WordPress init action
     *
     * @return void
     */
    public function init() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Register and load scripts and styles
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_register_style('lh-admin-css',
            LH_ASSETS_URL . 'css/lh-admin.min.css',
            [],
            LH_PLUGIN_VERSION,
            'screen');

        wp_register_script('lh-admin-js',
            LH_ASSETS_URL . 'js/lh-admin.min.js',
            ['jquery'],
            LH_PLUGIN_VERSION, true);
    }

    /**
     * Register rewrite rules
     *
     * @return Void
     */
    public function rewrite_rules() {
        
        add_rewrite_tag('%lh_category%',   '([^/]+)');
        add_rewrite_tag('%lh_page%',       '([^/]+)');

        $post_listing_pages = get_field('lh_post_listing_pages', 'option');
        $post_listing_pages = explode(PHP_EOL, $post_listing_pages);

        if (!empty($post_listing_pages) && is_array($post_listing_pages)) {
            foreach ($post_listing_pages as $post_listing_base_url) {
                
                $post_listing_base_url = preg_replace("~[\r\n]~", "", $post_listing_base_url);
                $post_listing_base_url = trim($post_listing_base_url, '/');

                add_rewrite_rule(
                    '^' . $post_listing_base_url . '/page/([^/]+)?',
                    [
                        'pagename'  => $post_listing_base_url,
                        'lh_page'  => '$matches[1]'
                    ],
                    'top');
        
                add_rewrite_rule(
                    '^' . $post_listing_base_url . '/category/(.+)/page/([^/]+)?$',
                    [
                        'pagename'      => $post_listing_base_url,
                        'lh_category'  => '$matches[1]',
                        'lh_page'      => '$matches[2]'
                    ],
                    'top');
            
                add_rewrite_rule(
                    '^' . $post_listing_base_url . '/category/(.+)?',
                    [
                        'pagename'      => $post_listing_base_url,
                        'lh_category'  => '$matches[1]'
                    ],
                    'top');           
            }
        }
    }

    /**
     * Stop automatic redirects to homepage for newly registered routes
     *
     * @param String $redirect
     * 
     * @return String
     */
    public function redirect_canonical($redirect) {
        // if (
        //     !empty(get_query_var(self::$conditions_post_type)) ||
        //     !empty(get_query_var('lh_category')) ||
        //     !empty(get_query_var('lh_page'))
        // ) {
        //     $redirect = false;
        // }

        return $redirect;
    }
}