<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>


    <h1>Video Rooms</h1>    
    <table id="myTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="80%" align="center">Room Name</th>
                <th width="20%" align="center">Action</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($room_list  as $key => $value) { ?>
                <tr>
                    <td><?php echo $value['name']; ?></td>
                    <td><button class="btn btn-primary" onclick="delete_room(<?php echo $value['id']; ?>);">Delete</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>    



<script type="text/javascript">
    $(document).ready(function() {
        jQuery('#myTable').DataTable();
    });
    function delete_room(id) {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_BackEnd_VideoChat_Controller::delete_room",id:id},
            success: function (data) {
                var result =  JSON.parse(data);
                if (result.status == 1) {
                    document.location.reload();
                }
            }
        });
    }
</script>
