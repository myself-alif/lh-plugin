<?php

namespace LH\Filters;

class Favicon 
{
    /**
     * Setup action & filter hooks.
     *
     * @return Favicon
     */
    public function __construct() {
        add_action('init',			[$this, 'init'],		0, 0);
        add_action('do_faviconico',	[$this, 'do_faviconico'],	1, 0);
        add_action('wp_head', 		[$this, 'wp_head'], 		0, 0);

        add_filter('get_site_icon_url',			[$this, 'get_site_icon_url'],			999, 3);
        add_filter('upload_mimes', 				[$this, 'upload_mimes'],				999, 2);
        add_filter('wp_check_filetype_and_ext',	[$this, 'wp_check_filetype_and_ext'],	999, 5);
    }

    /**
     * Fires after WordPress has finished loading but before any headers are sent.
     *
     * Most of WP is loaded at this stage, and the user is authenticated. WP continues
     * to load on the {@see 'init'} hook that follows (e.g. widgets), and many plugins instantiate
     * themselves on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
     *
     * If you wish to plug an action once WP is loaded, use the {@see 'wp_loaded'} hook below.
     *
     * @since 1.5.0
     * 
     * @return void
     */
    public function init() {
        $this->site_webmanifest();
    }

    /**
     * Fires when serving the favicon.ico file.
     *
     * @since 5.4.0
     * 
     * @return void
     */
    public function do_faviconico() {
        $favicon_settings = get_field('favicon_settings', 'options');

        if (empty($favicon_settings['favicon_ico'])) {
            return;
        }

        wp_redirect($favicon_settings['favicon_ico']);

        exit;
    }

    /**
     * Prints scripts or data in the head tag on the front end.
     *
     * @return void
     */
    public function wp_head() {
        $favicon_settings = get_field('favicon_settings', 'options');

        if (!empty($favicon_settings['favicon_ico'])) {
            ?><link rel="icon" sizes="any" href="/favicon.ico" />
<?php
        }

        if (!empty($favicon_settings['apple_touch_icon'])) {
            ?>		<link rel="apple-touch-icon" sizes="180x180" href="<?php print $favicon_settings['apple_touch_icon']; ?>" />
<?php
        }

        if (!empty($favicon_settings['favicon_png_16_16'])) {
            ?>        <link rel="icon" type="image/png" sizes="16x16" href="<?php print $favicon_settings['favicon_png_16_16']; ?>" />
<?php
        }

        if (!empty($favicon_settings['favicon_png_32_32'])) {
            ?>        <link rel="icon" type="image/png" sizes="32x32" href="<?php print $favicon_settings['favicon_png_32_32']; ?>" />
<?php
        }

        if (!empty($favicon_settings['favicon_svg'])) {
            ?>        <link rel="icon" type="image/svg+xml" sizes="any" href="<?php print $favicon_settings['favicon_svg']; ?>" />
<?php
        }

        if (
            !empty($favicon_settings['manifest_json_config']) &&
            !empty($favicon_settings['manifest_json_config']['use_json_file']) &&
            !empty($favicon_settings['manifest_json_config']['manifest_json_file'])
        ) {
            ?>        <link rel="manifest" href="<?php print $favicon_settings['manifest_json_config']['manifest_json_file']; ?>" />
<?php
        } else {
            ?>        <link rel="manifest" href="/site.webmanifest" />
<?php
        }

        print "\n\n        ";
    }

    /**
     * Filters the site icon URL.
     *
     * @since 4.4.0
     *
     * @param string $url     Site icon URL.
     * @param int    $size    Size of the site icon.
     * @param int    $blog_id ID of the blog to get the site icon for.
     * 
     * @return string
     */
    public function get_site_icon_url($url, $size = 512,  $blog_id = 0) {
        $favicon_settings = get_field('favicon_settings', 'options');

        if (empty($favicon_settings['favicon_ico'])) {
            return $url;
        }

        return $favicon_settings['favicon_ico'];
    }

