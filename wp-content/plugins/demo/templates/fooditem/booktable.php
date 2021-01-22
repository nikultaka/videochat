<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('restropress/assets/css/custom.css'); ?>" >
<script type="text/javascript" src="<?php echo plugins_url('restropress/assets/js/jquery.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('restropress/assets/css/bootstrap.min.css'); ?>" >
<script type="text/javascript" src="<?php echo plugins_url('restropress/assets/js/bootstrap.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo plugin_dir_url('') . '/restropress/assets/js/datatables.min.js'; ?>"></script>

<div class="loading">
    <img  class="loader" src="<?php echo plugin_dir_url('') . '/restropress/assets/images/loading.gif'; ?>" />
</div>


<div class="d-flex justify-content-center">
    <div class="row">
        
            <form id="book_table" name="book_table" onsubmit="return false;">
                <input type="hidden" name="action" value="RP_Shortcode_Booktable::repress_book_table" />
                <div class="col-md-6">
                    <h4><b>Booking Information</b></h4>
                    <div class="form-group">
                        <label for="numberofGuest">Number Of Guests</label>
                        <input type="number" class="form-control" id="NumberOfGuest" name="NumberOfGuest" placeholder="Number Of Guest">
                    </div>
                    <div class="form-group">
                        <label for="DateOfBooking">Date Of Booking</label>
                        <input type="date" class="form-control" id="DateOfBooking" name="DateOfBooking" placeholder="Date Of Booking">
                    </div>
                    <div class="form-group">
                        <label for="TimeOfBooking">Time</label>
                        <input type="time" class="form-control" id="TimeOfBooking" name="TimeOfBooking" placeholder="Time">
                    </div>
                </div>
                <div class="col-md-6">
                    <h4><b>Contact Information</b></h4>
                    <div class="form-group">
                        <label for="NameOfGuest">Name</label>
                        <input type="text" class="form-control" id="NameOfGuest" name="NameOfGuest" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="EmailOfGuest">Email</label>
                        <input type="email" class="form-control" id="EmailOfGuest" name="EmailOfGuest" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="MobileOfGuest">Mobile</label>
                        <input type="number" class="form-control" id="MobileOfGuest" name="MobileOfGuest" placeholder="Mobile">
                    </div>
                    
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="numberofguest">Your Instructions</label>
                        <textarea class="form-control" name="Instructions" placeholder="Your Instructions" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <p id="msg"></p>
                        <input type="submit" value="Submit" class="btn btn-info btnSubmitTable">
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
<script type="text/javascript">

    $(document).ready(function () {

        $('.btnSubmitTable').on('click', function () {

            var error = 0;
            if ($('#NumberOfGuest').val().trim() == '') {
                $('#NumberOfGuest').addClass('has-error');
                error++;
            } else {
                $('#NumberOfGuest').removeClass('has-error');
            }
            
            if ($('#DateOfBooking').val().trim() == '') {
                $('#DateOfBooking').addClass('has-error');
                error++;
            } else {
                $('#DateOfBooking').removeClass('has-error');
            }
             
            if ($('#TimeOfBooking').val().trim() == '') {
                $('#TimeOfBooking').addClass('has-error');
                error++;
            } else {
                $('#TimeOfBooking').removeClass('has-error');
            }
            
            if ($('#NameOfGuest').val().trim() == '') {
                $('#NameOfGuest').addClass('has-error');
                error++;
            } else {
                $('#NameOfGuest').removeClass('has-error');
            }
            
            if ($('#EmailOfGuest').val().trim() == '') {
                $('#EmailOfGuest').addClass('has-error');
                error++;
            } else {
                $('#EmailOfGuest').removeClass('has-error');
            }
            if ($('#MobileOfGuest').val().trim() == '') {
                $('#MobileOfGuest').addClass('has-error');
                error++;
            } else {
                $('#MobileOfGuest').removeClass('has-error');
            }
            
            
            if (error == 0) {
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: $('#book_table').serialize(),
                    success: function (data) {
                        var result = JSON.parse(data);
                        
                        if (result.insert_id > 0) {
                            $('#book_table')[0].reset();
                            $('#msg').html(result.message); 
                            $('#msg').css({"color":"green","font-size": "20px"}); 
                        }
                        $(".loading").hide();
                    }
                });
            }
        });
    });
</script>
