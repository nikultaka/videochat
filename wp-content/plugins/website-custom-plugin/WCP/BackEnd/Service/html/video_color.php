<?php 
$user_one = '';
$user_two = '';
$user_three = '';
$user_four = '';
if(!empty($color_list)) {
    $user_one = $color_list[0]['user_one'];
    $user_two = $color_list[0]['user_two'];
    $user_three = $color_list[0]['user_three'];
    $user_four = $color_list[0]['user_four']; 
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'website-custom-plugin/WCP/assets/css/bootstrap.min.css' ); ?>" >
<!-- Latest compiled and minified JavaScript -->
<script type="text/javascript" src="<?php echo plugins_url( 'website-custom-plugin/WCP/assets/js/bootstrap.min.js' ); ?>"></script>

<div class="flex_container" style="padding-top:20px;">
        <h1>Videochat Color Setting</h1>
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="form-group">
                        <label>User 1</label>
                        <input type="text" class="form-control" id="user_one" placeholder="" value="<?php echo $user_one; ?>">
                    </div>
                    <div class="form-group">
                        <label>User 2</label>
                        <input type="text" class="form-control" id="user_two" placeholder="" value="<?php echo $user_two; ?>">
                    </div>
                    <div class="form-group">
                        <label>User 3</label>
                        <input type="text" class="form-control" id="user_three" placeholder="" value="<?php echo $user_three; ?>">
                    </div>
                    <div class="form-group">
                        <label>User 4</label>
                        <input type="text" class="form-control" id="user_four" placeholder="" value="<?php echo $user_four; ?>">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="add_user_color();">Submit</button>
                </form>
            </div>    
        </div>   
</div>


<script>
    function add_user_color() {
        var user_one = $("#user_one").val();
        var user_two = $("#user_two").val();
        var user_three = $("#user_three").val();
        var user_four = $("#user_four").val();
        var count_error = 0;
        if(user_one == '') {
            $("#user_one").parent('div').addClass('has-error');
            count_error++;
        }
        if(user_two == '') {
            $("#user_two").parent('div').addClass('has-error');
            count_error++;
        }
        if(user_three == '') {
            $("#user_three").parent('div').addClass('has-error');
            count_error++;
        }
        if(user_four == '') {
            $("#user_four").parent('div').addClass('has-error');
            count_error++;
        }

        if(count_error == 0) {
            $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {"action": "WCP_BackEnd_VideoChat_Controller::add_color_setting","user_one":user_one,"user_two":user_two,"user_three":user_three,"user_four":user_four},
                    dataType : 'json',
                    success: function (data) {
                        if(data.status == '1') {
                            alert("Color setting saved successfully");
                        }      
                    }        
            });    
        }
                   
    }
</script>
