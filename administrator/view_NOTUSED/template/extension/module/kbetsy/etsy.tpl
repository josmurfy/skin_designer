<?php echo $header; ?><style type="text/css">
                .custom-tabs .tile .tile-body i{
                    font-size: 64px !important;
                        padding: 10px;
                }
                .custom-tabs .tile-body{
                    padding: 15px 56px !important;
                }
                .custom-tabs .tile-heading{
                    text-align: center !important;
                }
            </style>
            <?php echo $column_left; ?>

        <div id="content">
            <div class="page-header">
            <div class="container-fluid">
              <h1><?php echo $heading_title; ?></h1>
              <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
              </ul>
            </div>
          </div>
            <div class="container-fluid">
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                </div>
            </div>
            <div class="panel-body custom-tabs">
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $general_settings; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_gs; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-cog"></i>
                        </div>
                        </div>
                    </a>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $profile_management; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_pm; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-user"></i>
                        </div>
                        </div>
                    </a>
              </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $shipping_templates; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_st; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-plane"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $shipping_template_entries; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_ste; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-plane"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $product_listing; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_pl; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-tasks"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $order_settings; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_os; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $order_listing; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_ol; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-arrow-down"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $synchronization; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_sy; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-exchange"></i>
                        </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="<?php echo $audit_log; ?>"><div class="tile">
                        <div class="tile-heading"><?php echo $text_al; ?></div>
                        <div class="tile-body">
                            <i class="fa fa-book"></i>
                        </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php if ($success) { ?>
            <div class="success"><?php echo $success; ?></div>
            <?php } ?>
            <?php if ($error) { ?>
            <div class="warning"><?php echo $error; ?></div>
            <?php } ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-error" style="display:block;">
                <button class="close" data-dismiss="alert" type="button">×</button>
                <strong>Warning!</strong>
                <?php echo $error_warning; ?>
            </div>
            <?php } ?>
            <div class="successSave"></div>

            <div class="box"> 
                <!-- 100% -->

                <!-- 960px -->
                
                        <!-- layout-->
                    </form>
                </div>
                <!-- content tabs--> 
            </div>
            <script type="text/javascript">
                function confirm_box(){
                     var result = confirm("Are you sure you want to delete?");
                    if(result == true) {
                        return true;
                    }else{
                        return false;
                    }
                }
                function saveAndStay(){
                    $.ajax( {
                        type: "POST",
                        url: $('#form').attr( 'action' ) + '&save=stay',
                        data: $('#form').serialize(),
                        dataType: 'json',
                        beforeSend: function() {
                            $('#form').fadeTo('slow', 0.5);
                        },
                        complete: function() {
                            $('#form').fadeTo('slow', 1);
                        },
                        success: function( response ) {
                        xmlDoc  = $.parseXML( response );
                        $xml = $( xmlDoc );
                        $type = $xml.find( "Type" );
                            console.log( response );
                            if($type.text() == 'Sender'){
                               $.gritter.add({
                                        title: 'Notification',
                                        text: response,
                                        class_name:'gritter-warning',
                                        sticky: false,
                                        time: '3000'
                                });
                            }else{
                                 $('.gritter-add-primary').trigger('click');
                            }
                        }
                    } );
                }
                $('#version_check').click(function(){
                    $.ajax( {
                        type: "POST",
                        url: 'index.php?route=extension/module/etsy/version_check&token=<?php echo $token; ?>',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#form').fadeTo('slow', 0.5);
                        },
                        complete: function() {
                            $('#form').fadeTo('slow', 1);
                        },
                        success: function( json ) {
                            console.log( json );
                            if(json['error']){
                                $('#version_result').html('<div class="warning">' + json['error'] + '</div>')
                            }
                            if(json['attention']){
                                $html = '';
                                if(json['update']){
                                    $.each(json['update'] , function(k, v) {
                                        $html += '<div>Version: ' +k+ '</div><div>'+ v +'</div>';
                                    });
                                }
                                $('#version_result').html('<div class="attention">' + json['attention'] + $html + '</div>')
                            }
                            if(json['success']){
                                $('#version_result').html('<div class="success">' + json['success'] + '</div>')
                            }
                        }
                    })
                })

                 $('.active a').click(function(){
                    $('.active .activeClass').removeClass('activeClass');
                    $(this).addClass('activeClass');
                });
                $(document).ajaxStart(function(){
                    $(".wait").show();
                })
                $(document).ajaxStop(function(){
                    $(".wait").hide();
                });
                //-->
                function changeEtsyStore(selectedCentral){
                    document.location.href = 'index.php?route=module/etsy&selectedCentral=' + selectedCentral + '&token=<?php echo $token; ?>';
                }

                </script>

            <div class="wait"><span></span></div>  
        <!-- // Content END -->
    <div class="clearfix"></div>
    <!-- // Sidebar menu & content wrapper END -->




