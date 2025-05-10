<?php

namespace LH\Filters;

class Security 
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return Security
	 */
	public function __construct() {
        add_action('init', [$this, 'init'], 999);

        add_filter('the_generator', [$this, 'the_generator'], 999, 2);
    }

    /**
     * WordPress init action
     *
     * @return void
     */
    public function init() {
        global $wp_filter;

        remove_action('wp_head', 'wp_generator');

        if (
            !empty($wp_filter) &&
            !empty($wp_filter['wp_head']) &&
            !empty($wp_filter['wp_head']->callbacks) &&
            !empty($wp_filter['wp_head']->callbacks[10])
        ){
            foreach ($wp_filter['wp_head']->callbacks[10] as $idx => $idx_data) {
                if (str_ends_with($idx, 'addMetaData') && strlen($idx) > 12) {
                    unset($wp_filter['wp_head']->callbacks[10][$idx]);

                    break;
                }
            }
        }
    }

    /**
     * Filters the output of the XHTML generator tag for display.
     * 
     * @param String $generator_type
     * @param String $type
     * 
     * @return String
     */
    public function the_generator($generator_type, $type) {
        return '';
    }
}