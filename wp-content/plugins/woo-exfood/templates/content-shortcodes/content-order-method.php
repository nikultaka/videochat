<?php 
$method_ship = exwoofood_get_option('exwoofood_enable_method','exwoofood_shpping_options');
$dine_in = exwoofood_get_option('exwoofood_enable_dinein','exwoofood_shpping_options');
$limit = exwoofood_get_option('exwoofood_autocomplete_limit','exwoofood_shpping_options');
$limit = str_replace(' ', '',$limit)!='' ? json_encode(explode(",",$limit)) : '';
if($method_ship=='' && $dine_in!='yes'){ return;}
$class = '';
$user_address = WC()->session->get( '_user_deli_adress' );
$user_odmethod = WC()->session->get( '_user_order_method' );
//if($user_address == ''){
    //$class = 'ex-popup-active';
//}
global $location;
$loc_sl = exwoofood_loc_field_html($location);
$api = exwoofood_get_option('exwoofood_gg_api','exwoofood_shpping_options');
$cls_method = exwoofood_get_option('exwoofood_cls_method','exwoofood_shpping_options');
?>
<div class="exwf-order-method">
    <script type="text/javascript">
        jQuery(document).ready(function() {
            <?php if($cls_method=='yes' && exwoofood_get_option('exwoofood_enable_loc') !='yes'){?>
                jQuery('body').on('click','.exwf-opcls-info .exwf-method-ct .ex_close',function(event) {
                    jQuery(this).closest('.exwf-opcls-info').remove();
                    sessionStorage.setItem("exwf_cls_method", '1');
                    if(jQuery('.ex-popup-location').length){
                        var $popup_loc = jQuery(".ex-popup-location");
                        $popup_loc.addClass("ex-popup-active");
                    }
                });
                jQuery('body').on('click', '.exwf-opcls-info', function (event) {
                    if (event.target.className == 'exwf-opcls-info ex-popup-active') {
                        jQuery('.exwf-opcls-info').remove();
                        sessionStorage.setItem("exwf_cls_method", '1');
                    }
                });
                var exwf_at_method = sessionStorage.getItem("exwf_cls_method");
                if(exwf_at_method !== '1'){      
                    jQuery('.exwf-opcls-info.exwf-odtype').addClass('ex-popup-active');
                }
                 if(!jQuery('.exwf-order-method .exwf-opcls-info.exwf-odtype.ex-popup-active').length && jQuery('.ex-popup-location').length){
                    var $popup_loc = jQuery(".ex-popup-location");
                    $popup_loc.addClass("ex-popup-active");
                }
            <?php }else{?>
                jQuery('.exwf-opcls-info.exwf-odtype').addClass('ex-popup-active');
            <?php }?>
            jQuery('body').on('click', '.exwf-button', function (event) {
                var $method = 'delivery';
                if(jQuery(this).closest(".exwf-method-ct").find('.exwf-order-take.at-method').length){
                    $method = 'takeaway';
                }else if(jQuery(this).closest(".exwf-method-ct").find('.exwf-order-dinein.at-method').length){
                    $method = 'dinein';
                }
                jQuery('.exwf-add-error').fadeOut();
                var $addr ='';
                var $cnt = 1;
                if($method != 'takeaway' && $method != 'dinein'){
                    $addr = jQuery(this).closest(".exwf-method-ct").find('#exwf-user-address').val();
                    if($addr==''){ 
                        jQuery('.exwf-del-address .exwf-add-error').fadeIn();
                        $cnt = 0;
                    }
                }
                var $loc = jQuery(this).closest(".exwf-method-ct").find('.exwf-del-log select').val();
                if(jQuery('.exwf-del-log select.ex-logreq').length && ($loc==null || $loc=='' )){ 
                    jQuery('.exwf-del-log .exwf-add-error').fadeIn();
                    $cnt = 0;
                }
                if($cnt == 0){ return;}
                jQuery('.exwf-method-ct').addClass('ex-loading');
                var ajax_url        = jQuery('.ex-fdlist input[name=ajax_url]').val();
                var param = {
                    action: 'exwf_check_distance',
                    address: $addr,
                    log: $loc,
                    method: $method,
                };
                jQuery.ajax({
                    type: "post",
                    url: ajax_url,
                    dataType: 'json',
                    data: (param),
                    success: function(data){
                        if(data != '0'){
                            jQuery('.exwf-method-ct').removeClass('ex-loading');
                            if(data.mes!=''){
                                jQuery('.exwf-del-address .exwf-add-error').html(data.mes).fadeIn();
                            }else{
                                jQuery( document.body ).trigger( 'wc_fragment_refresh' );
                                var url_cr = window.location.href;
                                if(jQuery('.exwf-del-log select.ex-logreq').length){ 
                                    if($loc!='' && $loc!=null){
                                        if (url_cr.indexOf("?") > -1){
                                            url_cr = url_cr+"&loc="+$loc;
                                        }else{
                                            url_cr = url_cr+"?loc="+$loc;
                                        }
                                    }
                                }else{
                                    //jQuery('.exwf-order-method').fadeOut();
                                }
                                url_cr = url_cr.replace("change-address=1","");
                                if(window.location.hash) {
                                    url_cr = url_cr.replace(location.hash , "" );
                                }
                                window.location = url_cr;
                                return false;
                            }
                        }else{jQuery('#'+id_crsc+' .loadmore-exfood').html('error');}
                    }
                });
            });
            jQuery('body').on('click', '.exwf-method-title > div', function (event) {
                jQuery('.exwf-method-title > div').removeClass('at-method');
                jQuery(this).addClass('at-method');
                if(jQuery(this).hasClass('exwf-order-take') || jQuery(this).hasClass('exwf-order-dinein')){
                    jQuery('.exwf-del-address').fadeOut(); 
                }else{
                   jQuery('.exwf-del-address').fadeIn('fast');
                }
            });
            <?php if($api!=''){?>
                jQuery('body').on('click', '#exwf_geolo', function (event) {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(exwf_showPosition);
                    } else {
                        alert("<?php esc_html_e ("Geolocation is not supported by this browser.", 'woocommerce-food'); ?>");
                    }
                });

                function geocodeLatLng(latitude,longitude) {
                    const latlng = {
                        lat: parseFloat(latitude),
                        lng: parseFloat(longitude),
                    };
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: latlng }, (results, status) => {
                        if (status === "OK") {
                            if (results[0]) {
                                var address = results[0].formatted_address;
                                document.getElementById("exwf-user-address").value = address;
                            } else {
                                window.alert("No results found");
                            }
                        } else {
                            window.alert("Geocoder failed due to: " + status);
                        }
                    });
                }
                
                function exwf_showPosition(position) {
                    var lat = position.coords.latitude;
                    var lang = position.coords.longitude;
                    geocodeLatLng(lat,lang);
                }
            <?php }?>    

        });
    </script>
    <div class="exwf-opcls-info exwf-odtype <?php esc_attr_e($class);?>">
        <input type="hidden" name="exwf_auto_limit" id="exwf_auto_limit" value="<?php echo esc_attr($limit);?>">
        <div class="exwf-method-ct exwf-opcls-content">
            <?php if($cls_method=='yes' && exwoofood_get_option('exwoofood_enable_loc') !='yes'){ ?>
                <span class="ex_close">×</span>
            <?php }?>
            <div class="exwf-method-title">
                <?php if($method_ship!='takeaway' && $method_ship!=''){?>
                    <div class="exwf-order-deli <?php if(($user_odmethod!='takeaway' && $user_odmethod!='dinein' ) || ($method_ship=='delivery' && $dine_in!='yes') ){?> at-method <?php }?>">
                        <?php esc_html_e('Delivery','woocommerce-food');?>
                    </div>
                    <?php 
                }
                if($method_ship!='delivery' && $method_ship!=''){
                    ?>
                    <div class="exwf-order-take <?php if($user_odmethod=='takeaway' || $method_ship=='takeaway' && $user_odmethod==''){?> at-method <?php }?>">
                        <?php esc_html_e('Takeaway','woocommerce-food');?>
                    </div>
                <?php }
                 if($dine_in=='yes'){?>
                    <div class="exwf-order-dinein <?php if($user_odmethod=='dinein' || $method_ship==''){?> at-method <?php }?>">
                        <?php esc_html_e('Dine-In','woocommerce-food');?>
                    </div>
                <?php } ?>
            </div>
            <div class="exwf-method-content">
                <?php if($loc_sl!=''){?>
                    <div class="exwf-del-field exwf-del-log">
                        <span><?php esc_html_e('Ordering area','woocommerce-food');?></span>
                        <div><?php echo $loc_sl; ?></div>
                        <p class="exwf-add-error"><?php esc_html_e('Please choose area you want to order','woocommerce-food');?></p>
                    </div>
                <?php }
                if($method_ship!='takeaway'){?>
                    <div class="exwf-del-field exwf-del-address" <?php if($user_odmethod=='takeaway' || $user_odmethod=='dinein'){?> style="display: none;" <?php } ?>>
                        <span><?php echo esc_html__( 'Please type your address ','woocommerce-food' );?></span>
                        <div class="">
                            <input type="text" name="exwf-user-address" id="exwf-user-address" placeholder="<?php echo esc_html__( 'Enter a location ','woocommerce-food' );?>" value="<?php echo $user_address!='' ? $user_address : ''; ?>">
                        </div>
                         <?php if($api!=''){?>
                        <span class="exwf-crlog"><a href="javascript:;" id="exwf_geolo"><i class="ion-navigate"></i><?php esc_html_e('Or use my current location','woocommerce-food');?></a></span>
                        <?php }?>
                        <p class="exwf-add-error"><?php esc_html_e('Please add your address','woocommerce-food');?></p>
                    </div>
                <?php }?>
            </div>
            <div class="exwf-method-bt">
                <span class="exwf-button"><?php esc_html_e('Start my order','woocommerce-food');?></span>
            </div>
        </div>
    </div>
</div>