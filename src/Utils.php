<?php

namespace LH;

class Utils
{
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
    static function wp_get_attachment_image($attachment_id, $size = 'thumbnail', $attr = []) {
        /* Get desktop image URL and sizes */
        $image  = wp_get_attachment_image_src($attachment_id, $size);
        $width  = $image[1];
        $height = $image[2];

        /* Calculate sizes for desktop */
        $width_retina_display           = (int)($width  * 2);
        $height_retina_display          = (int)($height * 2);

        /* Calculate sizes for mobile */
        $width_mobile                   = (int)($width  / 2);
        $height_mobile                  = (int)($height / 2);

        /* Calculate sizes for tablet */
        $width_tablet                   = (int)($width  * 0.7);
        $height_tablet                  = (int)($height * 0.7);
        $width_tablet_retina_display    = (int)($width_tablet * 2);
        $height_tablet_retina_display   = (int)($height_tablet * 2);

        /* Get desktop image URL for retina display */
        $image_2x           = wp_get_attachment_image_src($attachment_id, [$width_retina_display,           $height_retina_display]);

        /* Get mobile image URL */
        $image_mobile       = wp_get_attachment_image_src($attachment_id, [$width_mobile,                   $height_mobile]);

        /* Get tablet images URLs */
        $image_tablet       = wp_get_attachment_image_src($attachment_id, [$width_tablet,                   $height_tablet]);
        $image_tablet_2x    = wp_get_attachment_image_src($attachment_id, [$width_tablet_retina_display,    $height_tablet_retina_display]);

        /* Set image tag attributes */
        $size_class     = is_array($size) ? join('x', $size) : $size;
        $default_attr   = [
            'src'       => $image[0],
            'srcset'    => $image[0] . ', ' . $image_2x[0] . ' 2x',
            'class'     => "attachment-$attachment_id size-$size_class",
            'alt'       => trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))),
            'width'     => $width,
            'height'    => $height
        ];
        /* Add `loading` attribute */
        if (wp_lazy_loading_enabled('img', 'wp_get_attachment_image')) {
            $default_attr['loading'] = 'lazy';
        }
        $attr = wp_parse_args($attr, $default_attr);
        /**
         * If the default value of `lazy` for the `loading` attribute is overridden
         * to omit the attribute for this image, ensure it is not included.
         */ 
        if ( array_key_exists( 'loading', $attr ) && ! $attr['loading'] ) {
            unset( $attr['loading'] );
        }

        /* Generate the HTML */
        $html = '
