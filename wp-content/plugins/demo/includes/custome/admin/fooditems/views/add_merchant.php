<?php 
$plugin_dir_path = plugin_dir_url( __FILE__ );
?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo $plugin_dir_path.'../../../../../assets/js/jquery.min.js'; ?>"></script>

<?php
$merchant_username = get_option( 'merchant_username');
?>

<div class="row" style="width: 100%">
    <form id="add_fooditem_form" name="add_merchant_form" method="post">
        <input type="hidden" value="rpress_save_merchant" name="action">
        <div class="col-md-8">
            <div class="form-group">
                <label>Merchant Username</label>
                <input type="text" name="merchant_username" id="merchant_username" class="form-control" value="<?php echo $merchant_username; ?>">
            </div>
        </div>
    </form>
    <div class="col-md-12 pull-left">
        <input type="button" class="btn btn-primary btn-lg btn-addmerchant" value="save" onclick="save_merchant();">
    </div>
</div>
<script>
    //$(document).ready(function () {
            function save_merchant() {
                $.ajax({
                        type : 'POST',
                        url : '<?php echo admin_url('admin-ajax.php'); ?>',
                        data : 'action=rpress_save_merchant&merchant_username='+$("#merchant_username").val(),
                        dataType : 'json',
                        success :function(msg) {
                            if(msg.status = "1") {
                                alert("Merchant username updated successfully");
                            }
                        }
                })
            }
    //});
</script>