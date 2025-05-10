<?php

namespace LH\Filters;

class Post 
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return Post
	 */
	public function __construct() {
        add_filter('template_include', [$this, 'template_include'], 999, 3);
    }

    /**
	 * Filters the path of the current template before including it.
	 *
	 * @param string $template The path of the template to include.
     * 
     * @return string
	 */
    public function template_include($template) {
        $GLOBALS['current_theme_template'] = basename($template);

        return $template;
    }
}