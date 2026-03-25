<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a target="_blank" href="<?php echo $sync_shop_section_url; ?>" data-toggle="tooltip" title="<?php echo $text_sync_shop_section; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                <h3 class="panel-title"><i class="fa fa-plane"></i><?php echo $text_shop_section; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <form action="" method="post" enctype="multipart/form-data" id="form-product">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-left">
                                        <?php if ($sort == 'etsy_shop_section_id') { ?>
                                            <a href="<?php echo $sort_etsy_shop_section_id; ?>" class="<?php echo $order; ?>"><?php echo $column_etsy_shop_section_id; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_etsy_shop_section_id; ?>"><?php echo $column_etsy_shop_section_id; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($sort == 'title') { ?>
                                            <a href="<?php echo $sort_title; ?>" class="<?php echo $order; ?>"><?php echo $column_etsy_shop_section_title; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_title; ?>"><?php echo $column_etsy_shop_section_title; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-right"><?php echo $column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($shop_sections) { ?>
                                    <?php foreach ($shop_sections as $section) { ?>
                                        <tr>
                                            <td class="text-left"><?php echo $section['etsy_shop_section_id']; ?></td>
                                            <td class="text-left"><?php echo $section['title']; ?></td>
                                            <td class="text-right">
                                                <a href="<?php echo $section['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo $section['delete']; ?>" onclick="return window.confirm('<?php echo $text_confirm_delete_etsy; ?>');" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td class="text-left" colspan="3">
                                            <div class="alert alert-warning">
                                                <i class="fa fa-check-circle"></i> <?php echo $text_no_shop_section_error; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>