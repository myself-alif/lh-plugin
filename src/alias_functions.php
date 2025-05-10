<?php

/**
 * In this file are functions that are aliases for some static functions from plugin or all sorts of repetitive code related to the plugin
 */

/**
 * Get image HTML tag for an attachment
 * 
 * This function will create a picture tag with multiple sources for mobile, tablets and desktop
 * for both normal density displays and retina displays.
 * 
 * In order for this function to work properly you have to have registerd all the image size for
 * 6 cases(mobile 1x, mobile 2x, tablets 1x, tablets 2x, desktop 1x, and desktop 2x).
 * 
 * The easies way to register image size for all these size is to use the lh_add_image_size
 * function. You will use it as add_image_size WordPress' function to register for desktop size
 * images, and the function will calculate and register the other image size.
 * 
 * @param integer $attachment_id Image attachment ID
 * @param string  $size          Image size identifier
 * @param array   $attr          Image tag attributes
 * 
 * @return string
 */
if (!function_exists('lh_wp_get_attachment_image')) {
    function lh_wp_get_attachment_image($attachment_id, $size = 'thumbnail', $attr = [])
    {
        return \LH\Utils::wp_get_attachment_image($attachment_id, $size, $attr);
    }
}

/**
 * Register a desired image size and the variations for mobile and tablet, for both normal density displays and retina displays
 * 
 * @param string  $name   Image size identifier
 * @param integer $width  Image width in pixels
 * @param integer $height Image height in pixels
 * @param boolean $crop   Image cropping behavior
 * 
 * @return void
 */
if (!function_exists('lh_add_image_size')) {
    function lh_add_image_size($name, $width, $height, $crop = false)
    {
        return \LH\Utils::add_image_size($name, $width, $height, $crop);
    }
}

/**
 * Get image HTML tag for an attachment
 * 
 * This function will create a picture tag with multiple sources for mobile, tablets and desktop
 * for both normal density displays and retina displays.
 * 
 * In order for this function to work properly you have to have registerd all the image size for
 * 6 cases(mobile 1x, mobile 2x, tablets 1x, tablets 2x, desktop 1x, and desktop 2x).
 * 
 * The easies way to register image size for all these size is to use the lh_add_image_sizes
 * function.
 * 
 * @param integer $attachment_id Image attachment ID
 * @param array   $sizes         Image sizes
 * @param array   $attr          Image tag attributes
 * @param mixed   $backup_image  False for trans gif, image id or href
 * 
 * @return string
 */
if (!function_exists('lh_wp_get_attachment_image_by_sizes')) {
    function lh_wp_get_attachment_image_by_sizes($attachment_id, $sizes, $attr = [])
    {
        return \LH\Utils::wp_get_attachment_image_by_sizes($attachment_id, $sizes, $attr);
    }
}

/**
 * Register multiple image sizes
 * 
 * @param array $sizes
 * 
 * @return void
 */
if (!function_exists('lh_add_image_sizes')) {
    function lh_add_image_sizes($sizes)
    {
        return \LH\Utils::add_image_sizes($sizes);
    }
}

/**
 * Get image URLs for all image sizes for a certain image
 * 
 * @param array   $image_sizes
 * @param integer $image_id
 * 
 * @return array
 */
if (!function_exists('lh_get_image_sizes')) {
    function lh_get_image_sizes($image_sizes, $image_id)
    {
        return \LH\Utils::get_image_sizes($image_sizes, $image_id);
    }
}

/**
 * Get the URL for the biggest image size for a certain image. Set bigger image sizes first in the $image_sizes array.
 * 
 * @param array   $image_sizes
 * @param integer $image_id
 * 
 * @return string
 */
if (!function_exists('lh_get_biggest_image_size')) {
    function lh_get_biggest_image_size($image_sizes, $image_id)
    {
        return \LH\Utils::get_biggest_image_size($image_sizes, $image_id);
    }
}

/**
 * Get embeded URL for YouTube and Vimeo videos
 * 
 * @param string $video_url
 * 
 * @return string
 */
if (!function_exists('lh_get_embeded_url')) {
    function lh_get_embeded_url($video_url)
    {
        return \LH\Utils::get_embeded_url($video_url);
    }
}

/**
 * Get embeded data for YouTube and Vimeo videos
 * 
 * @param string $video_url
 * 
 * @return array
 */
