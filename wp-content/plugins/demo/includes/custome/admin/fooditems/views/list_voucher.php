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

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">  

<?php
$voucher_list = get_all_voucher_custome();

?>
<div class="loading">
    <img  class="loader" src="<?php echo $plugin_dir_path.'../../../../../assets/images/loading.gif'; ?>" />
</div>

<div class="flex_container" style="padding-top:20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>Voucher List</h3>
        </div>
        <div class="col-sm-6">
            <input type="submit" value="Add offer" data-toggle="modal" data-target="#AddVoucherModal" style="margin-top: 15px;float:right;" class="btn btn-info btn-lg" />
        </div>

        <hr style="background-color:#000000; height:2px;">
        <div style="padding-bottom:10px;">
            <div style="clear:both;"></div>
        </div>
        <div class="">
            <table id='service-table' class="table table-bordered">
                <thead>
                    <tr>
                        <th class="all">voucher name</th>
                        <th class="all">voucher type</th>
                        <th class="all">amount</th>
                        <th class="all">expiration</th>
                        <th class="all">Status</th>
                        <th class="all">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($voucher_list as $list){ ?>
                    <tr>
                        <td><?php echo $list->voucher_name; ?></td>
                        <td><?php echo $list->voucher_type; ?></td>
                        <td><?php echo $list->amount; ?></td>
                        <td><?php echo $list->expiration; ?></td>
                        <td><?php echo $list->status; ?></td>
                        <td>
                            <div>
                                <input type="submit" value="Edit" data-id="<?php echo $list->voucher_id; ?>" class="btn btn-success EditVoucher" > &nbsp;
                                <a class="btn btn-danger" onclick="voucher_record_delete(<?php echo $list->voucher_id; ?>)" data-id ="<?php echo $list->voucher_id; ?>" >delete</a>
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
<div class="modal fade" id="AddVoucherModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form id="add_voucher_form" name="add_voucher_form" action="" method="post" class="form-horizontal" onclick="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Voucher</h4>
                </div>
   
                    <input type="hidden" name="id" id="id" value=""/>
                    <input type="hidden" value="rpress_insert_voucher" name="action" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-10 col-sm-offset-1">
                                <div class="form-group">
                                <label>Voucher name</label>
                                <input type="text" name="voucher_name" class="form-control" id="voucher_name" value="">
                            </div>
                                <div class="form-group">
                                <label>Type</label>
                                <select class="form-control" name="voucher_type" id="voucher_type" >
                                    <option value="fixed amount">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                                
                            </div>
                                <div class="form-group">
                                <label>Discount</label>
                                <input type="text" name="amount" class="form-control" id="amount" value="">
                            </div>
                                <div class="form-group">
                                <label>Expiration</label>
                                <input type="text" name="expiration" class="form-control" id="expiration" >
                            </div>
                                <div class="form-group">
                                <label>Used only once</label>
                                <input type="checkbox" name="used_once" class="" id="used_once" value="2" style="border-radius:0px;margin:0px;">
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
                        <input type="submit" id="btnAddVoucher" name="btnAddVoucher" class="btn btn-default" value="Save" />
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

        $( "input[type=checkbox]" ).on( "click", function(e) {
            e.stopPropagation();
        });

        //$("#used_once")

        $("#expiration").datepicker({      
            dateFormat: 'dd-mm-yy',  
            minDate: 0
        });
        
        $('#btnAddVoucher').on('click', function () {

            var error = 0;
            var amount = $('#amount').val().trim();
            
            if ($('#voucher_name').val().trim() == '') {
                $('#voucher_name').addClass('has-error');
                error++;
            } else {
                $('#voucher_name').removeClass('has-error');
            }
            if ($('#voucher_type').val().trim() == '') {
                $('#voucher_type').addClass('has-error');
                error++;
            } else {
                $('#voucher_type').removeClass('has-error');
            }
            if ( ( $('#amount').val().trim() == '' ||  isNaN(amount) ) || ( $('#voucher_type').val().trim() == 'percentage'  && $('#amount').val().trim() > 100  )  ) {
                $('#amount').addClass('has-error'); 
                error++;
            } else {
                $('#amount').removeClass('has-error');
            }
            if ($('#expiration').val().trim() == '') {
                $('#expiration').addClass('has-error');
                error++;
            } else {
                $('#expiration').removeClass('has-error');
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
                    data: $('#add_voucher_form').serialize(),
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
        $('body').on('click','.EditVoucher',function (){
            var id = $(this).data('id');
            if (id != "") {
            $(".loading").show();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "rpress_get_voucher_details", id: id},
                success: function (data) {

                    var result = JSON.parse(data);
                    if (result.status == 1) {
                        $("#add_voucher_form #id").val(result.voucher.voucher_id);
                        $("#add_voucher_form #voucher_name").val(result.voucher.voucher_name);
                        $("#add_voucher_form #voucher_type").val(result.voucher.voucher_type);
                        $("#add_voucher_form #amount").val(result.voucher.amount);
                        $("#add_voucher_form #expiration").val(result.voucher.expiration);
                        $("#add_voucher_form #status").val(result.voucher.status);
                        $('#AddVoucherModal').modal('show');

                    }
                    $(".loading").hide();
                }
            });
        }
        });
        $('#AddVoucherModal').on('hidden.bs.modal', function () {
            $('#add_voucher_form #id').val('');
            $('#add_voucher_form')[0].reset();
            
        });

    });
    function voucher_record_delete(id) {
    if (id != "") {
            if (confirm("Are you sure?")) {
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {"action": "rpress_delete_voucher_record", id: id},
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
