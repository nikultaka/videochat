<?php

require __DIR__ . '/vendor/autoload.php';
class WCP_VideoChat_Controller {

    public function videochat_home() { 
        global $wpdb;
        if (!is_user_logged_in()) {
            wp_redirect('my-account');
            exit;    
        }        
        $joinroomtable = $wpdb->prefix.'joinroom';
        $roomtable = $wpdb->prefix.'room';    
        $onlineuserstable = $wpdb->prefix.'online_users ';    
        $user_id = get_current_user_id();   
        $room_id = '';
        if(isset($_GET['id']) && $_GET['id']!='') {
          $room_id = $_GET['id'];  
        } else {
          wp_redirect('create-room');
          exit;  
        }  

        $roomData = $wpdb->get_results("select * from ".$joinroomtable." where room_id =".$room_id." and user_id = ".$user_id." ");
        $room = $wpdb->get_results("select * from ".$roomtable." where id =".$room_id."");

        if(empty($roomData)) {
          wp_redirect('create-room');  
        }


        require_once plugin_dir_path(dirname(__FILE__)) . 'VideoChat/HTML/home.php';
        $s = ob_get_contents();
        ob_end_clean();   
        return $s;
    }

    public function videochat_dashboard() { 
        global $wpdb;
        if (!is_user_logged_in()) {
            //wp_redirect('my-account');
            //exit;   
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'VideoChat/HTML/dashboard.php';
        $s = ob_get_contents();
        ob_end_clean();   
        return $s;
    }    

    public function create_room() {
        if (!is_user_logged_in()) {
            wp_redirect('my-account');
            exit;  
        }        
        require_once plugin_dir_path(dirname(__FILE__)) . 'VideoChat/HTML/create_room.php';
        $s = ob_get_contents();
        ob_end_clean();   
        return $s;
    }

    public function join_room() {
        if (!is_user_logged_in()) {
            wp_redirect('my-account');
            exit;  
        }        
        require_once plugin_dir_path(dirname(__FILE__)) . 'VideoChat/HTML/join_room.php';
        $s = ob_get_contents();
        ob_end_clean();   
        return $s;
    }

    public function get_online_user() {
        global $wpdb;
        $user_id = '';
        $table = $wpdb->prefix.'online_users';
        $marbletable = $wpdb->prefix.'marble_position';
        $joinroomtable = $wpdb->prefix.'joinroom';
        if(isset($_POST['user_id'])) {      
            $user_id = $_POST['user_id']; 
            $room_id = $_POST['room_id'];
            $wpdb->query(" update ".$table." set status = '1',gm_updated = NOW() where user_id = ".$user_id." and room_id =".$room_id);   
            $userData = $wpdb->get_results("select * from ".$table." where status = '1' and user_id != ".$user_id." and room_id =".$room_id);     
            $marbleData = $wpdb->get_results("select * from ".$marbletable." where room_id =".$room_id);  
            $stream_array = array();
            $user_stream_array = array();
            if(!empty($userData)) {
                foreach($userData as $key => $value) {
                    $stream_array[] = $value->stream_id;
                    $user_stream_array[$key]['stream_id'] = $value->stream_id;
                    $user_stream_array[$key]['user_id'] = $value->user_id;
                    $user_stream_array[$key]['user_id'] = $value->user_id;
                }   
            }  
            $currentTurnData = $wpdb->get_results("select * from ".$table." where current_turn = '1' and status = '1' and room_id =".$room_id);
            $next_turn = '';
            if(!empty($currentTurnData)) {
                $next_turn = $currentTurnData[0]->user_id;
            }

            /**************************/    
            $joinroomData = $wpdb->get_results("select DISTINCT ".$joinroomtable.".*,".$table.".color 
                from ".$joinroomtable." 
                inner join ".$table." on ".$table.".user_id = ".$joinroomtable.".user_id
                and ".$table.".room_id =".$room_id."
                where ".$joinroomtable.".room_id =".$room_id." order by id asc
            ");  

            $user_position = '';
            $color_1 = '';
            $color_2 = '';
            $color_3 = '';
            $color_4 = '';
            foreach($joinroomData as $join_key => $join_value) {
                if($next_turn == $join_value->user_id)  {
                    $user_position = $join_key+1;     
                }            
                if($join_key == 0) {
                    $color_1 = $join_value->color;
                } else if($join_key == 1) {
                    $color_2 = $join_value->color;
                } else if($join_key == 2) {
                    $color_3 = $join_value->color;
                } else if($join_key == 3) {
                    $color_4 = $join_value->color;
                }
            }   
            /**************************/

            $count = 1;
            $loadUserWithoutVideo = array();
            if(!empty($userData)) {
                foreach($userData as $key => $value) {
                    if($value->is_video_enable == "0" && $value->user_id != $user_id) {
                        $loadUserWithoutVideo[$count] = $value->user_id;        
                    }  else {
                        $loadUserWithoutVideo[$count] = '';
                    } 
                    $count++;  
                }    
            }
            //self::check_active_status($table,$joinroomtable);
            echo json_encode(array("status"=>1,'stream_data'=>$stream_array,'user_stream'=>$user_stream_array,'current_marble_id'=>$marbleData,'current_turn'=>$user_position,'turn_user_id'=>$next_turn,'loadUserWithoutVideo'=>$loadUserWithoutVideo,"color_1"=>$color_1,"color_2"=>$color_2,"color_3"=>$color_3,"color_4"=>$color_4));

        } else {       
            echo json_decode(array("status"=>0));
        }                 
        exit;               
    }

    public function add_stream_user() {     
        global $wpdb;
        $user_id = '';
        $table = $wpdb->prefix.'online_users';
        $marbletable = $wpdb->prefix.'marble_position';
        $joinroomtable = $wpdb->prefix.'joinroom';
        $type = $_POST['type'];
        $room_id = $_POST['room_id'];  
        $user_id = $_POST['user_id'];  
        $stream_id = $_POST['stream_id'];  
        $drone_id = $_POST['drone_id'];

        $is_video_enable = 1;
        if($stream_id == '') {
            $is_video_enable = 0; 
        }
 
        if(isset($_POST['user_id']) && $type == 'local') {          
                
            $wpdb->query(" update ".$table." set status = '1',stream_id='".$drone_id."',gm_updated = NOW(),is_video_enable = '".$is_video_enable."' where user_id = ".$user_id." and room_id =".$room_id);        
          
            $joinroomData = $wpdb->get_results("select * from ".$joinroomtable." where room_id =".$room_id." order by id asc");  

            $user_position = '';
            foreach($joinroomData as $join_key => $join_value) {
                if( $join_value->user_id == $user_id)  {
                    $user_position = $join_key;     
                }              
            }
            
            self::check_active_status($table,$joinroomtable);
            echo json_encode(array("status"=>1,'user_position'=>$user_position));
        } else if($type == 'remote') {
            $joinroomData = $wpdb->get_results("select distinct ".$table.".stream_id,".$joinroomtable.".id 
                from ".$joinroomtable." 
                inner join ".$table." on ".$table.".user_id = ".$joinroomtable.".user_id and ".$table.".room_id =".$room_id."
                where ".$joinroomtable.".room_id =".$room_id." order by id asc
           ");   

            $user_position = '';  

            foreach($joinroomData as $join_key => $join_value) {
                if( $drone_id == $join_value->stream_id)  {
                    $user_position = $join_key;     
                }              
            }  
            self::check_active_status($table,$joinroomtable);
            echo json_encode(array("status"=>1,'user_position'=>$user_position));
        } else {
            echo json_decode(array("status"=>0));
        }      
        exit;  
    }


    public function check_active_status($table,$joinroomtable) {
        global $wpdb;    
        /*$inactiveUsersData = $wpdb->get_results("select * from ".$table." where gm_updated < (NOW() - INTERVAL 15 SECOND) and stream_id!='' and status = '1'  "); 
            if(!empty($inactiveUsersData)) {
                foreach($inactiveUsersData as $inactiveKey => $inactiveValue) {
                    $inactiveUserID = $inactiveValue->user_id;    
                    $wpdb->query("update ".$table." set status ='0' where user_id = ".$inactiveUserID." ");
                    $wpdb->query("delete from ".$joinroomtable." where user_id = ".$inactiveUserID." ");
                }
            }*/
    }

    public function done() {
        global $wpdb;
        $table = $wpdb->prefix.'marble_position';
        $users_table = $wpdb->prefix.'online_users';
        if(isset($_POST['user_id'])) {      
            $userID = $_POST['user_id'];  
            $room_id = $_POST['room_id'];
            $userData = $wpdb->get_results("select * from ".$users_table." where status = '1' and room_id = '".$room_id."' order by id ASC");
            //user_id !=".$userID." and 

            $wpdb->query(" update ".$users_table." set current_turn = '0' ");

            $next_turn = '';
            if(!empty($userData)) {
                foreach($userData as $userKey => $userValue) {
                    if($userValue->current_turn == "1") {
                        if(isset($userData[$userKey+1])) {
                            $nextUserData = $userData[$userKey+1];
                            $next_turn = $nextUserData->user_id;
                        } else {
                            $nextUserData = $userData[0];
                            $next_turn = $nextUserData->user_id;
                        }
                        $wpdb->query(" update ".$users_table." set current_turn = '1' where user_id =".$next_turn." ");
                        break;
                    }
                }
            }

            /*$next_turn = '';
            if(!empty($userData)) {
               $next_turn = $userData[0]->user_id;
               $wpdb->query(" update ".$users_table." set current_turn = '0' ");
               $wpdb->query(" update ".$users_table." set current_turn = '1' where user_id =".$next_turn." ");
            }*/
            echo json_encode(array("status"=>1,'next_turn'=>$next_turn));
        } else {
            echo json_decode(array("status"=>0));
        }  
        exit;
    }

    public function update_marble_position() {
        global $wpdb;
        $user_id = '';      
        $table = $wpdb->prefix.'marble_position';  
        $users_table = $wpdb->prefix.'online_users';    
        if(isset($_POST['user_id'])) {               
            $userID = $_POST['user_id'];    
            $objectID = $_POST['objectID'];  
            $movedType = $_POST['movedType'];  
            $roomID = $_POST['room_id'];  
            $marbleData = $wpdb->get_results("select * from ".$table." where user_id =".$userID." and marble_id = '".$movedType."' ");
            if(!empty($marbleData)) {             
                $previousPosition = $marbleData[0]->current_position;
                $wpdb->query(" update ".$table." set current_position = '".$objectID."',previous_position = '".$previousPosition."' where user_id =".$userID." and marble_id = '".$movedType."' and room_id = ".$roomID);
            } else {     
                $wpdb->insert($table,array('user_id'=>$userID,'room_id'=>$roomID,'marble_id'=>$movedType,'current_position'=>$objectID));
            }       
            echo json_encode(array("status"=>1,'next_turn'=>$next_turn));
        } else {
            echo json_decode(array("status"=>0));
        }        
        exit;  
    }

    public function add_room() {
        global $wpdb;
        $table = $wpdb->prefix.'room';
        $joinroomtable = $wpdb->prefix.'joinroom';
        $onlineusertable = $wpdb->prefix.'online_users';
        $marbletable = $wpdb->prefix.'marble_position';
        $options = array(
           'cluster' => 'ap2',
           'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
           '2a1428184af6e786468c',
           'da3f6faa53e7911f7900',
           '1061526',
           $options
        );
        if(isset($_POST['user_id'])) {      
            $userID = $_POST['user_id'];
            $room_name = $_POST['room_name'];
            $wpdb->insert($table,array('user_id'=>$userID,'status'=>1,'name'=>$room_name)); 
            $lastid = $wpdb->insert_id;
            $pusher->trigger('room_users_'.$lastid, 'my_event',$userID);
            $wpdb->query(" delete from ".$marbletable." where user_id = ".$userID);
            $wpdb->query("update ".$onlineusertable." set status = '0' where user_id = ".$userID." ");
            $wpdb->query("delete from ".$joinroomtable." where user_id = ".$userID." ");
            $wpdb->insert($joinroomtable,array('room_id'=>$lastid,'is_admin'=>1,'user_id'=>$userID));

            $wpdb->query(" insert into ".$onlineusertable." (room_id,stream_id,user_id,is_admin,status,current_turn,gm_created,gm_updated) values(".$lastid.",'',".$userID.",'1','1','1',NOW(),NOW())  ");

            echo json_encode(array("status"=>1,'room_id'=>$lastid));   
        } else {         
            echo json_encode(array("status"=>0));
        }  
        exit;
    }       

    public function added_room() {  
        global $wpdb;
        $joinroomtable = $wpdb->prefix.'joinroom';
        $roomtable = $wpdb->prefix.'room';
        $table = $wpdb->prefix.'online_users';
        $marbletable = $wpdb->prefix.'marble_position';
        $options = array(
           'cluster' => 'ap2',
           'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
           '2a1428184af6e786468c',
           'da3f6faa53e7911f7900',
           '1061526',
           $options
        );
        if(isset($_POST['user_id'])) {  
            $current_user = wp_get_current_user();  
            $displayName = $current_user->data->display_name;  
            $userID = $_POST['user_id'];
            $room_id = $_POST['room_name'];
            $roomData = $wpdb->get_results("select * from ".$roomtable." where id =".$room_id." and status = '1' ");
            $is_locked = $roomData[0]->is_locked;
            $roomData = $wpdb->get_results("select * from ".$joinroomtable." where room_id =".$room_id." ");

            if(count($roomData)<4 && $is_locked != "1") {     
                $room_user_id = $roomData[0]->user_id;    
                $is_admin = 0;
                $current_turn = 0;
                if($room_user_id == $userID) {
                    $is_admin = 1;
                    $current_turn = 1;
                }             
                $triggerData = array("id"=>$userID,'name'=>$displayName);
                $pusher->trigger('room_users_'.$room_id, 'my_event',$triggerData);  
                $wpdb->query("delete from ".$marbletable." where user_id = ".$userID);
                $wpdb->query("delete from ".$table." where user_id = ".$userID." ");
                $wpdb->query("delete from ".$joinroomtable." where user_id = ".$userID." ");

                $isUserDataExist = $wpdb->get_results("select * from ".$table." where user_id =".$userID." and room_id = '".$room_id."' ");

                if(count($isUserDataExist) == 0) {  
                    $wpdb->query(" insert into ".$table." (room_id,stream_id,user_id,is_admin,status,current_turn,gm_created,gm_updated) values(".$room_id.",'',".$userID.",".$is_admin.",'1',".$current_turn.",NOW(),NOW())  ");
                    $wpdb->insert($joinroomtable,array('room_id'=>$room_id,'is_admin'=>$is_admin,'user_id'=>$userID));    
                }
                echo json_encode(array("status"=>1));    
            } else {
                echo json_encode(array("status"=>0,"msg"=> "Room is full or locked"));
            }
        } else {     
            echo json_encode(array("status"=>0,"msg"=> "something went wrong"));
        }  
        exit;
    }     

    public function get_color_data() {
        global $wpdb;        
        $room_id = $_POST['room_id'];
        $table = $wpdb->prefix.'online_users';
        $usertable = $wpdb->prefix.'users';
        $userData = $wpdb->get_results("select ".$table.".*,".$usertable.".display_name from ".$table." inner join ".$usertable." on ".$usertable.".ID =  ".$table.".user_id  where room_id =".$room_id." order by id ASC ");

        echo json_encode(array('status'=>1,'data'=>$userData));
        die;
    }

    public function save_color() {
        global $wpdb;   
        $table = $wpdb->prefix.'online_users';
        $tableroom = $wpdb->prefix.'room';
        $user_one_yellow = $_POST['user_one_yellow'];
        $user_one_blue = $_POST['user_one_blue'];
        $user_one_red = $_POST['user_one_red'];
        $user_one_green = $_POST['user_one_green'];

        $user_two_yellow = $_POST['user_two_yellow'];
        $user_two_blue = $_POST['user_two_blue'];
        $user_two_red = $_POST['user_two_red'];
        $user_two_green = $_POST['user_two_green'];

        $user_three_yellow = $_POST['user_three_yellow'];
        $user_three_blue = $_POST['user_three_blue'];
        $user_three_red = $_POST['user_three_red'];
        $user_three_green = $_POST['user_three_green'];

        $user_four_yellow = $_POST['user_four_yellow'];
        $user_four_blue = $_POST['user_four_blue'];
        $user_four_red = $_POST['user_four_red'];
        $user_four_green = $_POST['user_four_green'];
        $room_id      = $_POST['room_id'];

        $wpdb->query("update ".$tableroom." set is_locked = '1' where id = ".$room_id." ");

        $options = array(
           'cluster' => 'ap2',
           'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
           '2a1428184af6e786468c',
           'da3f6faa53e7911f7900',
           '1061526',
           $options
        );

        $userData = $wpdb->get_results("select * from ".$table." where room_id =".$room_id." order by id ASC ");         

        $user_color = '';
        $user_access = array();
        $use_one_id = '';
        $use_two_id = '';
        if(!empty($userData)) {  
            foreach ($userData as $key => $value) {
                $id = $value->id;
                $user_id = $value->user_id;

                if($key == 0) {
                    $use_one_id = $user_id;
                }
                if($key == 1) {
                    $use_two_id = $user_id;
                }

                if($key == 0) {
                    $json_string = json_encode(array($user_one_yellow,$user_one_blue,$user_one_red,$user_one_green));
                    $user_access[$user_id] = $json_string;
                    $user_color = $json_string;
                } else if($key == 1) {
                    $json_string = json_encode(array($user_two_yellow,$user_two_blue,$user_two_red,$user_two_green));  
                    $user_access[$user_id] = $json_string;
                    $user_color = $json_string;
                } else if($key == 2) {
                    $json_string = json_encode(array($user_three_yellow,$user_three_blue,$user_three_red,$user_three_green)); 
                    $user_access[$user_id] = $json_string;
                    $user_color = $json_string; 
                } else if($key == 3) {    
                    $json_string = json_encode(array($user_four_yellow,$user_four_blue,$user_four_red,$user_four_green)); 
                    $user_access[$user_id] = $json_string;
                    $user_color = $json_string;
                }    
                $wpdb->query("update ".$table." set marble_access = '".$user_color."' where id = ".$id." ");
            }     

            if(count($userData) == 2) {           
                $json_string = json_encode(array($user_one_yellow,$user_one_blue,'1',$user_one_green));
                $user_access[$use_one_id] = $json_string;
                $json_string = json_encode(array($user_two_yellow,$user_two_blue,$user_two_red,'1'));
                $user_access[$use_two_id] = $json_string;
            } else if(count($userData) == 3) {
                $json_string = json_encode(array($user_one_yellow,$user_one_blue,$user_one_red,'1'));
                $user_access[$use_one_id] = $json_string;
            }
        }    
        $pusher->trigger('marble_access_'.$room_id, 'my_event',$user_access);        
        echo json_encode(array('status'=>1));
        die;
    }

    public function reset_game() {
        global $wpdb;   
        $table = $wpdb->prefix.'marble_position';    
        $room_id = $_POST['room_id'];
        $wpdb->query("delete from ".$table." where room_id =".$room_id);
        echo json_encode(array('status'=>1));
        die;
    }

    public function reset_marble_position() {
        global $wpdb;   
        $table = $wpdb->prefix.'marble_position';    
        $room_id = $_POST['room_id'];
        $marble_id = $_POST['marble_id'];
        $wpdb->query("delete from ".$table." where room_id ='".$room_id."' and  marble_id= '".$marble_id."'  ");
        echo json_encode(array('status'=>1));
        die;
    }

    public function roll_dice() {
        $result = '';
        if(isset($_POST['result'])) {
            $room_id = $_POST['room_id'];
            $result = $_POST['result'];    
            $options = array(
               'cluster' => 'ap2',
               'encrypted' => true
            );
            $pusher = new Pusher\Pusher(
               '2a1428184af6e786468c',
               'da3f6faa53e7911f7900',
               '1061526',
               $options
            );
            $pusher->trigger('dice_channel_'.$room_id, 'my_event',$result);
            echo json_encode(array('status'=>1));
        } else {
            echo json_encode(array('status'=>0));
        }
        die;
    }

}

function custom_blockusers_init() {
    $url = basename($_SERVER['REQUEST_URI']);  
    if ( is_user_logged_in() && $url!='video-chat' && $url!='admin-ajax.php' ) {
        //wp_redirect('video-chat');
        //exit;    
    }
}

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
        show_admin_bar(false);
}
add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

function app_output_buffer() {
    ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');


add_action('init','custom_blockusers_init'); 

$WCP_VideoChat_Controller = new WCP_VideoChat_Controller();
add_shortcode('VideoChat', array($WCP_VideoChat_Controller, 'videochat_home'));
add_shortcode('CreateRoom', array($WCP_VideoChat_Controller, 'create_room'));
add_shortcode('JoinRoom', array($WCP_VideoChat_Controller, 'join_room'));
add_shortcode('Dashboard', array($WCP_VideoChat_Controller, 'videochat_dashboard'));
/// Ajax
add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::store_data', Array($WCP_VideoChat_Controller, 'store_data'));
add_action('wp_ajax_WCP_VideoChat_Controller::store_data', Array($WCP_VideoChat_Controller, 'store_data'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::get_online_user', Array($WCP_VideoChat_Controller, 'get_online_user'));
add_action('wp_ajax_WCP_VideoChat_Controller::get_online_user', Array($WCP_VideoChat_Controller, 'get_online_user'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::add_stream_user', Array($WCP_VideoChat_Controller, 'add_stream_user'));
add_action('wp_ajax_WCP_VideoChat_Controller::add_stream_user', Array($WCP_VideoChat_Controller, 'add_stream_user'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::update_marble_position', Array($WCP_VideoChat_Controller, 'update_marble_position'));
add_action('wp_ajax_WCP_VideoChat_Controller::update_marble_position', Array($WCP_VideoChat_Controller, 'update_marble_position'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::done', Array($WCP_VideoChat_Controller, 'done'));
add_action('wp_ajax_WCP_VideoChat_Controller::done', Array($WCP_VideoChat_Controller, 'done'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::add_room', Array($WCP_VideoChat_Controller, 'add_room'));
add_action('wp_ajax_WCP_VideoChat_Controller::add_room', Array($WCP_VideoChat_Controller, 'add_room'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::added_room', Array($WCP_VideoChat_Controller, 'added_room'));
add_action('wp_ajax_WCP_VideoChat_Controller::added_room', Array($WCP_VideoChat_Controller, 'added_room'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::save_color', Array($WCP_VideoChat_Controller, 'save_color'));
add_action('wp_ajax_WCP_VideoChat_Controller::save_color', Array($WCP_VideoChat_Controller, 'save_color'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::reset_game', Array($WCP_VideoChat_Controller, 'reset_game'));
add_action('wp_ajax_WCP_VideoChat_Controller::reset_game', Array($WCP_VideoChat_Controller, 'reset_game'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::reset_marble_position', Array($WCP_VideoChat_Controller, 'reset_marble_position'));
add_action('wp_ajax_WCP_VideoChat_Controller::reset_marble_position', Array($WCP_VideoChat_Controller, 'reset_marble_position'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::roll_dice', Array($WCP_VideoChat_Controller, 'roll_dice'));
add_action('wp_ajax_WCP_VideoChat_Controller::roll_dice', Array($WCP_VideoChat_Controller, 'roll_dice'));

add_action('wp_ajax_nopriv_WCP_VideoChat_Controller::get_color_data', Array($WCP_VideoChat_Controller, 'get_color_data'));
add_action('wp_ajax_WCP_VideoChat_Controller::get_color_data', Array($WCP_VideoChat_Controller, 'get_color_data'));
  
?>