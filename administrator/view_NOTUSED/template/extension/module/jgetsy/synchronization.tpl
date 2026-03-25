<?php echo $header; ?><?php echo $column_left; ?>

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
        <div class="message-container"></div>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-exchange"></i><?php echo $heading_title_synchronization; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-general">             
                        <div class="panel-body custom-tabs">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_product_local_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?>&local=1" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $local_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_product_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?>" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $product_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_update_product_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?>&update=1" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $product_update_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_status_sync_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?>&status=1" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $product_status_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_order_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order&secure_key='.$secure_key; ?>&status=1" target="_blank" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $order_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><?php echo $text_status_syncronization; ?></div>
                                    <div class="panel-body">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order/syncOrderStatus&secure_key='.$secure_key ?>" class="btn btn-primary" ><?php echo $text_sync_now; ?></a>
                                                <div style="text-align: left; margin-top: 20px">
                                                    <?php echo $order_status_sync_cron_hint; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- content tabs--> 
                <div class="well">
                    <label><?php echo $text_cron_config; ?></label><br>
                    <p><?php echo $text_cron_config_help; ?></p>

                    <b><?php echo $text_cron_via_cp; ?></b><br>
                    <ul>
                        <li style="margin-top: 5px"><b><?php echo $text_product_syncronization; ?> <?php echo $cron_frequency; ?>:</b> <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?></li>
                        <li style="margin-top: 5px"><b><?php echo $text_update_product_syncronization; ?> <?php echo $cron_two_frequency; ?>:</b> <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key.'&update=1'; ?></li>
                        <li style="margin-top: 5px"><b><?php echo $text_status_sync_syncronization; ?> <?php echo $cron_day_frequency; ?>:</b> <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key.'&status=1'; ?></li>
                        <li style="margin-top: 5px"><b><?php echo $text_order_syncronization; ?> <?php echo $cron_frequency; ?>:</b> <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order&secure_key='.$secure_key; ?></li>
                        <li style="margin-top: 5px"><b><?php echo $text_status_syncronization; ?> <?php echo $cron_frequency; ?>:</b> <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order/syncOrderStatus&secure_key='.$secure_key; ?></li>
                    </ul>
                    <b><?php echo $text_cron_via_ssh; ?></b><br>
                    <ul>
                        <li style="margin-top: 5px">0 * * * * wget -O /dev/null <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key; ?></li>
                        <li style="margin-top: 5px">15 */2 * * * wget -O /dev/null <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key.'&update=1'; ?></li>
                        <li style="margin-top: 5px">0 0 * * * wget -O /dev/null <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/product&secure_key='.$secure_key.'&status=1'; ?></li>
                        <li style="margin-top: 5px">0 * * * * wget -O /dev/null <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order&secure_key='.$secure_key; ?></li>
                        <li style="margin-top: 5px">5 * * * * wget -O /dev/null <?php echo HTTPS_CATALOG . 'index.php?route=jgetsy/order/syncOrderStatus&secure_key='.$secure_key; ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <style type="text/css">
            .custom-tabs .tile .tile-body i{
                font-size: 150px !important;
            }
            .custom-tabs .tile-body{
                padding: 15px 56px !important;
            }
            .custom-tabs .tile-heading{
                text-align: center !important;
            }
            .card-body {
                min-height: 175px;
            }
        </style>
    </div>
</div>
<?php echo $footer; ?>
