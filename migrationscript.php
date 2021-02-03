<?php 

include("wp-load.php");

function seconddb() {
     global $seconddb;
     $seconddb = new wpdb('doadmin','wzwoqihgsytfqgh4','defaultdb','private-db-mysql-sfo3-38875-do-user-8610871-0.b.db.ondigitalocean.com');
}
add_action('init','seconddb');

global $wpdb;

$query = "select cpml.initial_payment,cu.display_name,cpml.name,mu.* from cxg_pmpro_memberships_users as mu
	inner join cxg_users as cu on mu.user_id = cu.ID 
	inner join cxg_pmpro_membership_levels as cpml on cpml.id = mu.membership_id
	inner join cxg_pmpro_membership_orders  as cpmo on cpmo.user_id = mu.user_id
";
$arr_data = $wpdb->get_results($query);

if(!empty($arr_data)) {
	foreach($arr_data as $key => $value) {
	 	//$initial_payment = $value->initial_payment;
		//$query = "insert into Wo_Manage_Pro (type,price,status) values ('star','".$initial_payment."','1')";
		//$wpdb->query($query);  
		$user_login = $value->cu.user_login;
		$user_pass = $value->user_pass;
		$user_nicename = $value->cu.user_nicename;
		$user_email = $value->cu.user_email;
		$user_registered = $value->cu.user_registered;
		$user_activation_key = $value->cu.user_activation_key;
		$user_status = $value->cu.user_status;
		$display_name = $value->cu.display_name;
		
		$query = "select * from Wo_Users where email = '".$user_email."' ";
		$emailexist = $seconddb->get_results($query);
		
		if(!empty($emailexist)) {
			
		}
		
	} 
}

?>
