<?php

namespace LH\Plugins_Integrations;

class Google_Captcha_Integration
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return Google_Captcha_Integration
	 */
	public function __construct() {
        if ($this->is_plugin_active()) {
            add_filter('option_gglcptch_options', [$this, 'option_gglcptch_options'], 999, 2);
            
        } else {
            add_action('admin_notices', [$this, 'admin_notices']);
            return;
        }
    }

    /**
     * Check if reCaptcha by BestWebSoft plugin is active
     *
     * @return Boolean
     */
    private function is_plugin_active() {
        return function_exists('gglcptch_get_response');
    }

    /**
     * Set keys if there are no keys set in the plugin's config
     *
     * @param mixed $value
     * @param string $option
     * 
     * @return mixed
     */
    public function option_gglcptch_options($value, $option) {
        $recaptcha_keys = lh_get_recaptcha_keys();

        if (
            $option == 'gglcptch_options' &&
            !empty($recaptcha_keys)
        ) {
            $value['public_key']        = $recaptcha_keys['site_key'];
            $value['private_key']       = $recaptcha_keys['secret_key'];
            $value['recaptcha_version'] = 'v3';
            $value['score_v3']          = '0.5';
            $value['hide_badge']        = 1;
            $value['use_globally']      = 1;
            $value['keys_verified']     = true;

            unset($value['need_keys_verified_check']);
        }

        return $value;
    }

    /**
     * Show admin notice when reCaptcha by BestWebSoft is not installed/activated
     *
     * @return Void
     */
    public function admin_notices() {
        echo '
        <div class="updated">
            <p>
            ' . sprintf(
                    __('<strong>%s</strong> requires <strong><a href="https://en-gb.wordpress.org/plugins/google-captcha/" target="_blank">reCaptcha by BestWebSoft</a></strong> plugin to be installed and activated on your site.',
                    LH_PLUGIN_SLUG),
                    __CLASS__
                ) . '
            </p>
        </div>';
    }
}