<picture>
    <source media="(max-width:575px)" srcset="' . $image_mobile[0] . ', ' . $image[0] . ' 2x">
    <source media="(max-width:991px)" srcset="' . $image_tablet[0] . ', ' . $image_tablet_2x[0] . ' 2x">
    <source media="(min-width:992px)" srcset="' . $image[0] . ', ' . $image_2x[0] . ' 2x">
    <img ';
        foreach ($attr as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        $html .= '/></picture>';

        return $html;
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
    static function add_image_size($name, $width, $height, $crop = false) {
        /* Calculate sizes for desktop */
        $width_retina_display           = (int)($width  * 2);
        $height_retina_display          = (int)($height * 2);

        /* Calculate sizes for mobile */
        $width_mobile                   = (int)($width  / 2);
        $height_mobile                  = (int)($height / 2);

        /* Calculate sizes for tablet */
        $width_tablet                   = (int)($width  * 0.7);
        $height_tablet                  = (int)($height * 0.7);
        $width_tablet_retina_display    = (int)($width_tablet * 2);
        $height_tablet_retina_display   = (int)($height_tablet * 2);

        /* Register desktop image sizes */
        add_image_size($name,                   $width,                         $height,                        $crop);
        add_image_size($name . '_2x',           $width_retina_display,          $height_retina_display,         $crop);

        /* Register mobile image size */
        add_image_size($name . '_mobile',       $width_mobile,                  $height_mobile,                 $crop);
        /* We don't register any size for mobile retina display image size because it is the same size as the desktop nomal image size */
        
        /* Register tablet image sizes */
        add_image_size($name . '_tablet',       $width_tablet,                  $height_tablet,                 $crop);
        add_image_size($name . '_tablet_2x',    $width_tablet_retina_display,   $height_tablet_retina_display,  $crop);
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
    static function wp_get_attachment_image_by_sizes($attachment_id, $sizes, $attr = [], $backup_image = false) {
        $blank_gif = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        if (
            empty($sizes) ||
            count($sizes) !== 6 ||
            empty($sizes['mobile']) ||
            empty($sizes['mobile_2x']) ||
            empty($sizes['tablet']) ||
            empty($sizes['tablet_2x']) ||
            empty($sizes['desktop']) ||
            empty($sizes['desktop_2x'])
        ) {
            // If the sizes are not properly set, return the thumbnail for the attachment
            return self::wp_get_attachment_image($attachment_id);
        }

        $image_full = wp_get_attachment_image_src($attachment_id, 'full');

        if (
            empty($image_full) ||
            !is_array($image_full) ||
            empty($image_full[0])
        ) {
            
            if(empty($backup_image)) {
                $return_val = '<picture class="lh-img-not-found"><img src="' . $blank_gif . '"></picture>';
            } else {
                if((int) $backup_image == $backup_image){ 
                    $backup_img_obj = wp_get_attachment_image_src($backup_image, 'full');
                    
                    if (
                        empty($backup_img_obj) ||
                        !is_array($backup_img_obj) ||
                        empty($backup_img_obj[0])
                    ) {
                        $backup_image = $backup_img_obj[0];
                    }
                }
                
                $return_val = '<picture class="lh-img-not-found lh-passed-image"><img src="' . $backup_image . '"></picture>';
            }

            return $return_val;
        }

        /* Get mobile image URLs */
        $image_mobile       = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['mobile']['width'] . 'x' . $sizes['mobile']['height'] . (!empty($sizes['mobile']['crop']) && $sizes['mobile']['crop'] ? '_cropped' : '_not_cropped'));
        $image_mobile_2x    = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['mobile_2x']['width'] . 'x' . $sizes['mobile_2x']['height'] . (!empty($sizes['mobile_2x']['crop']) && $sizes['mobile_2x']['crop'] ? '_cropped' : '_not_cropped'));

        /* Get tablet image URLs */
        $image_tablet       = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['tablet']['width'] . 'x' . $sizes['tablet']['height'] . (!empty($sizes['tablet']['crop']) && $sizes['tablet']['crop'] ? '_cropped' : '_not_cropped'));
        $image_tablet_2x    = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['tablet_2x']['width'] . 'x' . $sizes['tablet_2x']['height'] . (!empty($sizes['tablet_2x']['crop']) && $sizes['tablet_2x']['crop'] ? '_cropped' : '_not_cropped'));

        /* Get desktop image URLs */
        $image_desktop      = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['desktop']['width'] . 'x' . $sizes['desktop']['height'] . (!empty($sizes['desktop']['crop']) && $sizes['desktop']['crop'] ? '_cropped' : '_not_cropped'));
        $image_desktop_2x   = wp_get_attachment_image_src($attachment_id, 'lh_' . $sizes['desktop_2x']['width'] . 'x' . $sizes['desktop_2x']['height'] . (!empty($sizes['desktop_2x']['crop']) && $sizes['desktop_2x']['crop'] ? '_cropped' : '_not_cropped'));

        if ($image_desktop_2x === false)    { $image_desktop_2x = $image_full; }
        if ($image_desktop === false)       { $image_desktop = $image_desktop_2x; }
        if ($image_tablet_2x === false)     { $image_tablet_2x = $image_desktop; }
        if ($image_tablet === false)        { $image_tablet = $image_tablet_2x; }
        if ($image_mobile_2x === false)     { $image_mobile_2x = $image_tablet; }
        if ($image_mobile === false)        { $image_mobile = $image_mobile_2x; }

        /* Set image tag attributes */
        $default_attr   = [
            'src'       => $image_mobile[0],
            'srcset'    => $image_mobile[0] . ', ' . $image_mobile_2x[0] . ' 2x',
            'class'     => "attachment-$attachment_id",
            'alt'       => trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))),
            'width'     => $sizes['mobile']['width'],
            'height'    => $sizes['mobile']['height']
        ];
        /* Add `loading` attribute */
        if (wp_lazy_loading_enabled('img', 'wp_get_attachment_image')) {
            $default_attr['loading'] = 'lazy';
        }

        $attr = wp_parse_args($attr, $default_attr);

        /**
         * If the default value of `lazy` for the `loading` attribute is overridden
         * to omit the attribute for this image, ensure it is not included.
         */ 
        if ( array_key_exists( 'loading', $attr ) && ! $attr['loading'] ) {
            unset( $attr['loading'] );
        }

        /* Generate the HTML */
        $html = '
