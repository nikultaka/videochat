<?php 
$user_id = get_current_user_id();   
?>
		<div class="row">
		    <div class="col-md-10">
		        <input type="text" class="form-control" id="room_name" placeholder="Enter Room Name " />       
		    </div>
		    <div class="col-md-2">
		    	<button class="btn btn-primary" type="button" onclick="create_room();" >Go!</button>
		    </div>	
		</div>

		<script>
			function create_room() {
				var room_name = $("#room_name").val();
				if(room_name == "") {
					toastr.error('Please enter roomname');
					return false;
				} else {
					$(".loader").show();
					$.ajax({      
			            type: 'POST',
			            url: '<?php echo admin_url('admin-ajax.php'); ?>',
			            data: {"action": "WCP_VideoChat_Controller::add_room","user_id":"<?php echo $user_id; ?>","room_name":room_name},
			            success: function (data) {   
			                var result =  JSON.parse(data);
			                if (result.status == 1) {  
			                	$("#room_name").val('');
			                    document.location.href='video-chat?id='+result.room_id;
			                }
			            } 
			        }); 		
				}
				
			}
		</script>	
</body>
</html>