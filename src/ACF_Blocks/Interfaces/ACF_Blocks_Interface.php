<?php

namespace LH\ACF_Blocks\Interfaces;

interface ACF_Blocks_Interface
{
    /**
     * Register block using ACF functions
     * 
     * @param string $block_slug
     *
     * @return Void
     */
    public function register_block($block_slug);

    /**
     * Register image sizes for the images displayed by the block
     *
     * @return Void
     */
    public function register_image_sizes();

    /**
     * Render the block
     *
     * @return Void
     */
    static function render_block($block, $content, $is_preview, $post_id, $wp_block, $context);
}