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

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


<?php
$offers_list = get_offers_custome();

?>
<div class="loading">
    <img  class="loader" src="<?php echo $plugin_dir_path.'../../../../../assets/images/loading.gif'; ?>" />
</div>

<div class="flex_container" style="padding-top:20px;">
    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>Offers List</h3>
        </div>
        <div class="col-sm-6">
            <input type="submit" value="Add offer" data-toggle="modal" data-target="#AddOfferModal" style="margin-top: 15px;float:right;" class="btn btn-info btn-lg" />
        </div>

        <hr style="background-color:#000000; height:2px;">
        <div style="padding-bottom:10px;">
            <div style="clear:both;"></div>
        </div>
        <div class="">
            <table id='service-table' class="table table-bordered">
                <thead>
                    <tr>
                        <th class="all">Offer Percentage</th>
                        <th class="all">Orders Over</th>
                        <th class="all">Valid From</th>
                        <th class="all">Valid To</th>
                        <th class="all">Applicable</th>
                        <th class="all">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($offers_list as $list){ ?>
                    <tr>
                        <td><?php echo $list->offer_percentage; ?></td>
                        <td><?php echo $list->offer_price; ?></td>
                        <td><?php echo $list->valid_from; ?></td>
                        <td><?php echo $list->valid_to; ?></td>
                        <td><?php 
                        $applicable = json_decode($list->applicable_to);
                        echo implode(",", $applicable);
                        
                        ?></td>
                        <td>
                            <div>
                                <input type="submit" value="Edit" data-id="<?php echo $list->offers_id; ?>" class="btn btn-success EditOffer" > &nbsp;
                                <a class="btn btn-danger" onclick="offer_record_delete(<?php echo $list->offers_id; ?>)" data-id ="<?php echo $list->offers_id; ?>" >delete</a>
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
<div class="modal fade" id="AddOfferModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form id="add_offer_form" name="add_offer_form" action="" method="post" class="form-horizontal" onclick="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Offer</h4>

                </div>

                    <input type="hidden" name="id" id="id" value=""/>
                    <input type="hidden" value="rpress_insert_offer" name="action" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-10 col-sm-offset-1">
                                <div class="form-group">
                                <label>Offer Percentage</label>
                                <input type="text" name="offer_percentage" class="form-control" id="offer_percentage" value="">
                            </div>
                                <div class="form-group">
                                <label>Orders Over</label>
                                <input type="text" name="offer_price" class="form-control" id="offer_price" value="">
                            </div>
                                <div class="form-group">
                                <label>Valid From</label>
                                <input type="text" name="valid_from" class="form-control datepicker" id="valid_from" value="" />
                            </div>
                                <div class="form-group">
                                <label>Valid To</label>
                                <input type="text" name="valid_to" class="form-control datepicker" id="valid_to" />
                            </div>
                                <div class="form-group">
                                <label>Applicable</label>
                                <select class="form-control" name="applicable_to[]" id="applicable_to" multiple="multiple">
                                    <option value="delivery">Delivery</option>
                                    <option value="pickup">Pickup</option>
                                    <option value="dinein">Dinein</option>
                                </select>
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
                        <input type="submit" id="btnAddOffer" name="btnAddOffer" class="btn btn-default" value="Save" />
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

        $("#valid_from").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            onSelect: function(selected) {
                $("#valid_to").datepicker("option","minDate", selected)
            }
        });
        $("#valid_to").datepicker({      
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            onSelect: function(selected) {
                $("#valid_from").datepicker("option","maxDate", selected)
            }
        });    
        
        $('#btnAddOffer').on('click', function () {

            var error = 0;

            var valid_from = $('#valid_from').val().trim();
            var valid_to = $('#valid_to').val().trim(); 
            
            if ($('#offer_percentage').val().trim() == '' || $("#offer_percentage").val().trim() >100) {
                $('#offer_percentage').addClass('has-error');
                error++;
            } else {
                $('#offer_percentage').removeClass('has-error');
            }
            if ($('#offer_price').val().trim() == '') {
                $('#offer_price').addClass('has-error');
                error++;
            } else {
                $('#offer_price').removeClass('has-error');
            }
            if ($('#valid_from').val().trim() == '') {
                $('#valid_from').addClass('has-error');
                error++;
            } else {
                $('#valid_from').removeClass('has-error');
            }
            if ($('#valid_to').val().trim() == '') {
                $('#valid_to').addClass('has-error');
                error++;
            } else {
                $('#valid_to').removeClass('has-error');
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
                    data: $('#add_offer_form').serialize(),
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
        $('body').on('click','.EditOffer',function (){
            var id = $(this).data('id');
            if (id != "") {
            $(".loading").show();
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {"action": "rpress_get_offer_details", id: id},
                success: function (data) {

                    var result = JSON.parse(data);
                    if (result.status == 1) {
                        $("#add_offer_form #id").val(result.offer.offers_id);
                        $("#add_offer_form #offer_percentage").val(result.offer.offer_percentage);
                        $("#add_offer_form #offer_price").val(result.offer.offer_price);
                        $("#add_offer_form #valid_from").val(result.offer.valid_from);
                        $("#add_offer_form #valid_to").val(result.offer.valid_to);
                        $("#add_offer_form #status").val(result.offer.status);
                        $("#add_offer_form #applicable_to").val(result.offer.applicable_to);
                        $('#AddOfferModal').modal('show');

                    }
                    $(".loading").hide();
                }
            });
        }
        });
        $('#AddOfferModal').on('hidden.bs.modal', function () {
            $('#add_offer_form #id').val('');
            $('#add_offer_form')[0].reset();
            $('#offer_percentage').removeClass('has-error');
            
            $('#status').removeClass('has-error');
        });

    });
    function offer_record_delete(id) {
    if (id != "") {
            if (confirm("Are you sure?")) {
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {"action": "rpress_delete_offer_record", id: id},
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