<picture>
    <source media="(max-width:575px)" srcset="' . $image_mobile[0] . ', ' . $image_mobile_2x[0] . ' 2x">
    <source media="(max-width:991px)" srcset="' . $image_tablet[0] . ', ' . $image_tablet_2x[0] . ' 2x">
    <source media="(min-width:992px)" srcset="' . $image_desktop[0] . ', ' . $image_desktop_2x[0] . ' 2x">
    <img ';
        foreach ($attr as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        $html .= '/></picture>';

        return $html;
    }

    /**
     * Register multiple image sizes
     * 
     * @param array $sizes
     * 
     * @return void
     */
    static function add_image_sizes($sizes) {
        foreach ($sizes as $size_name => $size_details) {
            if (
                !empty($sizes[$size_name]) &&
                !empty($sizes[$size_name]['width']) &&
                !empty($sizes[$size_name]['height'])
            ) {
                add_image_size(
                    'lh_' . $sizes[$size_name]['width'] . 'x' . $sizes[$size_name]['height'] . (!empty($sizes[$size_name]['crop']) && $sizes[$size_name]['crop'] ? '_cropped' : '_not_cropped'),
                    $sizes[$size_name]['width'],
                    $sizes[$size_name]['height'],
                    !empty($sizes[$size_name]['crop']) ? $sizes[$size_name]['crop'] : false
                );
            }
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
    static function get_image_sizes($image_sizes, $image_id) {
        $images = [];

        foreach ($image_sizes as $key => $image_size) {
            $image = wp_get_attachment_image_src($image_id, $image_size);
            $images[$key] = $image[0];
        }

        return $images;
    }
    
    
    /**
     * Get the URL for the biggest image size for a certain image. Set bigger image sizes first in the $image_sizes array.
     * 
     * @param array   $image_sizes
     * @param integer $image_id
     * 
     * @return string
     */
    static function get_biggest_image_size($image_sizes, $image_id) {
        global $_wp_additional_image_sizes;

        $image_src      = '';
        $image_full_src = wp_get_attachment_image_src($image_id, 'full');
        $image_full_src = (!empty($image_full_src[0]) ? $image_full_src[0] : '');

        foreach ($image_sizes as $image_size) {
            $new_image = wp_get_attachment_image_src($image_id, $image_size);

            if ($new_image[0] != $image_full_src) {
                if ($_wp_additional_image_sizes[$image_size]['crop']) {
                    if (
                        $new_image[1] >= $_wp_additional_image_sizes[$image_size]['width']
                        && $new_image[2] >= $_wp_additional_image_sizes[$image_size]['height']
                    ) {
                        $image_src = $new_image[0];
                        break;
                    }
                } else {
                    $image_src = $new_image[0];
                    break;
                }
            }
        }

        if ($image_src == '') {
            $image_src = $image_full_src;
        }

        return $image_src;
    }

    /**
     * Get embeded URL for YouTube and Vimeo videos
     * 
     * @param string $video_url
     * 
     * @return string
     */
    static function get_embeded_url($video_url) {
        $embeded_url            = '';
        $youtube_regex_pattern  = '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)(?:-nocookie)?\.(?:com|be)(?:\/embed\/|\/watch\?v=|\/)([^\s]+)/';
        $vimeo_regex_pattern    = '/(?:https?:\/{2})?(?:www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+[a-zA-Z0-9_\-\#\?\=\&]+)?/';

        if (preg_match($youtube_regex_pattern, $video_url, $matches)) {
            if (!empty($matches[1])) {
                $embeded_url = 'https://www.youtube.com/embed/' . $matches[1];
            }
        } elseif (preg_match($vimeo_regex_pattern, $video_url, $matches)) {
            if (!empty($matches[1])) {
                $embeded_url = 'https://player.vimeo.com/video/' . $matches[1];
            }
        } else {
            $embeded_url = $video_url;
        }

        return $embeded_url;
    }

    /**
     * Get embeded data for YouTube and Vimeo videos
     * 
     * @param string $video_url
     * 
     * @return array
     */
    static function get_embeded_data($video_url) {
        $embeded_url            = '';
        $video_code             = 'url';
        $video_type             = 'url';
        $youtube_regex_pattern  = '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?(?:-nocookie)?\.(?:com|be)(?:\/embed\/|\/watch\?v=|\/)([^\s]+)/';
        $vimeo_regex_pattern    = '/(?:https?:\/{2})?(?:www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+[a-zA-Z0-9_\-\#\?\=\&]+)?/';

        if (preg_match($youtube_regex_pattern, $video_url, $matches)) {
            if (!empty($matches[1])) {
                $embeded_url    = 'https://www.youtube.com/embed/' . $matches[1];
                $video_code     = $matches[1];
                $video_type     = 'youtube';
            }
        } elseif (preg_match($vimeo_regex_pattern, $video_url, $matches)) {
            if (!empty($matches[1])) {
                $embeded_url    = 'https://player.vimeo.com/video/' . $matches[1];
                $video_code     = $matches[1];
                $video_type     = 'vimeo';
            }
        } else {
            $embeded_url    = $video_url;
            $video_code     = 'url';
        }

        return [
            'embeded_url'   => $embeded_url,
            'video_code'    => $video_code,
            'video_type'    => $video_type
        ];
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
    static function relative_path($from, $to, $ps = DIRECTORY_SEPARATOR) {
        $ar_from    = explode($ps, rtrim($from, $ps));
        $ar_to      = explode($ps, rtrim($to, $ps));

        while(
            count($ar_from) &&
            count($ar_to) &&
            ($ar_from[0] == $ar_to[0])
        ) {
            array_shift($ar_from);
            array_shift($ar_to);
        }

        return str_pad('', count($ar_from) * 3, '..' . $ps) . implode($ps, $ar_to);
    }

    /**
     * Get svg file as code for styling
     * 
     * @param string $svg_path
     * 
     * @return string
     */
    static function get_svg_file($svg_path) {
        //make sure that the svg mime type is being checked for
        $mimes = array(
            'svg'   => 'image/svg+xml', 
            'svgz'  => 'image/svg+xml'
        );

        // Check the SVG file exists
        if (wp_check_filetype($svg_path, $mimes)['ext'] == 'svg' ) {
            $request = wp_remote_get( $svg_path, stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> true
                ]
            ]) );

            return wp_remote_retrieve_body( $request );
        } else {
            // if the file is not an SVG, it should at least be an image url
            return '<img src="' . $svg_path . '" alt="' . get_bloginfo('name') . '" />';
        }

        // Return a blank string if we can't find the file.
        return '';
    }

    /**
     * Check if the current page is the WordPress login or register page
     * 
     * @return boolean
     */
    static function is_login_page() {
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
    }

    /**
     * Get reCaptcha keys if there are any set
     * 
     * @return array
     */
    static function get_recaptcha_keys() {
        $lh_recaptcha  = get_field('lh_recaptcha', 'option');
        $recaptcha_keys = [];

        if (
            !empty($lh_recaptcha['site_key']) &&
            !empty($lh_recaptcha['secret_key'])
        ) {
            $recaptcha_keys = $lh_recaptcha;
        }

        return $recaptcha_keys;
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
    static function validate_google_recaptcha_v3($response = '', $secret = '', $remoteip = '') {
        if (!defined('LH_GOOGLE_RECAPTCHA_VERIFY_URL')) {
            return false;
        }

        if (empty($response)) {
            if (!empty($_POST['g-recaptcha-response'])) {
                $response = esc_attr($_POST['g-recaptcha-response']);
            } else {
                return false;
            }
        }

        if (empty($secret)) {
            $recaptcha_keys = self::get_recaptcha_keys();

            if (!empty($recaptcha_keys['secret_key'])) {
                $secret = $recaptcha_keys['secret_key'];
            } else {
                return false;
            }
        }

        if (empty($remoteip)) {
            $remoteip = $_SERVER['REMOTE_ADDR'];
        }

        $context = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query([
                    'secret'    => $secret,
                    'response'  => $response,
                    'remoteip'  => $remoteip
                ])
            ]
        ]);

        $grv_response = file_get_contents(LH_GOOGLE_RECAPTCHA_VERIFY_URL, false, $context);
        $grv_response = json_decode($grv_response, true);

        return !empty($grv_response['success']);
    }

    /**
     * Print markup for an acf link with a url and title
     * 
     * @param array $cta
     * @param string $classes
     * 
     * @return void
     */
    static function print_acf_link_as_html($cta, $classes, $arrow = false){
        $classes = (!empty($classes)) ? $classes . ' lh-cta' : 'lh-cta';

        if (!empty($cta['url']) && !empty($cta['title'])) {
            ?>
                <a 
                    href="<?php echo esc_url($cta['url']); ?>"     
                    <?php if(!empty($classes)){ ?>
                        class="<?php echo $classes ?>" 
                    <?php } ?>
                    <?php if(!empty($cta['target'])){ ?>
                        target="<?php echo esc_attr($cta['target']); ?>"
                    <?php } ?>
                >
                    <?php echo esc_html($cta['title']); ?>
                    <?php if($arrow){ ?>
                        <span class="lh-arrow"></span>
                    <?php } ?>
                </a>
            <?php
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
    static function print_simple_acf_element($field, $tag = "div", $classes = "lh-element", $attrs = []){
        if(!empty($field)){ 
            $attrs_string = " ";

            if(array_key_exists("class", $attrs)){
                $classes .= " " . $attrs['class'];
                unset($attrs['class']);
            }

            if(!empty($attrs)){
                
                foreach($attrs as $key => $value) {
                    $attrs_string .= $key . '="' . $value . '" ';
                }
            }

            echo '<' . $tag . ' class="' . $classes . '"' . $attrs_string . '>' . $field . "</" . $tag . ">";           
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
    static function add_zwsp($string) {
        $string = str_replace(
            ['-', '!', '(', ')', '[', ']', '{', '}', '\\', '/', '~', '_', '=', '+', '@', '|', '%'],
            ['-&#8203;', '!&#8203;', '&#8203;(', ')&#8203;', '&#8203;[', ']&#8203;', '&#8203;{', '}&#8203;', '\\&#8203;', '/&#8203;', '~&#8203;', '_&#8203;', '=&#8203;', '+&#8203;', '&#8203;@', '|&#8203;', '%&#8203;'],
            $string);

        return $string;
    }

    /**
     * Return pluralised name
     * 
     * @param string $first_name
     * 
     * @return string
     */
    static function return_pluralised_first_name($first_name){
        $last_letter = strtolower(substr($first_name, -1));
        $processed_name = $first_name . "'s";

        if($last_letter == "s"){
            $processed_name = $first_name . "'";
        }

        return $processed_name;
    }

    /**
     * Print the style for a block
     *
     * @param string $block_slug
     * 
     * @return void
     */
    static function print_block_style($block_slug) {
        if (empty($block_slug)) {
            return;
        }

        global $LH_BLOCKS_IN_PAGE;

        if (!is_array($LH_BLOCKS_IN_PAGE)) {
            $LH_BLOCKS_IN_PAGE = [];
        }

        if (!in_array($block_slug, $LH_BLOCKS_IN_PAGE)) {
            $LH_BLOCKS_IN_PAGE[] = $block_slug;

            print '<style type="text/css">';
            include(get_stylesheet_directory() . '/assets/dist/css/blocks/' . $block_slug . '.min.css');
            print '</style>';
        }
    }

    /**
     * Get post ID from its GUID
     * 
     * @param string $guid
     * 
     * @return null|integer
     */
    static function get_post_id_from_guid($guid) {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT `ID` FROM $wpdb->posts WHERE `guid` IN (%s, %s, %s) LIMIT 1",
                $guid,
                'http://' . $guid,
                'https://' . $guid,
            )
        );
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
    static function create_attachment_from_url($url, $post_parent_id = 0, $image_name = '') {
        $image_file_hash        = hash_file('sha1', $url);
        $parsed_url             = parse_url($url);
        $url_without_parameters = $parsed_url['scheme'].'://'.$parsed_url['host'].$parsed_url['path'];

        if (empty($image_name)) {
            $image_name = basename($url_without_parameters);
        }

        $image_post = get_posts([
            'title'             => sanitize_file_name($image_name),
            'posts_per_page'    => 1,
            'post_type'         => 'attachment'
        ]);

        if (!empty($image_post) && !empty($image_post[0]) && !empty($image_post[0]->ID)) {
            $image_post_hash = get_post_meta($image_post[0]->ID, 'lh_image_hash', true);
            if ($image_post_hash == $image_file_hash) {
                return $image_post[0]->ID;
            }
        }

        $context = stream_context_create([
            'http'  => [
                'method' => 'GET'
            ]
        ]);

        $image_data         = file_get_contents($url, false, $context);
        $upload_dir         = wp_upload_dir();
        $unique_file_name   = wp_unique_filename($upload_dir['path'], $image_name);
        $filename           = basename($unique_file_name);

        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        // Create the image  file on the server
        file_put_contents($file, $image_data);

        // Check image file type
        $wp_filetype = wp_check_filetype($filename, null);

        // Set attachment data
        $attachment = [
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        // Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file, $post_parent_id);
        add_post_meta($attach_id, 'lh_image_hash',         $image_file_hash,   true);
        add_post_meta($attach_id, 'lh_image_original_url', $url,               true);

        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);

        // Assign metadata to attachment
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
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
    static function get_post_listing(
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
        $show_more_page = false;

        $args = array(
            'post_type'      => $post_type,
            'post_status'    => $status,
            'orderby'        => $orderby
        );

        $args_all_posts = array(
            'post_type'      => $post_type,
            'post_status'    => $status,
            'posts_per_page' => -1,
            'fields'         => 'ids'
        );

        if (!empty($return_fields)) {
            $args['fields'] = $return_fields;
        }

        // set up category args
        if ($category_id !== 0) {
            // convert if we need to
            if (!is_int($category_id)) {
                $category_id = lh_get_cat_id_from_slug($category_id, $taxonomy_name);
            }

            $category = get_term_by('term_id', $category_id, $taxonomy_name);

            if (!empty($category->term_id)) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomy_name,
                        'field'    => 'term_id',
                        'terms'    => $category->term_id
                    )
                );

                $args_all_posts['tax_query'] = $args['tax_query'];
            }

            $check_featured_ids = $featured_ids;
            $featured_ids       = [];

            foreach ($check_featured_ids as $index => $featured_id) {
                $featured_cat = lh_get_primary_cat_by_post_id($featured_id, $taxonomy_name);

                if($featured_cat->slug == $category->slug){
                    $featured_ids[] = $featured_id;
                }
            }
        }

        $featured_count = count($featured_ids);

        $query_posts_per_page = ($featured_count >= ($page_number * $posts_per_page)) 
            ? 0 
            : (($featured_count > (($page_number - 1) * $posts_per_page) && $featured_count < ($page_number * $posts_per_page)) 
                ? $posts_per_page - ($featured_count - ($page_number - 1) * $posts_per_page)
                : $posts_per_page
                );

        $offset = ($page_number == ceil($featured_count / $posts_per_page)) 
            ? 0
            : ($page_number - 1)  * $posts_per_page - $featured_count;

        $featured_posts = [];

        if (!empty($featured_ids)) {
            $start  = ($page_number - 1) * $posts_per_page;
            $end    = ( $featured_count > ($page_number * $posts_per_page) ) ? ($page_number * $posts_per_page) : $featured_count;

            if ($featured_count > $start) {
                for($i = $start; $i < $end; $i++){
                    $featured_posts[] = get_post($featured_ids[$i]);
                }
            }
        }

        $args['offset']         = $offset;
        $args['posts_per_page'] = $query_posts_per_page;

        // include ids - cannot be used excluded posts
        if (empty($exclude_ids) && is_array($include_ids) && !empty($include_ids)) {
            $args['post__in'] = $include_ids;
            $args_all_posts['post__in'] = $args['post__in'];
        }

        if (!empty($featured_ids)) {
            $exclude_ids = array_merge($exclude_ids, $featured_ids);
        }

        // exclude ids, make sure this is part of total posts.
        if (is_array($exclude_ids) && !empty($exclude_ids)) {
            $args['post__not_in'] = $exclude_ids;
            $args_all_posts['post__not_in'] = $args['post__not_in'];
        }

        if (!empty($custom_meta)) {
            $args = array_merge($args, $custom_meta);
            $args_all_posts = array_merge($args_all_posts, $custom_meta);
        }

        $posts = [];

        if(!empty($query_posts_per_page)){
            $posts = get_posts( $args );
        }

        $merged_posts = array_merge($featured_posts, $posts);

        $post_count = count( $merged_posts );

        $all_posts          = get_posts($args_all_posts);
        $all_posts_count    = count($all_posts);

        if ($page_number * $posts_per_page < $featured_count + $all_posts_count) {
            $show_more_page = $page_number + 1;
        } else {
            $show_more_page = false;
        }

        return [
            'args'              => $args,
            'page_number'       => $page_number,
            'post_count'        => $post_count,
            'all_posts_count'   => $all_posts_count,
            'show_more'         => $show_more_page,
            'posts'             => $merged_posts
        ];
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
    static function get_post_type_categories(
        $post_type          = "post", 
        $tax_name           = "category", 
        $current_category   = 0, 
        $order_by           = "name", 
        $order              = "ASC"
    ) {
        $args = array(
            'type'      => $post_type,
            'orderby'   => $order_by,
            'order'     => $order,
            'taxonomy'  => $tax_name
        );
       
        $categories = get_categories($args);
       
        return $categories;
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
    static function print_pagination(
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
        $base_url   = trim($base_url , '/') . "/";
        $pages      = [];
        $show_all   = false;

        if (
            $max_page <= $display_all_count_less_than || 
            $first_pages_count >= $max_page || 
            $last_pages_count >= $max_page ||
            ($page + $around_current_page_count >= $max_page && $page - $around_current_page_count <= 1)
        ) {
            $show_all = true;
        }

        if ($show_all) {
            for ($i = 1; $i <= $max_page; $i++) {
                $pages[$i] = $i;
            }
        } else {
            //start
            $start  = 1;
            $end    = ($first_pages_count >= $max_page) ? $max_page : $first_pages_count;

            for ($i = $start; $i <= $end; $i++) {
                $pages[$i] = $i;
            }

            //middle
            $start  = (($page - $around_current_page_count - 1) < 1) ? 1 : $page - $around_current_page_count - 1;
            $end    = (($page + $around_current_page_count) > $max_page) ? $max_page : $page + $around_current_page_count;

            for ($i = $start; $i <= $end; $i++) {
                $pages[$i] = $i;
            }

            //end
            $start  = ($max_page - $last_pages_count < 1) ? 1 : $max_page - $last_pages_count;
            $end    = $max_page;

            for ($i = $start; $i <= $end; $i++) {
                $pages[$i] = $i;
            }

            //add elipses
            $last_index         = 0;
            $separator_count    = 0;
            $numeric_index      = 0;
            
            foreach ($pages as $index => $page_value) {
                if (
                    $index > 1 &&
                    $index - $last_index > 1
                ) {
                    $slice_1_length = $numeric_index;
                    $slice_2_start = $numeric_index + 1;
                    $slice_2_length = count($pages) - $numeric_index;
                    
                    $pages = array_merge(
                        array_slice($pages, 0, $slice_1_length, true), 
                        array("separator" . $separator_count => "separator"), 
                        array_slice($pages, $slice_2_start, $slice_2_length, true)
                    );

                    $separator_count++;
                }

                $last_index = $index;
                $numeric_index++;
            }
        }
        if (!$ajaxed) {
            ?><nav class="lh-pagination-container lh-pagination-numeric <?php print implode(' ', $containerClasses); ?>">
            <?php
        }
                
            foreach ($pages as $index => $page_value) {
                if ($page_value === "separator") {
                ?>
                    <div class="lh-pagination-separator">...</div>
                <?php
                }
                else {
                ?>
                    <a href="<?php echo explode('/page/', $base_url)[0] ?>page/<?php echo $page_value ?>/" 
                        class="lh-pagination-numeric-link <?php echo((int)$page_value === (int)$page) ? ' lh-pagination-numeric-link-active' : ''; ?>"
                        title="Go to page <?php echo $page_value ?>"
                        data-page="<?php echo $page_value ?>"   
                    >
                        <?php echo $page_value ?>    
                    </a>
                <?php
                }
            }
        if (!$ajaxed) {
            ?></nav>
            <?php
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
    static function get_category_id_from_slug($slug, $tax_name = "category"){
        $category_id    = 0;
        $category       = get_term_by('slug', $slug, $tax_name);
        
        if(!empty($category)){
            $category_id = $category->term_id;
        }

        return $category_id;
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
    static function get_primary_category_by_post_id($post_id, $tax_name = "category", $category_id = 0){
        //check to see if primary category set
        $main_category_id  = get_post_meta($post_id, '_yoast_wpseo_primary_' . $tax_name, true);
        
        //no category set
        if($category_id == 0){

            //get primary category or get first post category (will usually be only)
            if(!empty($main_category_id)){
                $category = get_term($main_category_id, $tax_name);

            }
            else {
                $post_categories = get_the_terms((int) $post_id, $tax_name);

                // Lets error check, if we dont we get an unhelpful and vague error msg
                if(!empty($post_categories->errors)){
                    $category = false;
                }
                else {
                    $category = $post_categories[0];
                }
            }
        }
        else { 
            // if we set a category then just return the selected category
            $category = get_term_by('term_id', $category_id, $tax_name);
        }

        return $category;
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
    static function limit_characters($str, $limit, $ellipsis = '...') {
        $str = trim($str);

        if (strlen($str) <= $limit) {
            return $str;
        }

        $words = preg_split('/\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);
        $truncated = '';
        $total_length = 0;

        foreach ($words as $word) {
            $word_length = strlen($word);
            $new_length = $total_length + $word_length;

            if ($new_length <= $limit) {
                $truncated .= $word . ' ';
                $total_length = $new_length;
            } else {
                break;
            }
        }

        $truncated = trim($truncated);

        if ($total_length < strlen($str)) {
            $truncated .= $ellipsis;
        }

        return $truncated;
    }
}