<?php

namespace LH\Filters;

class JavaScript_Scripts 
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return JavaScript_Scripts
	 */
	public function __construct() {
        add_filter('wp_head',       [$this, 'wp_head'],         0, 0);
        add_filter('wp_body_open',  [$this, 'wp_body_open'],    0, 0);
        add_filter('wp_footer',     [$this, 'wp_footer'],       0, 0);
    }

    /**
     * Prints scripts or data in the head tag on the front end.
     * 
     * @since 1.5.0
     *  
     * @return void
     */
    public function wp_head() {
        print get_field('lh_header_scripts', 'options');
    }

    /**
     * Fires the wp_body_open action.
     * 
     * See {@see 'wp_body_open'}.
     * 
     * @since 5.2.0
     * 
     * @return void
     */
    public function wp_body_open() {
        print get_field('lh_after_body_scripts', 'options');
    }

    /**
     * Fires the wp_footer action.
     * 
     * See {@see 'wp_footer'}.
     * 
     * @since 1.5.1
     * 
     * @return void
     */
    public function wp_footer() {
        print get_field('lh_footer_scripts', 'options');
    }
}