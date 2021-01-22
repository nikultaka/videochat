<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- <script type="text/javascript" src="<?php echo plugins_url('restropress/assets/js/jquery.min.js'); ?>"></script> -->

<?php


$cat_list = get_custome_cat();
$sizes = get_sizes_custome();
$addon_details = get_addon_subcategory_item_details_by_merchant();
wp_enqueue_media();//enqueue these default wordpress file
$image_url = '';
$screen = '';
if($product_details['photo'] != ''){
    $image_url = 'https://food.mammothecommerce.com/upload/'.$product_details['photo'];
    $screen = '<img scr="'.$image_url.'" id="test_image" style="width: 350px;"> ';
    $screen_test = '<img src="https://www.w3schools.com/tags/img_girl.jpg" alt="Girl in a jacket" width="500" height="600">';
}
?>

<div class="row" style="width: 100%">
    <form id="add_fooditem_form" name="add_fooditem_form">
        <input type="hidden" value="rpress_insert_fooditem" name="action">
        <input type="hidden" value="<?php echo isset($product_details['item_id']) ? $product_details['item_id'] : '' ?>" name="item_id">
        <div class="col-md-8">
            <div class="form-group">
                <label>Food Item Name</label>
                <input type="text" name="item_name" id="item_name" class="form-control" value="<?php echo isset($product_details['item_name']) ? $product_details['item_name'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description" class="form-control" rows="12"><?php echo isset($product_details['item_description']) ? $product_details['item_description'] : '' ?></textarea>
            </div>
            <h4>Addon</h4>
            <?php if(count($addon_details) > 0){
                    foreach ($addon_details as $addon){ 
                        $addon_item = $product_details['addon_item'];
                       $multi_option = $product_details['multi_option'];
                       $cat_id = $addon['cat_id'];
                       
                        ?>
                    <div class="row bg-info" style="padding: 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-4"><b><?php echo $addon['category_name']; ?></b></label>
                                <select class="col-md-4 form-control" id="multi_option_<?php echo $addon['cat_id']; ?>" name="multi_option[<?php echo $addon['cat_id']; ?>][]">
                                    <option value="one"  <?php echo reset($multi_option->$cat_id) == 'one' ? 'selected="selected"' : '' ?>>Can Select Only One</option>
                                    <option value="multiple" <?php echo reset($multi_option->$cat_id) == 'multiple' ? 'selected="selected"' : ''; ?> >Can Select Multiple</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 10px;">
                        <?php foreach ($addon['category_item'] as $category_item){ ?>
                            <div class="col-md-12">
                                <input <?php echo in_array($category_item['sub_item_id'], $addon_item->$cat_id) ? "checked" : ""; ?> value="<?php echo $category_item['sub_item_id'] ?>" id="sub_item_id_<?php echo $addon['cat_id']; ?>" class="form-control" type="checkbox" name="sub_item_id[<?php echo $addon['cat_id']; ?>][]">	 
                                <?php echo $category_item['sub_item_name'] ?> (<?php echo $category_item['price'] ?>) 
                            </div>
                        
                            <?php
                        } ?>
                        
                    </div>
                        <?php
                    }
            } ?>
            
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Food Category</label>
                <?php if (!empty($cat_list)) { ?>
                    <ul class="uk-list uk-list-striped">
                        <?php foreach ($cat_list as $cat) { ?>
                            <li>
                                <input value="<?php echo $cat->cat_id; ?>" class="form-control" <?php echo isset($product_details['category']) && in_array($cat->cat_id,$product_details['category']) ? 'checked' : '' ?>  type="checkbox" name="category[]" id="category">	 
                                <?php echo $cat->category_name; ?>	  
                            </li>

                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>


            </div>
            <div class="form-group">
                <label class="uk-form-label">Status</label>
                <select class="form-control" data-validation="required" name="status" id="status" >
                    <option value="publish" <?php echo isset($product_details['status']) && $product_details['status'] == 'publish' ? 'selected="selected"' : '' ?> >Publish</option>
                    <option value="pending" <?php echo isset($product_details['status']) && $product_details['status'] == 'pending' ? 'selected="selected"' : '' ?> >Pending for review</option>
                    <option value="draft" <?php echo isset($product_details['status']) && $product_details['status'] == 'draft' ? 'selected="selected"' : '' ?> >Draft</option>
                </select>	
            </div>
<!--            <div class="form-group"> 
                <label class="uk-form-label">Featured Image</label>
                <a href="javascript:;" id="sau_merchant_upload_file" class="button uk-button" data-progress="sau_merchant_progress" data-preview="image_preview" data-field="photo" style="cursor: pointer;">
                    Browse  </a>
            </div>--> 
            <div class="form-group">
                <label for="image_url">Picture:</label>
                
                <img src="<?php echo $image_url; ?>" id="image_url2" style="width: 350px;"> 
                <input type="hidden" name="image_url2" id="image_url_2" class="regular-text" value="">
                <input type="button" name="upload-btn" id="upload-btn-2" class="button-secondary" value="Upload Image">
            </div>
            <?php if(isset($product_details['price'])){
                    foreach ($product_details['price'] as $price_key=>$price_value){ ?>
                        <div class="inc">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="uk-form-label">Size</label>
                                    <select class="uk-form-width-medium form-control" name="size[]" id="size">

                                        <option value="0"></option>
                                        <?php foreach ($sizes as $size) { ?>
                                            <option value="<?php echo $size->size_id ?>"   <?php echo $price_key == $size->size_id ? 'selected="selected"' : ''; ?> ><?php echo $size->size_name ?></option>
                                            <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-field">Price</label>
                                    <input class="uk-form-width-medium numeric_only form-control" type="text" value="<?php echo $price_value; ?>" name="price[]" id="price">
                                </div>
                            </div>
                        </div>
                        </div>
                        <?php
                    }
            }else{ ?>
            <div class="inc">
                <div id="main_data">
                <div class="main_class">
                <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="uk-form-label">Size</label>
                        <select class="uk-form-width-medium form-control" name="size[]" id="size">

                            <option value="0"></option>
                            <?php foreach ($sizes as $size) { ?>
                                <option value="<?php echo $size->size_id ?>"><?php echo $size->size_name ?></option>
                                <?php
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-field">Price</label>
                        <input class="uk-form-width-medium numeric_only form-control" type="text" value="" name="price[]" id="price">
                    </div>
                </div>
                    <div class="col-md-4 append_button">
                        <a href="javascript:void(0);" id="append" >Add</a>
                    </div>
            </div>
                </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </form>
    <div class="col-md-12 text-center">
        <input type="submit" class="btn btn-primary btn-lg btn-addfooditem" value="save">
    </div>
</div>
<script>
    jQuery(document).ready(function () {

        jQuery('.btn-addfooditem').on('click',function(e) {
            e.preventDefault();
            var countError = 0;  
            var item_name = jQuery("#item_name").val();
            var description = jQuery("#description").val();
            var price = jQuery("#price").val();

            if(price !='' && !isNaN(price) && price<=0) {
                alert("Please enter valid price");
                countError++;
            }

            if(item_name == "") {
                alert("Please enter item name");
                countError++;
            }

            if(description == "") {
                alert("Please enter description");
                countError++;
            }

            if(countError != 0) {
                return false;
            } else {
                return true;
            }
        });

        jQuery('body').on('click',"#append", function(e) {

          
          e.preventDefault();

          
 
         // $( ".main_class" ).clone().appendTo( ".inc" );
         jQuery("div.main_class:first").clone().insertAfter("div.main_class:last");
         jQuery('.append_button').html('<a href="javascript:void(0);" class="remove_this" >Delete</a>')
         jQuery('div.append_button:first').html('<a href="javascript:void(0);" id="append" >Add</a>')
        });
        jQuery(document).on('click', '.remove_this', function() {
            jQuery(this).parent().parent().remove();
            return false;
        });
        jQuery('#upload-btn-2').click(function(e) {
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
                    jQuery('#image_url_2').val(image_url);
                    jQuery('#image_url2').attr('src',image_url);

                });
            });
    });
</script>