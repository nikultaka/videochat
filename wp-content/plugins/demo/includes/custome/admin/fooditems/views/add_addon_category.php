<?php 
$plugin_dir_path = plugin_dir_url( __FILE__ );
?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $plugin_dir_path.'../../../../../assets/css/custom.css'; ?>" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<?php
$cat_list = get_all_addon_subcategory_by_merchant();

?>
<div class="loading">
    <img  class="loader" src="<?php echo $plugin_dir_path.'../../../../../assets/images/loading.gif'; ?>" />
</div>

<div class="flex_container" style="padding-top:20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>Addon Category List</h3>
        </div>
        <div class="col-sm-6">
            <input type="submit" value="Add Addon Category" data-toggle="modal" data-target="#AddAddonCategoryModal" style="margin-top: 15px;float:right;" class="btn btn-info btn-lg" />
        </div>

        <hr style="background-color:#000000; height:2px;">
        <div style="padding-bottom:10px;">
            <div style="clear:both;"></div>
        </div>
        <div class="">
            <table id='service-table' class="table table-bordered">
                <thead>
                    <tr>
                        <th class="all">Category Name</th>
                        <th class="all">Status</th>
                        <th class="all">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($cat_list as $list){ ?>
                    <tr>
                        <td><?php echo $list->subcategory_name; ?></td>
                        <td><?php echo $list->status; ?></td>
                        <td>
                            <div>
                                <input type="submit" value="Edit" data-id="<?php echo $list->subcat_id; ?>" class="btn btn-success EditAddonCategory" > &nbsp;
                                <a class="btn btn-danger" onclick="category_record_delete(<?php echo $list->subcat_id; ?>)" data-id ="<?php echo $list->cat_id; ?>" >delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="AddAddonCategoryModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form id="add_addoncategory_form" name="add_addoncategory_form" action="" method="post" class="form-horizontal" onclick="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Addon Category</h4>

                </div>

                    <input type="hidden" name="id" id="id" value=""/>
                    <input type="hidden" value="rpress_insert_addoncategory" name="action" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-10 col-sm-offset-1">
                                <div class="form-group">
                                <label>Food Category Name</label>
                                <input type="text" name="category_name" class="form-control" id="category_name" value="">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="8"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="publish">Publish</option>
                                    <option value="pending">Pending for review</option>
                                    <option value="draft">Draft</option>
                                </select>

                            </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="submit" id="btnAddaddonCategory" name="btnAddaddonCategory" class="btn btn-default" value="Save" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </form>
        </div>
    </div>


<style>
    .has-error{
        border: 1px solid red !important;
    }
</style>


<script>

    $(document).ready(function () {
        
        $('#btnAddaddonCategory').on('click', function () {

            var error = 0;
            
            if ($('#category_name').val().trim() == '') {
                $('#category_name').addClass('has-error');
                error++;
            } else {
                $('#category_name').removeClass('has-error');
            }

            if ($('#status').val().trim() == '') {
                $('#status').addClass('has-error');
                error++;
            } else {
                $('#status').removeClass('has-error');
            }

            if (error == 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: $('#add_addoncategory_form').serialize(),
                    success: function (data) {
                        
                        var result = JSON.parse(data);
                        if (result.insert_id > 0) {
                            location.reload(); 
                        }
                    }
                });
            }
        });
//                    }
        $('body').on('click','.EditAddonCategory',function (){
            var id = $(this).data('id');
            if (id != "") {
            $(".loading").show();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "rpress_get_addoncategory", id: id},
                success: function (data) {

                    var result = JSON.parse(data);
                    if (result.status == 1) {
                        $("#add_addoncategory_form #id").val(result.category.subcat_id);
                        $("#add_addoncategory_form #category_name").val(result.category.subcategory_name);
                        $("#add_addoncategory_form #description").val(result.category.subcategory_description);
                        $("#add_addoncategory_form #status").val(result.category.status);
                        $('#AddAddonCategoryModal').modal('show');

                    }
                    $(".loading").hide();
                }
            });
        }
        });
        $('#AddAddonCategoryModal').on('hidden.bs.modal', function () {
            $('#add_addoncategory_form #id').val('');
            $('#add_addoncategory_form')[0].reset();
            $('#category_name').removeClass('has-error');
            $('#status').removeClass('has-error');
        });

    });
    function category_record_delete(id) {
    if (id != "") {
            if (confirm("Are you sure?")) {
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {"action": "rpress_delete_addoncategory_record", id: id},
                    success: function (data) {
                        var result = JSON.parse(data)
                        if (result.status == 1) {
                            location.reload(); 
                        }
                        $(".loading").hide();
                    }
                });
            }
            return false;
        }
        return false;
    }
    
</script>
