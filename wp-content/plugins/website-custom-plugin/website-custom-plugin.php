<?php
/**
* Plugin Name: Website Custom Plugin
* Plugin URI: 
* Description: Custom Plugin
* Version: 1.0.0
* Author: Dino Bartolome    
* Author URI: 
* License: GPL2
*/

define( 'WCP_PLUGIN_VERSION', '1.0.0' );
define( 'WCP_PLUGIN_DOMAIN', 'website-custom-plugin' );
define( 'WCP_PLUGIN_URL', WP_PLUGIN_URL . '/Website-Custome-Plugin' );
function create_database_table_for_website_custome_plugin() {
	global $table_prefix, $wpdb;

	$tblname = 'online_users';
	$tblnameRoom = $table_prefix ."room";
	$joinRoom = $table_prefix ."joinroom";
	$wp_track_table = $table_prefix . "$tblname";
	$wp_marble_position_table = $table_prefix . "marble_position";
	$color_table = $table_prefix ."videochat_color";
	#Check to see if the table exists already, if not, then create it
    if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
	{
		$sql = "CREATE TABLE `".$wp_track_table ."` ( ";
		$sql .= "  `id`  int(11)   NOT NULL AUTO_INCREMENT, ";
		$sql .= "  `room_id`  int(11)   NOT NULL, ";
        $sql .= "  `stream_id`  varchar(245)   NOT NULL, ";	
        $sql .= "  `user_id`  int(11)   NOT NULL, ";
        $sql .= "  `status`  tinyint(1)   NOT NULL, ";
        $sql .= "  `current_turn`  int(11)   NOT NULL, ";
        $sql .= "  `is_admin`  int(11)   NOT NULL, ";
        $sql .= "  `is_video_enable`  tinyint(1)   NOT NULL, ";
        $sql .= "  `color`  varchar(255)   NULL, ";    
		$sql .= "  `gm_created`  DATETIME NOT NULL,PRIMARY KEY (id),  ";
        $sql .= "  `gm_updated`  DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ";
		$sql .= ");";
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql);  
	}    
	if($wpdb->get_var( "show tables like '$tblnameRoom'  ") != $tblnameRoom) 
	{
		$sql = "CREATE TABLE `".$tblnameRoom ."` ( ";
		$sql .= "  `id`  int(11)   NOT NULL AUTO_INCREMENT, ";
		$sql .= "  `user_id`  int(11)   NOT NULL, ";
        $sql .= "  `name`  varchar(255)   NOT NULL, ";
        $sql .= "  `status`  tinyint(1)   NOT NULL,PRIMARY KEY (id) ";
		$sql .= ");";  
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql);  
	}  
	if($wpdb->get_var( "show tables like '$joinRoom' ") != $joinRoom) 
	{ 
		$sql = "CREATE TABLE `".$joinRoom ."` ( ";
		$sql .= "  `id`  int(11)   NOT NULL AUTO_INCREMENT, ";
		$sql .= "  `is_admin`  tinyint(11)   NOT NULL, ";       
        $sql .= "  `room_id`  int(11)   NOT NULL, ";
        $sql .= "  `user_id`  int(11)   NOT NULL,PRIMARY KEY (id) ";
		$sql .= ");";   
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql);  
	}    
	if($wpdb->get_var( "show tables like '$wp_marble_position_table' " ) != $wp_marble_position_table) 
	{
		$sql = "CREATE TABLE `".$wp_marble_position_table."` ( ";
		$sql .= "  `id`  int(11)   NOT NULL AUTO_INCREMENT, ";
		$sql .= "  `room_id`  int(11)   NOT NULL, ";
        $sql .= "  `marble_id`  varchar(255)   NOT NULL, ";   
        $sql .= "  `user_id`  int(11)   NOT NULL, ";   
        $sql .= "  `current_position`  varchar(255)   NOT NULL,PRIMARY KEY (id) ";
        $sql .= ");";          
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql);  
	}

	if($wpdb->get_var( "show tables like '$color_table' " ) != $color_table) 
	{
		$sql = "CREATE TABLE `".$color_table."` ( ";
		$sql .= "  `id`  int(11)   NOT NULL AUTO_INCREMENT, ";
        $sql .= "  `user_one`  varchar(255)   NOT NULL, ";   
        $sql .= "  `user_two`  varchar(255)   NOT NULL, ";   
        $sql .= "  `user_three`  varchar(255)   NOT NULL, ";   
        $sql .= "  `user_four`  varchar(255)   NOT NULL ";   
        $sql .= "  ,PRIMARY KEY (id) ";
        $sql .= ");";          
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta($sql);  
	}         
}

register_activation_hook( __FILE__, 'create_database_table_for_website_custome_plugin' );
include_once(dirname(__FILE__)."/WCP/BackEnd/Service/Controller.php");
include_once(dirname(__FILE__)."/WCP/FrontEnd/VideoChat/Controller.php");

