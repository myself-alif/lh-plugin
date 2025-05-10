<?php

namespace LH\ACF_Blocks;

class Title extends ACF_Block {
    static $slug = 'title';

    /**
     * Render the block
     *
     * @return Void
     */
    static function render_block($block, $content, $is_preview, $post_id, $wp_block, $context) {
        $el_id      = (!empty($block['anchor']) ? esc_attr($block['anchor']) : 'lh-acf-block-' . mt_rand(100000, 999999));
        $el_class   = !empty($block['className']) ? explode(' ', $block['className']) : [];
        $el_class[] = 'lh-acf-block';
        $el_class[] = 'lh-acf-block-' . self::$slug;

        $title      = get_field("title");

        require(self::get_template_path());
    }
}
