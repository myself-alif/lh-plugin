<?php

namespace LH\Plugins_Integrations;

class Gravity_Forms_Integration
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return Gravity_Forms_Integration
	 */
	public function __construct() {
        if ($this->is_plugin_active()) {
            add_action('init',          [$this, 'init'],            1);
            add_action('gform_loaded',  [$this, 'gform_loaded'],    5);

            add_filter('option_rg_gforms_captcha_public_key',                       [$this, 'option_rg_gforms_captcha_public_key']);
            add_filter('option_rg_gforms_captcha_site_key',                         [$this, 'option_rg_gforms_captcha_public_key']);
            add_filter('option_rg_gforms_captcha_private_key',                      [$this, 'option_rg_gforms_captcha_private_key']);
            add_filter('option_rg_gforms_captcha_secret_key',                       [$this, 'option_rg_gforms_captcha_private_key']);
            add_filter('option_gform_recaptcha_keys_status',                        [$this, 'option_gform_recaptcha_keys_status']);
            add_filter('option_gravityformsaddon_gravityformsrecaptcha_settings',   [$this, 'option_gravityformsaddon_gravityformsrecaptcha_settings'], 999, 2);

            add_filter('gform_add_field_buttons',   [$this, 'gform_add_field_buttons'],     99, 1);
            add_filter('gform_form_settings_menu',  [$this, 'gform_form_settings_menu'],    99, 2);
            add_filter('gform_pre_render',          [$this, 'gform_pre_render'],            99, 3);
            add_filter('gform_field_content',       [$this, 'gform_field_content'],         99, 5);
            add_filter('gform_field_value',         [$this, 'gform_field_value'],           99, 3);

            // add_filter('gform_validation_message',  [$this, 'gform_validation_message'],    99, 2);
        } else {
            add_action('admin_notices', [$this, 'show_gf_admin_notice']);
            return;
        }
    }

    /**
     * Check if Gravity Forms plugin is active
     *
     * @return Boolean
     */
    private function is_plugin_active() {
        return class_exists('GFForms');
    }

    /**
     * Register Gravity Forms recaptcha keys if there are any set for the current domain
     *
     * @return void
     */
    public function register_recaptcha_keys() {
        $recaptcha_keys = lh_get_recaptcha_keys();

        if (
            !empty($recaptcha_keys) &&
            !defined('GF_RECAPTCHA_V3_SITE_KEY') &&
            !defined('GF_RECAPTCHA_V3_SECRET_KEY')
        ) {
            define('GF_RECAPTCHA_V3_SITE_KEY',      $recaptcha_keys['site_key']);
            define('GF_RECAPTCHA_V3_SECRET_KEY',    $recaptcha_keys['secret_key']);
        }
    }

    /**
     * Plugins loaded action hook
     *
     * @return void
     */
    public function init() {
        $this->register_recaptcha_keys();
    }

    /**
     * Load Gravity Forms addons
     *
     * @return Void
     */
    public function gform_loaded() {
        if (!method_exists('GFForms', 'include_addon_framework')) {
            add_action('admin_notices', [$this, 'show_gf_admin_notice']);
            return;
        }
 
        // \GFAddOn::register('\LH\GF_Addons\New_Feature');
    }

    /**
     * Gravity Forms retrive Site key (public key) from options
     *
     * @param String $key
     * 
     * @return String
     */
    public function option_rg_gforms_captcha_public_key($key = '') {
        if (defined('GF_RECAPTCHA_PUBLIC_KEY') && GF_RECAPTCHA_PUBLIC_KEY != '') {
            return GF_RECAPTCHA_PUBLIC_KEY;
        }

        return $key;
    }

    /**
     * Gravity Forms retrive Secret key (private key) from options
     *
     * @param String $key
     * 
     * @return String
     */
    public function option_rg_gforms_captcha_private_key($key = '') {
        if (defined('GF_RECAPTCHA_PRIVATE_KEY') && GF_RECAPTCHA_PRIVATE_KEY != '') {
            return GF_RECAPTCHA_PRIVATE_KEY;
        }

        return $key;
    }

    /**
     * Gravity Forms retrive Captcha keys status from options
     *
     * @param Boolean $key
     * 
     * @return Boolean
     */
    public function option_gform_recaptcha_keys_status($status = false) {
        if ($status == true) {
            return true;
        } else {
            $public_key     = get_option('rg_gforms_captcha_public_key');
            $private_key    = get_option('rg_gforms_captcha_private_key');

            if ($public_key && $private_key) {
                return true;
            }
        }

        return $status;
    }

    /**
     * Set keys if there are no keys set in the plugin's config
     *
     * @param mixed  $value
     * @param string $option
     * 
     * @return mixed
     */
    public function option_gravityformsaddon_gravityformsrecaptcha_settings($value, $option) {
        $recaptcha_keys = lh_get_recaptcha_keys();

        if (
            $option == 'gravityformsaddon_gravityformsrecaptcha_settings' &&
            !empty($recaptcha_keys)
        ) {
            $value['site_key_v3']               = $recaptcha_keys['site_key'];
            $value['secret_key_v3']             = $recaptcha_keys['secret_key'];
            $value['score_threshold_v3']        = '0.5';
            $value['disable_badge_v3']          = 1;
            $value['recaptcha_keys_status_v3']  = '1';
        }

        return $value;
    }

    /**
     * Add/edit/remove "Add Field" buttons from the form editor's floating toolbox.
     *
     * @param array $field_groups The field groups, including group name, label and fields.
     */
    public function gform_add_field_buttons($field_groups) {
        $standard_fields_to_be_removed  = ['html', 'section', 'page'];
        $advanced_fields_to_be_removed  = ['name', 'address', 'list', 'multiselect', 'password', 'username', 'captcha'];
        $field_groups_to_be_removed     = ['post_fields', 'pricing_fields'];

        foreach ($field_groups as $field_group_index => &$field_group) {
            if (in_array($field_group['name'], $field_groups_to_be_removed)) {
                unset($field_groups[$field_group_index]);

                continue;
            }

            if (
                $field_group['name'] == 'standard_fields' &&
                !empty($field_group['fields'])
            ) {
                foreach ($field_group['fields'] as $field_index => $field) {
                    if (in_array($field['data-type'], $standard_fields_to_be_removed)) {
                        unset($field_group['fields'][$field_index]);
                    }
                }
            }

            if (
                $field_group['name'] == 'advanced_fields' &&
                !empty($field_group['fields'])
            ) {
                foreach ($field_group['fields'] as $field_index => $field) {
                    if (in_array($field['data-type'], $advanced_fields_to_be_removed)) {
                        unset($field_group['fields'][$field_index]);
                    }
                }
            }
        }
        unset($field_group);

        return $field_groups;
    }

    /**
     * Settings menu filter
     *
     * @param Array $menu_items
     * 
     * @return Array
     */
    public function gform_form_settings_menu($menu_items) {
        /** Fix GF live validation plugin menu registration */
        if ($menu_items[40]['name'] == 'lv_form_setting') {
            $menu_items[(int)array_key_last($menu_items) + 1] = $menu_items[40];

            $menu_items[40] = [
                'name'  => 'personal-data',
                'label' => 'Personal Data',
                'query' =>  [
                    'nid' => NULL
                ],
                'capabilities' => [
                    'gravityforms_edit_forms'
                ]
            ];
        }
        
        return $menu_items;
    }

    /**
     * Gravity forms filter applied on $form object before the form is rendered
     *
     * @param Array   $form
     * @param Boolean $ajax
     * @param Array   $field_values
     * 
     * @return Array
     */
    public function gform_pre_render($form, $ajax, $field_values) {
        foreach ($form['fields'] as $form_field) {
            $form_field->cssClass .= ($form_field->cssClass != '' ? ' ' : '') . 'gfield--size-' . $form_field->size;
        }

        return $form;
    }

    /**
     * Gravity forms filter applied to create the HTML for a field
     *
     * @param String $field_content
     * @param \GField $field
     * @param String $value
     * @param Integer $form_id
     * 
     * @return String
     */
    public function gform_field_content($field_content, $field, $value, $form_id) {
        if ($field->type == 'email') {
            $field_content = str_replace('type=\'text\'', 'type="email"', $field_content);
        }

        if ($field->type == 'phone') {
            $field_content = str_replace('type=\'text\'', 'type="tel"', $field_content);
        }

        return $field_content;
    }

    /**
     * This filter is executed before displaying each field and
     * can be used to dynamically populate fields with a default value.
     * 
     * This filter can also be used to pre-select or pre-check
     * drop down, radio button and checkbox items.
     * 
     * Note: This filter requires that the “Allow field to be populated dynamically”
     * option is checked in the field editor’s advanced tab.
     *
     * @param String $value
     * @param \GF_Field $field
     * @param String $name
     * 
     * @return String
     */
    public function gform_field_value($value, $field, $name) {

        return $value;
    }

    /**
     * This filter is executed when a form fails validation,
     * before the validation message is displayed.
     * Use this filter to change the default validation message.
     *
     * @param String $message
     * @param Array  $form
     * 
     * @return String
     */
    public function gform_validation_message($message, $form) {
        return "<h2 class='gform_submission_error{$hide_summary_class}'><span class='gform-icon gform-icon--close'></span>" . esc_html__( 'Oops! Something went wrong. Please make sure you\'ve filled all the fields correctly and try again.', 'gravityforms' ) . '</h2>';;
    }

    /**
     * Show admin notice when Gravity Forms is not installed/activated
     *
     * @return Void
     */
    public function show_gf_admin_notice() {
        echo '
        <div class="updated">
            <p>
            ' . sprintf(
                    __('<strong>%s</strong> requires <strong><a href="https://www.gravityforms.com/" target="_blank">Gravity Forms</a></strong> plugin to be installed and activated on your site.',
                    LH_PLUGIN_SLUG),
                    __CLASS__
                ) . '
            </p>
        </div>';
    }
}