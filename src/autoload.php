<?php

spl_autoload_register('lh_autoload');

/**
 * Load plugin classes
 *
 * @param String $class_name
 * 
 * @return Void
 */
function lh_autoload($class_name) {
    if (strpos($class_name, 'LH') !== false) {
        $class_name = str_replace('LH', '', $class_name);
        $classes_dir = realpath( plugin_dir_path( __FILE__ ) );
        $class_file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';

        if (file_exists($classes_dir . $class_file)) {
            require_once($classes_dir . $class_file);
        }
    }
}