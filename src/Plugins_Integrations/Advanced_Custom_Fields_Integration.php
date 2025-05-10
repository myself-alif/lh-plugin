<?php

namespace LH\Plugins_Integrations;

class Advanced_Custom_Fields_Integration
{
    /**
     * Setup action & filter hooks
     *
     * @return Advanced_Custom_Fields_Integration
     */
    public function __construct()
    {
        if ($this->is_plugin_active()) {
            add_action('init',      [$this, 'acf_blocks']);
            add_action('acf/init',  [$this, 'acf_options_page']);
            // add_action('acf/init', [$this, 'google_maps_api_key']);

            add_filter('acf/settings/save_json',    [$this, 'acf_settings_save_json'],  999,    1);
            add_filter('acf/settings/load_json',    [$this, 'acf_settings_load_json'],  999,    1);

            add_filter('block_type_metadata',       [$this, 'block_type_metadata'],     999,    1);
            add_filter('block_categories_all',      [$this, 'block_categories_all'],    999,    2);
            add_filter('render_block',              [$this, 'render_block'],            999,    2);
            add_filter('allowed_block_types_all',   [$this, 'blacklist_blocks'],        999,    2);

            add_filter('acf/fields/wysiwyg/toolbars',   [$this, 'acf_fields_wysiwyg_toolbars'],     999,    1);
            add_filter('acf/fields/post_object/query',  [$this, 'acf_fields_post_object_query'],    999,    3);
        } else {
            add_action('admin_notices', [$this, 'show_acf_version_notice']);
            return;
        }
    }

    /**
     * Check if ACF plugin is active
     *
     * @return Boolean
     */
    private function is_plugin_active()
    {
        return class_exists('ACF');
    }

    /**
     * Register Gutenberg blocks using ACF functions
     *
     * @return Void
     */
    public function acf_blocks()
    {

        new \LH\ACF_Blocks\Title();
        new \LH\ACF_Blocks\Latest_Insights();
        new \LH\ACF_Blocks\Cta_Banner();
    }

    /**
     * Register options pages
     *
     * @return Void
     */
    public function acf_options_page()
    {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        acf_add_options_page([
            'parent_slug'       => 'options-general.php',
            'page_title'        => __('Lh Solutions Theme settings', LH_PLUGIN_SLUG),
            'menu_title'        => __('Lh Solutions Theme settings', LH_PLUGIN_SLUG),
            'menu_slug'         => 'lh-theme-settings-options-general',
            'capability'        => 'manage_options',
            'show_in_graphql'   => true
        ]);

        acf_add_options_page([
            'parent_slug'       => 'themes.php',
            'page_title'        => __('Lh Solutions Theme settings', LH_PLUGIN_SLUG),
            'menu_title'        => __('Lh Solutions Theme settings', LH_PLUGIN_SLUG),
            'menu_slug'         => 'lh-theme-settings-themes',
            'capability'        => 'manage_options',
            'show_in_graphql'   => true
        ]);
    }

    /**
     * Set Google Maps API key
     *
     * @return Void
     */
    public function google_maps_api_key()
    {
        acf_update_setting('google_api_key', LH_GOOGLE_MAPS_KEY);
    }

    /**
     * Set path where ACF saves field groups
     *
     * @param string $path
     * 
     * @return string
     */
    public function acf_settings_save_json($path)
    {
        $path = LH_PLUGIN_PATH . '/config/ACF_Fields';

        return $path;
    }

    /**
     * Set path where ACF loads field groups
     *
     * @param string $path
     * 
     * @return string
     */
    public function acf_settings_load_json($path)
    {
        $path = LH_PLUGIN_PATH . '/config/ACF_Fields';

        return $path;
    }

