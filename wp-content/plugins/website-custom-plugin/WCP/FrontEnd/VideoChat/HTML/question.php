<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>

<style>
.ck-editor__editable_inline {
    min-height: 400px;
}
#myTable_wrapper {
    margin-right: 20px;  	
}
</style>

    <div class="row">
    	<div class="col-md-6 pull-left">
    	    <h1>Question & Answers</h1>    
    	</div>    
    	<div class="col-md-6" style="text-align:right;">
	    <button class="btn btn-primary" onclick="add_question(<?php echo $value['id']; ?>);" style="margin-right: 20px;">ADD</button>
    	</div>
    </div>
    <table id="myTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th width="80%" align="center">Question</th>
                <th width="20%" align="center">Action</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($question_list  as $key => $value) { 
	          $question =  str_replace("\\", "", $value['question']);
		  $question = str_replace("/", "",$question);
		  
		  $answer =  str_replace("\\", "", $value['answer']);
		  $answer = str_replace("/", "",$answer);
            ?>
                <tr>
                    <td><?php echo trim($question); ?></td>
                    <td><button class="btn btn-primary" onclick="edit_question(<?php echo $value['id']; ?>);">Edit</button>
                    <button class="btn btn-primary" onclick="delete_question(<?php echo $value['id']; ?>);">Delete</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>    


<?php 
require_once('add_question.php');
?>

<script type="text/javascript">
    $(document).ready(function() {
        jQuery('#myTable').DataTable();
    });
    function delete_question(id) {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {"action": "WCP_BackEnd_Question_Controller::delete_question",id:id},
            success: function (data) {
                var result =  JSON.parse(data);
                if (result.status == 1) {
                    document.location.reload();
                }
            }
        });
    }
    
</script>
