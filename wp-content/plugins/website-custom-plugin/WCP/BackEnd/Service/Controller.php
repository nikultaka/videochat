<?php

class WCP_BackEnd_VideoChat_Controller {

    public function index() {
        ob_start();
        global $wpdb;
        $colortable = $wpdb->prefix.'videochat_color';
        $sql = "SELECT * FROM ".$colortable;
        $color_list = $wpdb->get_results($sql, "ARRAY_A");

        include(dirname(__FILE__) . "/html/video_color.php");
        $s = ob_get_contents();
        ob_end_clean();
        print $s;
    }
    
    public function add_color_setting() {
        global $wpdb;
        $user_one = '';
        $user_two = '';
        $user_three = '';
        $user_four = '';
        $colortable = $wpdb->prefix.'videochat_color';
        if(isset($_POST['user_one']) && $_POST['user_one']!='') {
            $user_one = $_POST['user_one'];    
        }
        if(isset($_POST['user_two']) && $_POST['user_two']!='') {
            $user_two = $_POST['user_two'];    
        }
        if(isset($_POST['user_three']) && $_POST['user_three']!='') {
            $user_three = $_POST['user_three'];    
        }
        if(isset($_POST['user_four']) && $_POST['user_four']!='') {
            $user_four = $_POST['user_four'];    
        }
        $colorData = $wpdb->get_results("select * from ".$colortable." ");
        if(!empty($colorData)) {    
            $wpdb->update($colortable,array('user_one'=>$user_one,'user_two'=>$user_two,'user_three'=>$user_three,'user_four'=>$user_four),array('id'=>1));
        } else {
            $wpdb->insert($colortable,array('user_one'=>$user_one,'user_two'=>$user_two,'user_three'=>$user_three,'user_four'=>$user_four));
        }
        echo json_encode(array('status'=>1)); 
        exit;
    }

    function add_menu_pages() {
        return false;
        add_menu_page('Video Chat', 'Video Chat', 'manage_options', 'videochat', Array("WCP_BackEnd_VideoChat_Controller", "index"));
    }

}

$WCP_VideoChat_Controller = new WCP_BackEnd_VideoChat_Controller();

add_action('admin_menu', array($WCP_VideoChat_Controller, 'add_menu_pages'));

add_action('wp_ajax_WCP_BackEnd_VideoChat_Controller::add_color_setting', Array($WCP_VideoChat_Controller, 'add_color_setting'));
add_action('wp_ajax_nopriv_WCP_BackEnd_VideoChat_Controller::add_color_setting', array($WCP_VideoChat_Controller, 'add_color_setting'));

?>
