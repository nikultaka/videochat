
    <div class="modal fade" id="AddSericePriceModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Service</h4>
                </div>
                <form id="frmAddService" name="frmAddService" action="" class="form-horizontal">
                    <input type="hidden" name="id" id="id" value=""/>
                    <div class="modal-body">
                        <div class="row" style="padding-bottom: 10px">
                            <div class="col-sm-4">
                                Service
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="service_name" name="service_name" class="form-control" value="" />
                            </div>
                        </div>


                        <div class="row" style="padding-bottom: 10px">
                            <div class="col-sm-4">
                                Price
                            </div>
                            <div class="col-sm-8" id="div_service_level">
                                <input type="text" id="price" name="price" class="form-control" value="" >
                            </div>
                        </div>

                        <div class="row" style="padding-bottom: 10px">
                            <div class="col-sm-4">
                                Status
                            </div>
                            <div class="col-sm-8" id="div_service_level">
                                <select id="status" name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" id="saveservice">
                        <input type="submit" name="btnAddService" id="btnAddService" class="btn btn-default" value="Save" />
                        <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
<script>
                    
$(document).ready(function() {
    $('#frmAddService').bootstrapValidator({
            framework:'boostrap',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                service_name: {
                    validators: {
                        notEmpty: {
                            message: 'The service name is required and can\'t be empty'
                        }
                    }
                },
                price: {
                    validators: {
                        notEmpty: {
                            message: 'The price is required and can\'t be empty'
                        },
                    }
                }
            }
        }).on('success.form.bv', function(e) {
                    // Prevent form submission
                    e.preventDefault();

                    // Get the form instance
                    var $form = $(e.target);

                    // Get the BootstrapValidator instance
                    var bv = $form.data('bootstrapValidator');

                    // Use Ajax to submit form data
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: {"action": "WCP_BackEnd_Service_Controller::add_service_price", service_name: $('#service_name').val(), price: $('#price').val(),status:$('#status').val(),id:$('#frmAddService #id').val()},
                        success: function (data) {
                            var result =  JSON.parse(data);
                            if (result.status == 1) {
                                $("#AddSericePriceModal").modal('hide');
                                clearvalue();
                                reload_table();
                            }
                        }
                    });
    });
});

function updateserviceprice() {

    var id = $('#ServiceId').val();
    var service_id = $('#service_id').val();
    var service_level_id = $('#service_level_id').val();
    var price = $('#price').val();
    var warranty_price = $("#warranty_price").val();
    var zipcode = $('#zipcode').val();
    var status = $('#service_Status').val();
    var service_type = $("#service_type").val();

    var errCount = 0;
    if(service_id == "") {
        $("#service_id").parent().addClass('has-error');
        errCount++;
    } else {
        $("#service_id").parent().removeClass('has-error');
    } 
    if(service_level_id == "") {
        $("#service_level_id").parent().addClass('has-error');
        errCount++;
    } else {
        $("#service_level_id").parent().removeClass('has-error');
    }
    if(price == "") {
        $("#price").parent().addClass('has-error');
        errCount++;
    } else {
        $("#price").parent().removeClass('has-error');
    }
    if(warranty_price == "") {
        $("#warranty_price").parent().addClass('has-error');
        errCount++;
    } else {
        $("#warranty_price").parent().removeClass('has-error');
    }


    if(service_type == '2') {
        if(zipcode == "") {
            $("#zipcode").parent().addClass('has-error');
            errCount++;
        } else {
            $("#zipcode").parent().removeClass('has-error');
        }
    }

    if(errCount == '0') {
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_BackEnd_Service_Price_Controller::update_service_list", id: id, service_id: service_id, service_level_id: service_level_id, price: price,warranty_price:warranty_price,zipcode: zipcode, status: status},
            success: function (data) {
                if (data == 'ok') {
                    reload_table();
                    clearvalue();
                }
            }
        });
    }
}
function clearvalue() {
    var price = $('#price').val('');
    document.getElementById("frmAddService").reset();
}
</script>