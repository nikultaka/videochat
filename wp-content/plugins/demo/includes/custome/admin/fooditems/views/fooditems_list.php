<?php 
$plugin_dir_path = plugin_dir_url( __FILE__ );
//echo $plugin_dir_path; die;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $plugin_dir_path.'../../../../../assets/css/custom.css'; ?>" >
<script type="text/javascript" src="<?php echo $plugin_dir_path.'../../../../../assets/js/jquery.min.js'; ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $plugin_dir_path.'../../../../../assets/css/bootstrap.min.css'; ?>" >
<script type="text/javascript" src="<?php echo $plugin_dir_path.'../../../../../assets/js/bootstrap.min.js'; ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $plugin_dir_path.'../../../../../assets/css/jquery.dataTables.min.css'; ?>" />
<script type="text/javascript" src="<?php echo $plugin_dir_path.'../../../../../assets/js/datatables.min.js'; ?>"></script>

<div class="loading">
    <img  class="loader" src="<?php echo $plugin_dir_path.'../../../../../assets/images/loading.gif'; ?>" />
</div>
<?php
$product_list = get_food_itemproduct_list();

?>
<div class="flex_container" style="padding-top:20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>FoodItems List</h3>
        </div>
        
        <hr style="background-color:#000000; height:2px;">
        <div style="padding-bottom:10px;">
            <div style="clear:both;"></div>
        </div>
        <div class="">
            <table id='fooditem-table' class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10%" class="all">Name</th>
                        <th width="40%" class="all">Description</th>
                        <th width="10%" class="all">Categories</th>
                        <th width="10%" class="all">Price</th>
                        <th width="10%" class="all">Date</th>
                        <th width="20%" class="all">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($product_list as $key=>$product){ ?>
                    <tr>
                        <td><?php echo $product['item_name']; ?></td>
                        <td><?php echo $product['item_description']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo $product['date']; ?></td>
                        <td><div> <button class="btn btn-info" onclick="fooditems_record_edit(<?php echo $product['item_id']; ?>)">edit</button> &nbsp;<button class="btn btn-danger" onclick="fooditems_record_delete(<?php echo $product['item_id']; ?>)" > delete</button> </div></td>
                    </tr>
                        <?php
                    }
                    ?>
                    
                </tbody>
            </table>
            
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $ = jQuery;
        $('#fooditem-table').dataTable();
    
    });
    
    function fooditems_record_delete(id) {
        if (confirm("Are you sure?")) {
            $(".loading").show();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "rpress_delete_fooditems_record", id: id},
                success: function (data) {
                    var result = JSON.parse(data);
                    
                    if (result.status == 1) {
                        window.location.href = 'admin.php?page=fooditem'; 
                    }
                    $(".loading").hide();
                }
            });
        }
        return false;
    }
    function fooditems_record_edit(id){
        if(id > 0){
             window.location.href = 'admin.php?page=rpress-add_fooditem&item_id='+id; 
        }
    }
</script>
