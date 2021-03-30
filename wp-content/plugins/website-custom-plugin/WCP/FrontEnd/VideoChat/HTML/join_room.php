<?php 
global $wpdb;
$user_id = get_current_user_id();  
$table = $wpdb->prefix.'room'; 
$roomsData = $wpdb->get_results("select * from ".$table." ");  
?>
		<div class="row">
		    <div class="col-md-10">
	            <select name="room_name" id="room_name" class="form-control pt-2" style="margin-top: 50px;">
	            	<option value="">--Select Room--</option>
	            	<?php if(!empty($roomsData)) { 
	            		foreach ($roomsData as $key => $value) {
	            	?>
	            		<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
	            	<?php } } ?>
	            </select>
		    </div>
		    <div class="col-md-2">
		        <button class="btn btn-primary" type="button" onclick="join_room();" >Go</button>
		    </div>
		</div>
		<script>
			$(document).ready(function() {
				$('#room_name').select2();
			});
			function join_room() {
				var room_name = $("#room_name").val();
				if(room_name == "") {
					toastr.error('Please enter roomname');
					return false;
				} else {
					$(".loader").show();
					$.ajax({      
			            type: 'POST',
			            url: '<?php echo admin_url('admin-ajax.php'); ?>',
			            data: {"action": "WCP_VideoChat_Controller::added_room","user_id":"<?php echo $user_id; ?>","room_name":room_name},
			            dataType : 'json',
			            success: function (data) {   
			                if (data.status == 1) {  
			                    document.location.href='video-chat?id='+room_name;
			                } else {
			                	alert(data.msg);
			                	document.location.href='create-room';
			                	return false;
			                }
			            }    
			        }); 		
				}
				
			}
		</script>	
</body>
</html>