    /**
     * Filters the metadata provided for registering a block type.
     *
     * @since 5.7.0
     *
     * @param array $metadata Metadata for registering a block type.
     */
    public function block_type_metadata($metadata)
    {
        if (str_starts_with($metadata['name'], 'lh/')) {
            $block_slug = str_replace('lh/', '', $metadata['name']);

            if (empty($metadata['$schema'])) {
                $metadata['$schema'] = 'https://schemas.wp.org/trunk/block.json';
            }

            if (empty($metadata['apiVersion'])) {
                $metadata['apiVersion'] = 2;
            }

            if (empty($metadata['category'])) {
                $metadata['category'] = LH_PLUGIN_SLUG;
            }

            if (empty($metadata['icon'])) {
                $metadata['icon'] = LH_LOGO_SVG;
            }

            if (empty($metadata['supports'])) {
                $metadata['supports'] = [
                    'anchor'        => true,
                    'align'         => false,
                    'align_content' => false,
                    'align_text'    => false,
                    'full_height'   => false,
                    'mode'          => true,
                    'multiple'      => true,
                    'color'         => [
                        'text'          => false,
                        'background'    => false,
                        'link'          => false,
                    ]
                ];
            }

            if (empty($metadata['style'])) {
                $metadata['style'] = ['lh-acf-block-' . $block_slug . '-css'];
            }

            if (empty($metadata['editorStyle'])) {
                $metadata['editorStyle'] = ['lh-acf-block-' . $block_slug . '-editor-css'];
            }

            if (empty($metadata['script'])) {
                $metadata['script'] = ['lh-acf-block-' . $block_slug . '-js'];
            }

            if (empty($metadata['editorScript'])) {
                $metadata['editorScript'] = ['lh-acf-block-' . $block_slug . '-editor-js'];
            }

            if (empty($metadata['acf'])) {
                $metadata['acf'] = [
                    'mode'              => 'edit',
                    'renderCallback'    => '\LH\ACF_Blocks\\' . str_replace('-', '_', ucwords(strtolower($block_slug), '-')) . '::render_block'
                ];
            }

            if (
                !empty($metadata['example']) &&
                !empty($metadata['example']['attributes']) &&
                !empty($metadata['example']['attributes']['data'])
            ) {
                $example_data_replacements = $this->get_example_data_replacements();

                $metadata['example']['attributes']['data'] = $this->replace_example_data($metadata['example']['attributes']['data'], $example_data_replacements);
            }
        }

        return $metadata;
    }

    /**
     * Returns and array with all the posible replacement values
     * for the example data of the blocks
     *
     * @return array
     */
    private function get_example_data_replacements()
    {
        $example_data_replacements = [
            'LH_LOREM_IPSUM_1_TITLE'            => 'Lorem ipsum dolor sit amet',
            'LH_LOREM_IPSUM_2_TITLE'            => 'Consectetur adipiscing elit',
            'LH_LOREM_IPSUM_1_EMAIL'            => 'joe.bloggs@domain.com',
            'LH_LOREM_IPSUM_1_SHORT_COPY'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'LH_LOREM_IPSUM_1_SHORT_PARAGRAPH'  => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
            'LH_LOREM_IPSUM_2_SHORT_PARAGRAPH'  => '<p>In ut purus sed metus sodales tincidunt. Vivamus at euismod sem, et egestas velit.</p>',
            'LH_LOREM_IPSUM_1_COPY'             => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut purus sed metus sodales tincidunt. Vivamus at euismod sem, et egestas velit. Mauris egestas lacus a eros commodo, mattis finibus orci molestie. In consequat erat mollis quam aliquam ornare sed ornare enim.',
            'LH_LOREM_IPSUM_1_PARAGRAPH'        => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut purus sed metus sodales tincidunt. Vivamus at euismod sem, et egestas velit. Mauris egestas lacus a eros commodo, mattis finibus orci molestie. In consequat erat mollis quam aliquam ornare sed ornare enim.</p>',
            'LH_LOREM_IPSUM_2_COPY'             => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut purus sed metus sodales tincidunt. Vivamus at euismod sem, et egestas velit. Mauris egestas lacus a eros commodo, mattis finibus orci molestie. In consequat erat mollis quam aliquam ornare sed ornare enim. Phasellus purus ex, maximus vitae eros nec, tincidunt finibus sem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Suspendisse vitae nunc sit amet elit efficitur sollicitudin sit amet non massa.\n\nMauris tempor aliquam est, in vulputate turpis consequat et. Vestibulum eleifend enim non diam fermentum semper. Vivamus eget erat sed risus mollis venenatis. Suspendisse potenti. Phasellus placerat lectus sit amet eros eleifend porta.',
            'LH_LOREM_IPSUM_2_PARAGRAPHS'       => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ut purus sed metus sodales tincidunt. Vivamus at euismod sem, et egestas velit. Mauris egestas lacus a eros commodo, mattis finibus orci molestie. In consequat erat mollis quam aliquam ornare sed ornare enim. Phasellus purus ex, maximus vitae eros nec, tincidunt finibus sem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Suspendisse vitae nunc sit amet elit efficitur sollicitudin sit amet non massa.</p><p>Mauris tempor aliquam est, in vulputate turpis consequat et. Vestibulum eleifend enim non diam fermentum semper. Vivamus eget erat sed risus mollis venenatis. Suspendisse potenti. Phasellus placerat lectus sit amet eros eleifend porta.</p>',
            'LH_CTA'                            => [
                'url'   => '#',
                'title' => 'CTA'
            ],
            'LH_URL'                            => '#',
            'LH_NAME'                           => 'John Doe',
            'LH_JOB'                            => 'Job title',
        ];

        if (!is_admin()) {
            return $example_data_replacements;
        }

        $lh_latest_post = get_posts([
            'post_type'         => \LH\Setup::$posts_post_type,
            'posts_per_page'    => 1,
            'post_status'       => 'publish'
        ]);

        if (!empty($lh_latest_post[0])) {
            $example_data_replacements['LH_LATEST_POST'] = $lh_latest_post[0]->ID;
        } else {
            $example_data_replacements['LH_LATEST_POST'] = 0;
        }

        $lh_latest_image = get_posts([
            'post_type'         => 'attachment',
            'posts_per_page'    => 1,
            'post_status'       => null,
            'post_mime_type'    => ['image/webp', 'image/jpeg', 'image/gif', 'image/png', 'image/bmp']
        ]);

        if (!empty($lh_latest_image[0])) {
            $example_data_replacements['LH_LATEST_IMAGE'] = $lh_latest_image[0]->ID;
        } else {
            $example_data_replacements['LH_LATEST_IMAGE'] = 0;
        }

        $lh_latest_file = get_posts([
            'post_type'         => 'attachment',
            'posts_per_page'    => 1,
            'post_status'       => null,
            'post_mime_type'    => 'application/pdf'
        ]);

        if (!empty($lh_latest_file[0])) {
            $example_data_replacements['LH_PLG_LATEST_FILE'] = $lh_latest_image[0];
        } else {
            $example_data_replacements['LH_PLG_LATEST_FILE'] = 0;
        }

        if (class_exists('GFForms')) {
            $forms_raw = \GFAPI::get_forms();

            if (!empty($forms_raw) && !empty($forms_raw[0]) && !empty($forms_raw[0]['id'])) {
                $example_data_replacements['LH_LATEST_GFORM'] = $forms_raw[0]['id'];
            } else {
                $example_data_replacements['LH_LATEST_GFORM'] = 0;
            }
        } else {
            $example_data_replacements['LH_LATEST_GFORM'] = 0;
        }

        return apply_filters('lh_acf_block_get_example_data_replacements', $example_data_replacements);
    }