if (!function_exists('lh_get_embeded_data')) {
    function lh_get_embeded_data($video_url)
    {
        return \LH\Utils::get_embeded_data($video_url);
    }
}

/**
 * Get relative path between 2 absolute paths
 * 
 * @param string $from
 * @param string $to
 * @param string $ps
 * 
 * @return string
 */
if (!function_exists('lh_relative_path')) {
    function lh_relative_path($from, $to, $ps = DIRECTORY_SEPARATOR)
    {
        return \LH\Utils::relative_path($from, $to, $ps);
    }
}

/**
 * Get svg file as code for styling
 * 
 * @param string $svg_path
 * 
 * @return string
 */
if (!function_exists('lh_get_svg_file')) {
    function lh_get_svg_file($svg_path)
    {
        return \LH\Utils::get_svg_file($svg_path);
    }
}

/**
 * Check if the current page is the WordPress login or register page
 *
 * @return boolean
 */
if (!function_exists('lh_is_login_page')) {
    function lh_is_login_page()
    {
        return \LH\Utils::is_login_page();
    }
}

/**
 * Get reCaptcha keys if there are any set
 * 
 * @return array
 */
if (!function_exists('lh_get_recaptcha_keys')) {
    function lh_get_recaptcha_keys()
    {
        return \LH\Utils::get_recaptcha_keys();
    }
}

/**
 * Validate Google reCaptcha V3 response
 *
 * @param string $response
 * @param string $secret
 * @param string $remoteip
 * 
 * @return boolean
 */
if (!function_exists('lh_validate_google_recaptcha_v3')) {
    function lh_validate_google_recaptcha_v3()
    {
        return \LH\Utils::validate_google_recaptcha_v3();
    }
}

/**
 * Print markup for an acf link with a url and title
 * 
 * @param array $cta
 * @param string $classes
 * 
 * @return void
 */
if (!function_exists('lh_print_acf_link_as_html')) {
    function lh_print_acf_link_as_html($cta, $classes, $arrow = false)
    {
        return \LH\Utils::print_acf_link_as_html($cta, $classes, $arrow);
    }
}

/**
 * Print a simple acf element such as copy or title if not empty
 * 
 * @param string $field
 * @param string $tag
 * @param string $classes
 * @param array  $attrs
 * 
 * @return void
 */
if (!function_exists('lh_print_simple_acf_element')) {
    function lh_print_simple_acf_element($field, $tag = "div", $classes = "lh-element", $attrs = [])
    {
        return \LH\Utils::print_simple_acf_element($field, $tag, $classes, $attrs);
    }
}

/**
 * Add the zero-width space (ZWSP) character after most common special characters to
 * facilitate breaking long words correctly.
 * 
 * This function should only be used in places where the word breaking is not working
 * properly.
 *
 * @param string $string
 * 
 * @return string
 */
if (!function_exists('lh_add_zwsp')) {
    function lh_add_zwsp($string)
    {
        return \LH\Utils::add_zwsp($string);
    }
}

/**
 * Return pluralised name
 * 
 * @param string $first_name
 * 
 * @return string
 */
if (!function_exists('lh_return_pluralised_first_name')) {
    function lh_return_pluralised_first_name($first_name)
    {
        return \LH\Utils::return_pluralised_first_name($first_name);
    }
}

/**
 * Print the style for a block
 *
 * @param string $block_slug
 * 
 * @return void
 */
if (!function_exists('lh_print_block_style')) {
    function lh_print_block_style($block_slug)
    {
        return \LH\Utils::print_block_style($block_slug);
    }
}

/**
 * Get post ID from its GUID
 * 
 * @param string $guid
 * 
 * @return null|integer
 */
if (!function_exists('lh_get_post_id_from_guid')) {
    function lh_get_post_id_from_guid($guid)
    {
        return \LH\Utils::get_post_id_from_guid($guid);
    }
}

/**
 * Create a new Attachment post from the URL and return its ID.
 * If there is an Attachment with the same name and file hash, the post ID of that one is returned,
 * and no new file is uploaded.
 * 
 * @param string  $url
 * @param integer $post_parent_id
 * @param string  $image_name
 * 
 * @return null|integer
 */
if (!function_exists('lh_create_attachment_from_url')) {
    function lh_create_attachment_from_url($url, $post_parent_id = 0)
    {
        return \LH\Utils::create_attachment_from_url($url, $post_parent_id);
    }
}

