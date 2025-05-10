<?php

namespace LH;

class Install_Uninstall
{
    /**
     * Plugin activation
     *
     * @return Void
     */
    static function activation_hook() {
        // global $wpdb, $wp_roles;
        //require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // For a possible future feature of having custom users just for the API
        // $sql = 'CREATE TABLE `' . lh_API_TABLE_API_USERS . '` (
        //     `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        //     `username` VARCHAR(255) NOT NULL,
        //     `password` VARCHAR(255) NOT NULL,
        //     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //     `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL,
        //     PRIMARY KEY (`id`)
        //     ) ENGINE = InnoDB ' . $charset_collate . ';';
        // dbDelta($sql);
        
        // $wp_roles->add_cap('administrator', 'access_lh_api');

        // add_role(
        //     'lh_bas_system_admin_user',
        //     __( 'System admin User' ),
        //     [
        //         'read' => true
        //     ]    
        // );
    }

    /**
     * Plugin deactivation
     * 
     * @return Void
     */
    static function deactivation_hook() {
        
    }

    /**
     * Plugin uninstall
     * 
     * @return Void
     */
    static function uninstall_hook() {
        // global $wpdb, $wp_roles;

        // $wpdb->query('DROP TABLE IF EXISTS ' . lh_API_TABLE_API_USERS);

        // $wp_roles->remove_cap('administrator', 'access_lh_api');
        // remove_role('lh_bas_system_admin_user');
    }    
}