    /**
     * Replace example data
     *
     * @param array $data
     * @param array $example_data_replacements
     * 
     * @return array
     */
    private function replace_example_data($data, $example_data_replacements)
    {
        foreach ($data as $variable => &$value) {
            if (
                !empty($variable) &&
                !empty($value) && !is_array($value) &&
                isset($example_data_replacements[$value])
            ) {
                $value = $example_data_replacements[$value];
            }

            if (
                (
                    (is_string($variable) && !empty($variable)) ||
                    is_numeric($variable)
                ) &&
                !empty($value) && is_array($value)
            ) {
                $value = $this->replace_example_data($value, $example_data_replacements);
            }
        }

        unset($value);

        return $data;
    }

    /**
     * Register a new category for the ACF blocks
     * 
     * @param Array $categories
     * @param \WP_Post $post
     * 
     * @return Array
     */
    public function block_categories_all($categories, $post)
    {
        return array_merge(
            [
                [
                    'slug'  => 'wp-lh-solutions-plugin',
                    'title' => 'Lh Solutions'
                ]
            ],
            $categories
        );
    }

    /**
     * Filter blocks before they are rendered
     * 
     * @param String $block_content
     * @param Array  $block
     * 
     * @return String
     */
    public function render_block($block_content, $block)
    {
        $non_acf_blocks_without_wrapper = ['core/block', 'core/list-item'];

        if (
            (
                !empty($block['blockName']) &&
                !preg_match('~^lh/~', $block['blockName']) &&                    // Any other block except the blocks from this plugin
                !in_array($block['blockName'], $non_acf_blocks_without_wrapper)     // Non ACF blocks that shouldn't be wrapped
            ) ||
            (empty($block['blockName']) && $block['innerHTML'] != "\n\n")           // WordPress classic content block
        ) {
            $block_content = '<div class="lh-non-acf-block lh-non-acf-block-' . str_replace('/', '-', $block['blockName']) . '">' . $block_content . '</div>';
        }

        return $block_content;
    }