    /**
     * Filters the list of allowed mime types and file extensions.
     * 
     * @since 2.0.0
     *
     * @param array            $t    Mime types keyed by the file extension regex corresponding to those types.
     * @param int|WP_User|null $user User ID, User object or null if not provided (indicates current user).
     * 
     * @return array
     */
    public function upload_mimes($t, $user) {
        $t['ico'] 			= 'image/x-icon';
        $t['json']			= 'application/json';
        $t['webmanifest']	= 'application/manifest+json';

        return $t;
    }

    /**
     * Filters the "real" file type of the given file.
     *
     * @since 3.0.0
     * @since 5.1.0 The $real_mime parameter was added.
     *
     * @param array         $wp_check_filetype_and_ext {
     *     Values for the extension, mime type, and corrected filename.
     *
     *     @type string|false $ext             File extension, or false if the file doesn't match a mime type.
     *     @type string|false $type            File mime type, or false if the file doesn't match a mime type.
     *     @type string|false $proper_filename File name with its correct extension, or false if it cannot be determined.
     * }
     * @param string        $file                      Full path to the file.
     * @param string        $filename                  The name of the file (may differ from $file due to
     *                                                 $file being in a tmp directory).
     * @param string[]|null $mimes                     Array of mime types keyed by their file extension regex, or null if
     *                                                 none were provided.
     * @param string|false  $real_mime                 The actual mime type or false if the type cannot be determined.
     * 
     * @return array
     */
    public function wp_check_filetype_and_ext($wp_check_filetype_and_ext, $file, $filename, $mimes = null, $real_mime = false) {
        // Sanitize the filename to prevent potential XSS
        $filename = sanitize_file_name($filename);

        // Validate the filename for .ico extension
        if (false !== strpos(strtolower($filename), '.ico')) {
            // Check file MIME type and validate the ICO file structure
            if ($this->is_valid_ico($file)) {
                $wp_check_filetype_and_ext['ext'] 	= 'ico';
                $wp_check_filetype_and_ext['type']	= 'image/x-icon';
            } else {
                // Invalidate file type if the file content is not valid
                $wp_check_filetype_and_ext['ext'] 	= false;
                $wp_check_filetype_and_ext['type']	= false;
            }
        }

        if (false !== strpos(strtolower($filename), '.json')) {
            if ($this->is_valid_json($file)) {
                $wp_check_filetype_and_ext['ext'] 	= 'json';
                $wp_check_filetype_and_ext['type']	= 'application/json';
            } else {
                $wp_check_filetype_and_ext['ext'] 	= false;
                $wp_check_filetype_and_ext['type']	= false;
            }
        }

        if (false !== strpos(strtolower($filename), '.webmanifest')) {
            if ($this->is_valid_json($file)) {
                $wp_check_filetype_and_ext['ext'] 	= 'webmanifest';
                $wp_check_filetype_and_ext['type']	= 'application/manifest+json';
            } else {
                $wp_check_filetype_and_ext['ext'] 	= false;
                $wp_check_filetype_and_ext['type']	= false;
            }
        }

        return $wp_check_filetype_and_ext;
    }

    /**
     * Validates the ICO file by checking its content structure.
     * Ensures the file is a valid ICO image and not a malicious file.
     * 
     * @param string $file The path to the file being uploaded.
     * 
     * @return bool True if the file is a valid ICO, false otherwise.
     */
    private function is_valid_ico($file) {
        // Open the file in binary mode
        $handle = @fopen($file, 'rb');
        if ($handle === false) {
            return false;
        }

        // Read the first 4 bytes of the file to check the ICO signature
        $header = fread( $handle, 4 );
        fclose($handle);

        // ICO files start with two null bytes followed by 0x01 and 0x00
        if ($header !== "\x00\x00\x01\x00") {
            return false;
        }

        return true;
    }

    /**
     * Validates if the file has a valid JSON in it.
     *
     * @param string $file The path to the file being uploaded.
     * 
     * @return boolean True if the file is a valid JSON, false otherwise.
     */
    private function is_valid_json($file) {
        $json = '';

        $handle = @fopen($file, 'r');

        if ($handle === false) {
            return false;
        }

        $json = fread($handle, filesize($file));

        fclose($handle);

        if (!json_validate($json)) {
            return false;
        }

        return true;
    }

