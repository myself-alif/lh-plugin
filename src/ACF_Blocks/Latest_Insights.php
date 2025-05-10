<?php

namespace LH\ACF_Blocks;

class Latest_Insights extends ACF_Block
{
    static $slug = 'latest-insights';

    /**
     * Render the block
     *
     * @return Void
     */
    static function render_block($block, $content, $is_preview, $post_id, $wp_block, $context)
    {
        $el_id      = (!empty($block['anchor']) ? esc_attr($block['anchor']) : 'lh-acf-block-' . mt_rand(100000, 999999));
        $el_class   = !empty($block['className']) ? explode(' ', $block['className']) : [];
        $el_class[] = 'lh-acf-block';
        $el_class[] = 'lh-acf-block-' . self::$slug;

        $title      = get_field("title");
        $title_icon  = get_field("title_icon");
        $select_content_manually = get_field("select_content_manually");
        $recent_posts = get_field("recent_posts");
        $post_type = get_field("post_type");
        $category = get_field("category_filter");
        $manual_select = get_field("manual_select");
        $grid_columns = get_field("grid_columns");
        $grid_gap = get_field("grid_gap");

        require(self::get_template_path());
    }
}