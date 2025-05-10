<?php

namespace LH\ACF_Blocks;

use LH\ACF_Blocks\Interfaces\ACF_Blocks_Interface;

class ACF_Block implements ACF_Blocks_Interface
{

    /**
     * Setup action & filter hooks
     * 
     * @param string $block_slug
     * 
     * @return ACF_Block
     */
    public function __construct($block_slug = '')
    {
        if ($block_slug == '' && isset(static::$slug)) {
            $block_slug = static::$slug;
        }

        $this->register_block($block_slug);
        $this->register_image_sizes();
    }

    /**
     * Register block using ACF functions
     * 
     * @param string $block_slug
     *
     * @return void
     */
    public function register_block($block_slug = "")
    {
        if ($block_slug == '' && isset(static::$slug)) {
            $block_slug = static::$slug;
        }

        $relative_path_between_wp_root_and_active_theme = '/' . lh_relative_path(ABSPATH, get_stylesheet_directory()) . '/';


        // I tweaked these to function as they were throwing 404 error



        // wp_register_script('lh-acf-block-' . $block_slug . '-js',
        //     $relative_path_between_wp_root_and_active_theme . 'assets/dist/js/blocks/' . $block_slug . '.min.js',
        //     ['jquery'],
        //     LH_PLUGIN_VERSION,
        //     true);
        wp_register_script(
            'lh-acf-block-' . $block_slug . '-js',
            '/wp-content/themes/lh-theme/assets/dist/js/blocks/' . $block_slug . '.min.js',
            ['jquery'],
            LH_PLUGIN_VERSION,
            true
        );

        wp_register_style(
            'lh-acf-block-' . $block_slug . '-editor-css',
            $relative_path_between_wp_root_and_active_theme . 'assets/dist/css/blocks/' . $block_slug . '-editor.min.css',
            [],
            LH_PLUGIN_VERSION,
            'screen'
        );

        // wp_register_script(
        //     'lh-acf-block-' . $block_slug . '-editor-js',
        //     $relative_path_between_wp_root_and_active_theme . 'assets/dist/js/blocks/' . $block_slug . '-editor.min.js',
        //     ['jquery'],
        //     LH_PLUGIN_VERSION,
        //     true
        // );
        wp_register_script(
            'lh-acf-block-' . $block_slug . '-editor-js',
            '/wp-content/themes/lh-theme/assets/dist/js/blocks/' . $block_slug . '-editor.min.js',
            ['jquery'],
            LH_PLUGIN_VERSION,
            true
        );

        register_block_type(LH_PLUGIN_PATH . 'config/ACF_Blocks/' . $block_slug . '-block.json');
    }

    /**
     * Register image sizes for the images displayed by the block
     *
     * @return void
     */
    public function register_image_sizes()
    {
        if (
            isset(static::$image_sizes) &&
            is_array(static::$image_sizes) &&
            !empty(static::$image_sizes)
        ) {
            if (
                count(static::$image_sizes) === 6 &&
                !empty(static::$image_sizes['mobile']) &&
                !empty(static::$image_sizes['mobile_2x']) &&
                !empty(static::$image_sizes['tablet']) &&
                !empty(static::$image_sizes['tablet_2x']) &&
                !empty(static::$image_sizes['desktop']) &&
                !empty(static::$image_sizes['desktop_2x'])
            ) {
                lh_add_image_sizes(static::$image_sizes);
            } else {
                foreach (static::$image_sizes as $image_sizes_key => $image_sizes_set) {
                    lh_add_image_sizes($image_sizes_set);
                }
            }
        }
    }

    /**
     * Get block's template's path
     *
     * @return string
     */
    static function get_template_path($template_name = '')
    {
        if ($template_name == '' && isset(static::$slug)) {
            $template_name = static::$slug;
        }

        $template_path = get_stylesheet_directory() . '/blocks/' . static::$slug . '/' . $template_name . '.php';

        return apply_filters('lh_block_path', $template_path, __CLASS__);
    }

    /**
     * Start the Profile timer for the Black bar plugin
     * 
     * @param string $function
     * 
     * @return void
     */
    static function start_timer($function = 'render_block')
    {
        if (current_user_can('administrator')) {
            apply_filters('timer:start', static::$slug . ' - ' . $function);
        }
    }

    /**
     * Stop the Profile timer for the Black bar plugin
     * 
     * @param string $function
     * 
     * @return void
     */
    static function stop_timer($function = 'render_block')
    {
        if (current_user_can('administrator')) {
            apply_filters('timer:stop', static::$slug . ' - ' . $function);
        }
    }

    /**
     * Render the block
     *
     * @return void
     */
    static function render_block($block, $content, $is_preview, $post_id, $wp_block, $context) {}
}