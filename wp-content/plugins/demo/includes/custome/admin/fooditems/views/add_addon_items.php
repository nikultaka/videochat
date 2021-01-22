<?php 
$plugin_dir_path = plugin_dir_url( __FILE__ );
?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $plugin_dir_path.'../../../../../assets/css/custom.css' ?>" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<?php
$cat_list = get_all_addon_subcategory_item_details_by_merchant(true);
$cat = get_addon_subcategory_by_merchant();
wp_enqueue_media();//enqueue these default wordpress file
?>
<div class="loading">
    <img  class="loader" src="<?php echo $plugin_dir_path.'../../../../../assets/images/loading.gif'; ?>" />
</div>

<div class="flex_container" style="padding-top:20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>Addon List</h3>
        </div>
        <div class="col-sm-6">
            <input type="submit" value="Add Addon items" data-toggle="modal" data-target="#AddAddonitemModal" style="margin-top: 15px;float:right;" class="btn btn-info btn-lg" />
        </div>

        <hr style="background-color:#000000; height:2px;">
        <div style="padding-bottom:10px;">
            <div style="clear:both;"></div>
        </div>
        <div class="">
            <table id='service-table' class="table table-bordered">
                <thead>
                    <tr>
                        <th class="all">Name</th>
                        <th class="all">Price</th>
                        <th class="all">Status</th>
                        <th class="all">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($cat_list as $list){ ?>
                    <tr>
                        <td><?php echo $list['sub_item_name']; ?></td>
                        <td><?php echo $list['price']; ?></td>
                        <td><?php echo $list['status']; ?></td>
                        <td>
                            <div>
                                <input type="submit" value="Edit" data-id="<?php echo $list['sub_item_id']; ?>" class="btn btn-success EditAddonItems" > &nbsp;
                                <a class="btn btn-danger" onclick="items_record_delete(<?php echo $list['sub_item_id']; ?>)"  >delete</a>
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
<div class="modal fade" id="AddAddonitemModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form id="add_addonitem_form" name="add_addonitem_form" action="" method="post" class="form-horizontal" onclick="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Addon Category</h4>

                </div>

                    <input type="hidden" name="id" id="id" value=""/>
                    <input type="hidden" value="rpress_insert_addonitem" name="action" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-10 col-sm-offset-1">
                            <div class="form-group">
                                <label>AddOn Item</label>
                                <input type="text" name="item_name" class="form-control" id="item_name" value="">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" name="price" class="form-control" id="price" value="">
                            </div>
                                
                                    
                                    
                               <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="category[]" id="category" multiple="multiple">
                                    <?php foreach ($cat as $cats){ ?>
                                     <option value="<?php echo $cats->subcat_id; ?>"><?php echo $cats->subcategory_name;  ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>

                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="8"></textarea>
                            </div>
                                <div class="form-group">
                                    <label for="image_url">Picture:</label>

                                    <img src="" id="image_url2" style="width: 350px;"> 
                                    <input type="hidden" name="image_url2" id="image_url_2" class="regular-text" value="">
                                    <input type="button" name="upload-btn" id="upload-btn-2" class="button-secondary" value="Upload Image">
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
                        <input type="submit" id="btnAddaddonitem" name="btnAddaddonitem" class="btn btn-default" value="Save" />
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
        
         $('#upload-btn-2').click(function(e) {
                e.preventDefault();
                var image = wp.media({ 
                    title: 'Upload Image',
                    // mutiple: true if you want to upload multiple files at once
                    multiple: false
                }).open()
                .on('select', function(e){
                    // This will return the selected image from the Media Uploader, the result is an object
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    // Output to the console uploaded_image
                    console.log(uploaded_image);
                    var image_url = uploaded_image.toJSON().url;
                    // Let's assign the url value to the input field
                    $('#image_url_2').val(image_url);
                    $('#image_url2').attr('src',image_url);

                });
            });
        
        $('#btnAddaddonitem').on('click', function () {

            var error = 0;
            
            if ($('#item_name').val().trim() == '') {
                $('#item_name').addClass('has-error');
                error++;
            } else {
                $('#item_name').removeClass('has-error');
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
                    data: $('#add_addonitem_form').serialize(),
                    success: function (data) {
                        var result = JSON.parse(data);
                        if (result.insert_id > 0) {
                            location.reload(); 
                        }
                    }
                });
            }
        });
        $('body').on('click','.EditAddonItems',function (){
            var id = $(this).data('id');
            if (id != "") {
            $(".loading").show();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "rpress_get_addonitems", id: id},
                success: function (data) {

                    var result = JSON.parse(data);
                    if (result.status == 1) {
                        $("#add_addonitem_form #id").val(result.category.sub_item_id);
                        $("#add_addonitem_form #item_name").val(result.category.sub_item_name);
                        $("#add_addonitem_form #price").val(result.category.price);
                        var image_url = '';
                        if(result.category.photo != ''){
                            image_url = 'https://food.mammothecommerce.com/upload/'+result.category.photo;
                        }
                        $('#add_addonitem_form #image_url2').attr('src',image_url);
                        $("#add_addonitem_form #description").val(result.category.item_description);
                        $("#add_addonitem_form #category").val(result.category.category);
                        $("#add_addonitem_form #status").val(result.category.status);
                        $('#AddAddonitemModal').modal('show');

                    }
                    $(".loading").hide();
                }
            });
        }
        });
    
        $('#AddAddonitemModal').on('hidden.bs.modal', function () {
            $('#add_addonitem_form #id').val('');
            $('#add_addonitem_form')[0].reset();
            $('#add_addonitem_form #image_url2').attr('src','');
            $('#category_name').removeClass('has-error');
            $('#status').removeClass('has-error');
        });

    });
    function items_record_delete(id) {
    if (id != "") {
            if (confirm("Are you sure?")) {
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {"action": "rpress_delete_addonitems_record", id: id},
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