    /**
     * Blacklist unwanted blocks.
     * 
     * @return array;
     * 
     * Sourced from: https://rudrastyh.com/gutenberg/remove-default-blocks.html
     * Created: 08/03/2023
     */
    public function blacklist_blocks($allowed_blocks, $editor_context)
    {
        $blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();

        //$whiteList = ['core/table', 'core/embed','core/block','core/list','core/list-item','core/heading','core/paragraph','core/preformatted','core/pullquote','core/quote','core/image','core/legacy-widget','core/widget-group'];
        $blackList = ['core/archives', 'core/avatar', 'core/calendar', 'core/categories', 'core/comment-author-name', 'core/comment-content', 'core/comment-date', 'core/comment-edit-link', 'core/comment-reply-link', 'core/comment-template', 'core/comments', 'core/comments-pagination', 'core/comments-pagination-next', 'core/comments-pagination-numbers', 'core/comments-pagination-previous', 'core/comments-title', 'core/cover', 'core/file', 'core/gallery', 'core/home-link', 'core/latest-comments', 'core/latest-posts', 'core/loginout', 'core/navigation', 'core/navigation-link', 'core/navigation-submenu', 'core/page-list', 'core/pattern', 'core/post-author', 'core/post-author-biography', 'core/post-comments-form', 'core/post-content', 'core/post-date', 'core/post-excerpt', 'core/post-featured-image', 'core/post-navigation-link', 'core/post-template', 'core/post-terms', 'core/post-title', 'core/query', 'core/query-no-results', 'core/query-pagination', 'core/query-pagination-next', 'core/query-pagination-numbers', 'core/query-pagination-previous', 'core/query-title', 'core/read-more', 'core/rss', 'core/search', 'core/shortcode', 'core/site-logo', 'core/site-tagline', 'core/site-title', 'core/social-link', 'core/tag-cloud', 'core/template-part', 'core/term-description', 'core/audio', 'core/button', 'core/buttons', 'core/code', 'core/column', 'core/columns', 'core/freeform', 'core/group', 'core/html', 'core/media-text', 'core/missing', 'core/more', 'core/nextpage', 'core/separator', 'core/social-links', 'core/spacer', 'core/text-columns', 'core/verse', 'core/video', 'core/post-comments'];

        foreach ($blackList as $item) {
            unset($blocks[$item]);
        }

        // return the new list of allowed blocks
        return array_keys($blocks);
    }

    /**
     * Customize the WYSIWYG toolbars
     * 
     * @param array $toolbars
     * 
     * @return array
     */
    public function acf_fields_wysiwyg_toolbars($toolbars)
    {
        $toolbars['LH Basic'] = [
            '1' => [
                'bold',
                'italic',
                'underline',
                'strikethrough',
                '|',
                'superscript',
                'subscript',
                '|',
                'link',
                '|',
                'pastetext',
                'removeformat',
                '|',
                'undo',
                'redo'
            ]
        ];

        $toolbars['LH'] = [
            '1' => [
                'formatselect',
                '|',
                'alignleft',
                'aligncenter',
                'alignright',
                'alignjustify',
                'alignnone',
                '|',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                '|',
                'superscript',
                'subscript',
                '|',
                'link',
                'blockquote',
                '|',
                'bullist',
                'numlist',
                'outdent',
                'indent',
                '|',
                'pastetext',
                'removeformat',
                '|',
                'undo',
                'redo'
            ]
        ];

        return $toolbars;
    }

    /**
     * ACF fields - Post obejct - get posts query parameters
     *
     * @param array   $args
     * @param array   $field
     * @param integer $post_id
     * 
     * @return array
     */
    public function acf_fields_post_object_query($args, $field, $post_id)
    {
        $args['search_columns'] = ['post_title'];

        return $args;
    }

    /**
     * Show admin notice when ACF is not installed/activated
     *
     * @return Void
     */
    public function show_acf_version_notice()
    {
        echo '
        <div class="updated">
            <p>
            ' . sprintf(
            __(
                '<strong>%s</strong> requires <strong><a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">ACF</a></strong> plugin to be installed and activated on your site.',
                LH_PLUGIN_SLUG
            ),
            __CLASS__
        ) . '
            </p>
        </div>';
    }
}
