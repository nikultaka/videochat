<?php

class WCP_BackEnd_VideoChat_Controller {

    public function index() {
        ob_start();
        global $wpdb;
        $roomable = $wpdb->prefix.'room';
        $sql = "SELECT * FROM ".$roomable;
        $room_list = $wpdb->get_results($sql, "ARRAY_A");

        include(dirname(__FILE__) . "/html/video_color.php");
        $s = ob_get_contents();
        ob_end_clean();
        print $s;
    }
    
    public function delete_room() { 
        global $wpdb;
        $roomable = $wpdb->prefix.'room';
        $id = $_REQUEST['id'];
        $sql = "delete FROM ".$roomable." where id = ".$id;
        $wpdb->query($sql);
        echo json_encode(array('status'=>1)); 
        exit;
    }

    function add_menu_pages() {
        add_menu_page('Video Chat', 'Video Chat', 'manage_options', 'videochat', Array("WCP_BackEnd_VideoChat_Controller", "index"));
    }

}

$WCP_VideoChat_Controller = new WCP_BackEnd_VideoChat_Controller();

add_action('admin_menu', array($WCP_VideoChat_Controller, 'add_menu_pages'));

add_action('wp_ajax_WCP_BackEnd_VideoChat_Controller::delete_room', Array($WCP_VideoChat_Controller, 'delete_room'));
add_action('wp_ajax_nopriv_WCP_BackEnd_VideoChat_Controller::delete_room', array($WCP_VideoChat_Controller, 'delete_room'));

?>