/**
 * Get post listing
 * 
 * @param string        $post_type 
 * @param string        $taxonomy_name
 * @param integer       $category_id
 * @param integer       $page_number
 * @param array         $featured_ids
 * @param array         $include_ids
 * @param array         $exclude_ids
 * @param integer       $posts_per_page
 * @param string        $template_name
 * @param string        $block_name
 * @param boolean       $show_pagination
 * @param boolean       $return_fields
 * @param array         $orderby
 * @param array         $status
 * @param boolean|array $custom_meta
 * 
 * @return array
 */
if (!function_exists('lh_get_post_listing')) {
    function lh_get_post_listing(
        $post_type          = "post",
        $taxonomy_name      = "category",
        $category_id        = 0,
        $page_number        = 1,
        $featured_ids       = [],
        $include_ids        = [],
        $exclude_ids        = [],
        $posts_per_page     = 10,
        $template_name      = "post-listing",
        $block_name         = "explore-destinations",
        $show_pagination    = false,
        $return_fields      = false,
        $orderby            = ['date' => "DESC"],
        $status             = ["publish"],
        $custom_meta        = false
    ) {
        return \LH\Utils::get_post_listing($post_type, $taxonomy_name, $category_id, $page_number, $featured_ids, $include_ids, $exclude_ids, $posts_per_page, $template_name, $block_name, $show_pagination, $return_fields, $orderby, $status, $custom_meta);
    }
}

/**
 * Get categories
 * 
 * @param string  $post_type 
 * @param string  $tax_name
 * @param integer $current_category
 * @param string  $order_by (optional)
 * @param string  $order (optional)
 * 
 * @return array
 */
if (!function_exists('lh_get_categories')) {
    function lh_get_categories(
        $post_type          = "post",
        $tax_name           = "category",
        $current_category   = 0,
        $order_by           = "name",
        $order              = "ASC"
    ) {
        return \LH\Utils::get_post_type_categories($post_type, $tax_name, $current_category, $order_by, $order);
    }
}

/**
 * Print pagination
 *
 * @param string  $base_url
 * @param integer $max_page
 * @param integer $page
 * @param integer $first_pages_count
 * @param integer $last_pages_count
 * @param integer $around_current_page_count
 * @param integer $display_all_count_less_than
 * @param boolean $ajaxed
 * @param array   $containerClasses
 * 
 * @return void
 */
if (!function_exists('lh_print_pagination')) {
    function lh_print_pagination(
        $base_url                       = "",
        $max_page                       = 10,
        $page                           = 1,
        $first_pages_count              = 3,
        $last_pages_count               = 3,
        $around_current_page_count      = 4,
        $display_all_count_less_than    = 6,
        $ajaxed                         = false,
        $containerClasses               = []
    ) {
        return \LH\Utils::print_pagination($base_url, $max_page, $page, $first_pages_count, $last_pages_count, $around_current_page_count, $display_all_count_less_than, $ajaxed, $containerClasses);
    }
}

/**
 * Get category ID from slug
 * 
 * @param string $slug
 * @param string $tax_name
 * 
 * @return integer
 */
if (!function_exists('lh_get_cat_id_from_slug')) {
    function lh_get_cat_id_from_slug($slug, $tax_name = "category")
    {
        return \LH\Utils::get_category_id_from_slug($slug . $tax_name);
    }
}

/**
 * Get primary category by post ID
 * 
 * @param integer $post_id
 * @param string  $tax_name
 * @param integer $category_id 
 * 
 * @return \WP_Term
 */
if (!function_exists('lh_get_primary_cat_by_post_id')) {
    function lh_get_primary_cat_by_post_id($post_id, $tax_name = "category", $category_id = 0)
    {
        return \LH\Utils::get_primary_category_by_post_id($post_id, $tax_name, $category_id);
    }
}

/**
 * Take all the words up to a certain character limit from a string
 * 
 * @param string  $str
 * @param integer $limit
 * @param string  $ellipsis
 * 
 * @return string
 */
if (!function_exists('lh_limit_characters')) {
    function lh_limit_characters($str, $limit, $ellipsis = '...')
    {
        return \LH\Utils::limit_characters($str, $limit, $ellipsis);
    }
}
