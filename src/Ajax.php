<?php

namespace LH;

class Ajax
{
    /**
	 * Setup action & filter hooks
	 *
	 * @return Ajax
	 */
	public function __construct() {
        // add_action('wp_ajax_lh_delete_endpoints_group', [$this, 'delete_endpoints_group']);
    }

    /**
     * Delete endpoints group
     * 
     * @return Void
     */
    public function delete_endpoints_group() {
        // global $wpdb;

        // $result = [
        //     'status' => '',
        //     'message' => ''
        // ];

        // if (!wp_verify_nonce( $_REQUEST['nonce'], "lh_confirmation_modal_delete_btn")) {
        //     $result['status'] = 'error';
        //     $result['message'] = 'Server error!';
        //     print json_encode($result);
        //     wp_die();
        // }

        // if (!isset($_POST['id'])) {
        //     $result['status'] = 'error';
        //     $result['message'] = 'Incorrect parameters!';
        //     print json_encode($result);
        //     wp_die();
        // }

        // $delete_endpoints = $wpdb->delete(lh_API_TABLE_ENDPOINTS, ['group_id' => $_POST['id']], '%d');
        // $delete_endpoints_groups = $wpdb->delete(lh_API_TABLE_ENDPOINTS_GROUPS, ['id' => $_POST['id']], '%d');

        // if ($delete_endpoints === false || $delete_endpoints_groups === false) {
        //     $result['status'] = 'error';
        //     $result['message'] = 'There was a problem while trying to delete the group!';
        //     print json_encode($result);
        //     wp_die();
        // }

        // $result['status'] = 'success';
        // $result['message'] = 'Endpoints group was deleted!';

        // print json_encode($result);
        // wp_die();
    }
}
