<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_support; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tab_common; ?>

                <div>
                    <style>
                        .productOCList .productImage img {
                            width: 100%;
                        }
                        .productOCName {
                            text-align: center;
                            font-size: 18px;
                            padding-top: 10px;
                            font-weight: 500;
                            color: #000;
                            line-height: 24px;
                        }
                        .productContent .productContentText {
                            font-size: 13px;
                            text-align: center;
                        }
                        .productContent p {
                            text-align: center;
                        }
                        .btn.btn-view {
                            background: #06b7f0;
                            width: 70%;
                            color: #fff;
                            text-transform: uppercase;
                            margin-top: 10px;
                            margin-bottom: 10px;
                        }
                        .productOCList {
                            border: 1px solid #efefef;
                            margin-bottom: 20px;
                        }
                        .productContent {
                            padding: 0 12px;
                        }
                        h2.productSupportHeading {
                            text-align: center;
                            font-size: 20px;
                            margin-bottom: 15px;
                            color: #000;
                            font-weight: 600;
                            text-transform: uppercase;
                        }
                        .productSupportSubHeading {
                            //text-align: center;
                            margin-bottom: 30px;
                        }
                        @media(max-width:1200px){
                            .productOCName {
                                min-height: 60px;
                            }	

                        }
                        @media(max-width:767px){
                            .productOCName {
                                min-height: auto;
                            }	
                            .productOCList{
                                max-width:400px;
                                margin: 0 auto 20px;
                            }

                        }
                    </style>
                    <div class="productSupport">
                        <div class="container-fluid">
                            <p class="alert alert-info productSupportSubHeading"> 
                                <a href="https://www.knowband.com/blog/user-manual/opencart-etsy-marketplace-integration/" target="_blank"><?php echo $text_click_here; ?></a> <?php echo $text_user_manual."."; ?><?php echo " ".$text_support_ticket1 ?><a href="https://www.knowband.com/create-ticket" target="_blank"><?php echo $text_support_ticket2 ?></a> <?php echo $text_support_ticket3 ?>
                            </p>
                            <br>
                            <h2 class="productSupportHeading"><?php echo $text_support_other ?></h2>
                            <br>
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-6 customHeight">
                                    <div class="productOCList" style="height: 374px;">
                                        <div class="productImage">
                                            <img src="view/image/jgetsy/multi-vendor-marketplace.jpg">
                                        </div>
                                        <div class="productContent">
                                            <h4 class="productOCName"><?php echo $text_support_marketplace ?></h4>
                                            <p class="productContentText"> <?php echo $text_support_marketplace_descp ?> </p>
                                            <p><a target="_blank" href="https://www.knowband.com/opencart-multi-vendor-marketplace-plugin" class="btn btn-view"><?php echo $text_support_view_more ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 customHeight">
                                    <div class="productOCList" style="height: 374px;">
                                        <div class="productImage">
                                            <img src="view/image/jgetsy/Opencart--ebay.jpg">
                                        </div>
                                        <div class="productContent">
                                            <h4 class="productOCName"><?php echo $text_support_ebay ?></h4>
                                            <p class="productContentText"> <?php echo $text_support_ebay_descp ?> </p>
                                            <p><a target="_blank" href="https://www.knowband.com/opencart-ebay-integration" class="btn btn-view"><?php echo $text_support_view_more ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 customHeight">
                                    <div class="productOCList" style="height: 374px;">
                                        <div class="productImage">
                                            <img src="view/image/jgetsy/google-shopping.jpg">
                                        </div>
                                        <div class="productContent">
                                            <h4 class="productOCName"><?php echo $text_support_gs ?></h4>
                                            <p class="productContentText"> <?php echo $text_support_gs_descp ?> </p>
                                            <p><a target="_blank" href="https://www.knowband.com/opencart-google-shopping" class="btn btn-view"><?php echo $text_support_view_more ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 customHeight">
                                    <div class="productOCList" style="height: 374px;">
                                        <div class="productImage">
                                            <img src="view/image/jgetsy/Mobile-app-opencart-plugin.jpg">
                                        </div>
                                        <div class="productContent">
                                            <h4 class="productOCName"><?php echo $text_support_mab ?></h4>
                                            <p class="productContentText"> <?php echo $text_support_mab_descp ?> </p>
                                            <p><a target="_blank" href="https://www.knowband.com/opencart-mobile-app-builder" class="btn btn-view"><?php echo $text_support_view_more ?></a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $text_support_ticket1 ?> <a href="https://www.knowband.com/create-ticket" target="_blank"><?php echo $text_support_ticket2 ?></a> <?php echo $text_support_ticket3 ?> 
                </div>

            </div>

        </div>
    </div>
</div>
<?php echo $footer; ?>