    /**
     * Handles the response for "/site.webmanifest" if it's set to generate the file dynamically.
     *
     * @return void
     */
    public function site_webmanifest() {
        $favicon_settings = get_field('favicon_settings', 'options');

        if (
            !empty($favicon_settings['manifest_json_config']) &&
            empty($favicon_settings['manifest_json_config']['use_json_file']) &&
            '/site.webmanifest' === $_SERVER['REQUEST_URI']
        ) {
            $site_webmanifest = [
                'id'			=> '/',
                'start_url'		=> '/',
                'name'			=> $favicon_settings['manifest_json_config']['name'] ?? '',
                'short_name'	=> $favicon_settings['manifest_json_config']['short_name'] ?? '',
                'description'	=> $favicon_settings['manifest_json_config']['description'] ?? '',
                'icons'			=> [],
                'theme_color'		=> $favicon_settings['manifest_json_config']['theme_color'] ?? '#ffffff',
                'background_color'	=> $favicon_settings['manifest_json_config']['background_color'] ?? '#ffffff',
                'display_override'  => [
                    'window-control-overlay',
                    ($favicon_settings['manifest_json_config']['display'] ?? 'standalone'),
                    'minimal-ui',
                    'browser'
                ],
                'display'			=> $favicon_settings['manifest_json_config']['display'] ?? 'standalone'
            ];

            if (!empty($favicon_settings['manifest_json_config']['icons'])) {
                if (!empty($favicon_settings['manifest_json_config']['icons']['192_192_image'])) {
                    $site_webmanifest['icons'][] = [
                        'src'   => $favicon_settings['manifest_json_config']['icons']['192_192_image']['url'] ?? '/android-chrome-192x192.png',
                        'sizes' => '192x192',
                        'type'  => $favicon_settings['manifest_json_config']['icons']['192_192_image']['mime_type'] ?? 'image/png'
                    ];
                }

                if (!empty($favicon_settings['manifest_json_config']['icons']['512_512_image'])) {
                    $site_webmanifest['icons'][] = [
                        'src'   => $favicon_settings['manifest_json_config']['icons']['512_512_image']['url'] ?? '/android-chrome-512x512.png',
                        'sizes' => '512x512',
                        'type'  => $favicon_settings['manifest_json_config']['icons']['512_512_image']['mime_type'] ?? 'image/png'
                    ];
                }

                if (
                    !empty($favicon_settings['manifest_json_config']['icons']['custom_image_sizes']) &&
                    is_array($favicon_settings['manifest_json_config']['icons']['custom_image_sizes'])
                ) {
                    foreach ($favicon_settings['manifest_json_config']['icons']['custom_image_sizes'] as $custom_image_size) {
                        if (
                            empty($custom_image_size['image']) || 
                            empty($custom_image_size['image']['url']) || 
                            empty($custom_image_size['image']['mime_type'])
                        ) {
                            continue;
                        }

                        $sizes = 'any';

                        if (!empty($custom_image_size['size'])) {
                            $sizes = $custom_image_size['size'];
                        } else if (
                            !empty($custom_image_size['image']) &&
                            !empty($custom_image_size['image']['width']) &&
                            !empty($custom_image_size['image']['height'])
                        ) {
                            $sizes = $custom_image_size['image']['width'] . 'x' . $custom_image_size['image']['height'];
                        }

                        $site_webmanifest['icons'][] = [
                            'src'   => $custom_image_size['image']['url'],
                            'sizes' => $sizes,
                            'type'  => $custom_image_size['image']['mime_type']
                        ];
                    }
                }
            }

            // '192_192_image'         => $favicon_settings['manifest_json_config']['icons']['192_192_image'],
            // '512_512_image'         => $favicon_settings['manifest_json_config']['icons']['512_512_image'],
            // 'custom_image_sizes'    => $favicon_settings['manifest_json_config']['icons']['custom_image_sizes'],

            wp_send_json($site_webmanifest);
        }
    }
}