</div>
<!--        <script src="view/javascript/etsy/theme/plugins/system/jquery-ui/js/jquery-ui-1.9.2.custom.min.js"></script>-->

<script src="view/javascript/etsy/theme/demo/common.js?1386063042"></script>

<!-- Bootstrap -->
<script src="view/javascript/etsy/bootstrap/bootstrap.min.js"></script>

<!-- Bootstrap Extended -->
<script src="view/javascript/etsy/bootstrap/extend/bootstrap-select/bootstrap-select.js"></script>
<script src="view/javascript/etsy/bootstrap/extend/bootstrap-switch/static/js/bootstrap-switch.js"></script>

<!-- Gritter Notifications Plugin -->
<script src="view/javascript/etsy/theme/plugins/notifications/Gritter/js/jquery.gritter.min.js"></script>

<!-- Notyfy Notifications Plugin -->
<script src="view/javascript/etsy/theme/plugins/notifications/notyfy/jquery.notyfy.js"></script>
<script src="view/javascript/etsy/theme/demo/notifications.js"></script>

<!-- Cookie Plugin -->
<script src="view/javascript/etsy/theme/plugins/system/jquery.cookie.js"></script>


<!-- Colors -->
<script>
    var primaryColor = '#50995E',
    dangerColor = '#bd362f',
    successColor = '#609450',
    warningColor = '#ab7a4b',
    inverseColor = '#45484d';
</script>

<!-- Themer -->
<script>
    var themerPrimaryColor = primaryColor;
</script>

<script src="view/javascript/etsy/theme/demo/themer.js"></script>
<!--<script src="view/javascript/etsy/theme/plugins/system/jquery.min.js"></script>-->
<script src="view/javascript/etsy/theme/plugins/color/jquery-miniColors/jquery.miniColors.js"></script>
<!-- Dashboard Demo Script -->
<!--<script src="view/javascript/etsy/theme/demo/index.js?1386063042"></script>-->

<?php echo $footer; ?>
<!-- // Main Container Fluid END -->
<!-- // for themer -->
<div id="themer" class="collapse">
    <div class="wrapper">
        <span class="close2">&times; close</span>
        <h4>Themer <span>color options</span></h4>
        <ul>
            <li>Theme: <select id="themer-theme" class="pull-right"></select><div class="clearfix"></div></li>
            <li>Primary Color: <input type="text" data-type="minicolors" data-default="#ffffff" data-slider="hue" data-textfield="false" data-position="left" id="themer-primary-cp" /><div class="clearfix"></div></li>
            <li>
                <span class="link" id="themer-custom-reset">reset theme</span>
                <span class="pull-right"><label>advanced <input type="checkbox" value="1" id="themer-advanced-toggle" /></label></span>
            </li>
        </ul>
        <div id="themer-getcode" class="hide">
            <hr class="separator" />
            <button class="btn btn-primary btn-small pull-right btn-icon glyphicons download" id="themer-getcode-less"><i></i>Get LESS</button>
            <button class="btn btn-inverse btn-small pull-right btn-icon glyphicons download" id="themer-getcode-css"><i></i>Get CSS</button>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
 <style>
                /* tr:nth-child(even) */
                tr.even { background-color: #EDEDED; }
                /* tr:nth-child(odd) */
                tr.odd { background-color: white; }
            </style>
            <script>
                $(function(){        
                    $('.alternate').each(function() {
                        $('tr:odd',  this).addClass('odd').removeClass('even');
                        $('tr:even', this).addClass('even').removeClass('odd');
                    });
                    $(".success").delay(2000).fadeOut(3000);
                });
                 function filter() {
                     url = 'index.php?route=extension/module/etsy/itemList&token=<?php echo $token; ?>';
                    location = url;    
                }
                function filter_update() {
                     url = 'index.php?route=extension/module/etsy/itemupdateList&token=<?php echo $token; ?>';
                    location = url;    
                }
                function filter_feed() {
                     url = 'index.php?route=extension/module/etsy/feedList&token=<?php echo $token; ?>';
                    location = url;    
                }
            </script>
            