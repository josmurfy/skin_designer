<?php echo $header; ?><?php echo $column_left; ?>
    <div id="content">
        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <button type="submit" form="form-image_cache" data-toggle="tooltip"
                            title="<?php echo $button_save; ?>"
                            class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                       class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                </div>
                <div class="panel-body">
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"
                          id="form-image_cache"
                          class="form-horizontal">
                        <div class="well">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div id="progress-wrap" class="progress">
                                        <div id="progress" class="progress-bar progress-bar-info" role="progressbar"
                                             aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="btn-group" role="group">
                                        <button type="button" id="cache-image-start" class="btn btn-success">
                                            <?= $button_run_cache ?> <i class="fa fa-refresh"></i></button>
                                        <button type="button" id="cache-image-stop"
                                                class="btn btn-default"><?= $button_stop_cache ?></button>
                                        <button type="button" id="cache-image-reset"
                                                class="btn btn-danger"><?= $button_reset_cache ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h3><?= $text_settings ?></h3>

                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-general"><?= $tab_general ?></a></li>
                            <li><a data-toggle="tab" href="#tab-advanced"><?= $tab_advanced ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="tab-general" class="tab-pane fade in active">
                                <div class="form-group">
                                    <label class="col-lg-2 col-sm-4">
                                        <?= $input_fields['is_cache_after_save_or_edit']['label'] ?>
                                    </label>
                                    <div class="col-lg-10 col-sm-8">
                                        <input
                                                class="form-control"
                                                type="checkbox"
                                                data-toggle="toggle"
                                                data-on="<?= $text_yes ?>"
                                                data-off="<?= $text_no ?>"
                                                name="<?= $input_fields['is_cache_after_save_or_edit']['name'] ?>"
                                                value="1"
                                            <?= $input_fields['is_cache_after_save_or_edit']['is_checked'] ?>
                                        />
                                    </div>
                                </div>

                                <?php if ($image_sizes_of_current_template) { ?>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="bs-callout bs-callout-info">
                                                <h4><?= $text_message_info_header ?></h4>
                                                <table class="info-size">
                                                    <?php foreach ($image_sizes_of_current_template as $item) { ?>
                                                        <tr>
                                                            <td><?= $item['name'] ?></td>
                                                            <td><?= $item['size']['width'] . 'x' . $item['size']['height'] ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <h4><b><?= $entry_image_sizes ?></b></h4>
                                    </div>
                                    <div class="col-sm-6">
                                        <table id="table-sizes" class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <td class="text-left"><?php echo $entry_width; ?></td>
                                                <td class="text-left"><?php echo $entry_height; ?></td>
                                                <td></td>
                                            </tr>
                                            </thead>
                                            <tbody><?php $sizes_row = 0; ?>

                                            <?php if (isset($input_fields['sizes']['values'])) { ?>
                                                <?php foreach ($input_fields['sizes']['values'] as $field) { ?>
                                                    <tr id="sizes-row<?php echo $sizes_row; ?>">
                                                        <td class="text-left"><input
                                                                    type="text"
                                                                    name="<?= $input_fields['sizes']['base_name'] ?>[<?= $sizes_row ?>][width][value]"
                                                                    value="<?= $field['width']['value']; ?>"
                                                                    id="input-width"
                                                                    class="form-control width"/><?php if (isset($field['width']['error'])) { ?>
                                                                <div class="text-danger"><?= $field['width']['error']; ?></div><?php } ?>
                                                        </td>
                                                        <td class="text-left"><input
                                                                    type="text"
                                                                    name="<?= $input_fields['sizes']['base_name'] ?>[<?= $sizes_row ?>][height][value]"
                                                                    value="<?= $field['height']['value']; ?>"
                                                                    id="input-height"
                                                                    class="form-control height"/><?php if (isset($field['height']['error'])) { ?>
                                                                <div class="text-danger"><?= $field['height']['error']; ?></div><?php } ?>
                                                        </td>
                                                        <td class="text-left">
                                                            <button type="button"
                                                                    onclick="$('#sizes-row<?= $sizes_row; ?>, .tooltip').remove();"
                                                                    data-toggle="tooltip"
                                                                    class="btn btn-danger"><i
                                                                        class="fa fa-minus-circle"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php $sizes_row++; ?>
                                                <?php } ?>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-left">
                                                    <button type="button" onclick="add_sizes();" data-toggle="tooltip"
                                                            class="btn btn-primary"><i
                                                                class="fa fa-plus-circle"></i></button>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <?php if (isset($error_empty_table_sizes)) { ?>
                                            <div class="text-danger"><?= $error_empty_table_sizes; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div id="tab-advanced" class="tab-pane fade">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="bs-callout bs-callout-warning">
                                            <?= $notice_advanced ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-2 col-sm-4 control-label">
                                        <?= $input_fields['quantity_of_images']['label'] ?>
                                    </label>
                                    <div class="col-lg-1 col-sm-2">
                                        <input
                                                id="quantity-of-images"
                                                class="form-control"
                                                type="text"
                                                name="<?= $input_fields['quantity_of_images']['name'] ?>"
                                                value="<?= $input_fields['quantity_of_images']['value'] ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-2 col-sm-4 control-label">
                                        <?= $input_fields['delay_between_requests']['label'] ?>
                                    </label>
                                    <div class="col-lg-1 col-sm-2">
                                        <input
                                                id="delay-between-requests"
                                                class="form-control"
                                                type="text"
                                                name="<?= $input_fields['delay_between_requests']['name'] ?>"
                                                value="<?= $input_fields['delay_between_requests']['value'] ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HIDDEN FIELDS -->
                        <input type="hidden" id="cached-image-count" name="<?= $input_fields['cached_image_count']['name'] ?>" value="<?= $input_fields['cached_image_count']['value'] ?>" />
                    </form>
                </div>
            </div>
        </div>
        <script>
            var sizes_row = '<?= $sizes_row ?>';
            var base_name = "<?= $input_fields['sizes']['base_name'] ?>";

            function add_sizes() {
                var html = '<tr id="sizes-row' + sizes_row + '">';
                html += '<td class="text-left">';
                html += '<input type="text" name="' + base_name + '[' + sizes_row + '][width][value]" class="form-control width" />';
                html += '</td>';
                html += '<td class="text-left">';
                html += '<input type="text" name="' + base_name + '[' + sizes_row + '][height][value]" class="form-control height" />';
                html += '</td>';
                html += '<td class="text-left"><button type="button" onclick="$(\'#sizes-row' + sizes_row + '\').remove();" data-toggle="tooltip" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td></tr>';
                $('#table-sizes tbody').append(html);
                sizes_row++;
            }

        </script>

        <script>

            $(function () {
                $(cacheImage.selectorStart).click(function () {
                    cacheImage.start();
                });

                $(cacheImage.selectorStop).click(function () {
                    cacheImage.stop();
                });

                $(cacheImage.selectorReset).click(function () {
                    cacheImage.reset();
                });
            });

            var cacheImage = {
                selectorStart: '#cache-image-start',
                selectorStop: '#cache-image-stop',
                selectorReset: '#cache-image-reset',

                selectorTableSizes: '#table-sizes',
                selectorQuantityImages: '#quantity-of-images',
                selectorDelayBetweenRequests: '#delay-between-requests',
                selectorCachedImageCount: '#cached-image-count',

                selectorProgressWrap: '#progress-wrap',
                selectorProgress: '#progress',

                actionStart: '<?= $action_cache_start ?>',
                actionSaveLastState: '<?= $action_save_last_state ?>',

                isStop: false,

                start: function () {
                    if (!this.validate()) {
                        alert('<?= $text_js_size_table_error ?>');
                        return false;
                    }

                    $(this.selectorTableSizes + ' .text-danger').html('');

                    var isConfirm = confirm('<?= $text_js_confirm ?>');
                    if (isConfirm) {

                        this.isStop = false;

                        this.tableHoldOn();
                        $(this.selectorProgress).html('0%');
                        $(this.selectorProgress).css('width', '0');
                        $(this.selectorProgress).removeClass('progress-bar-danger');
                        $(this.selectorProgress).removeClass('progress-bar-success');
                        $(this.selectorProgress).addClass('progress-bar-info');

                        $(this.selectorStart).children('i').addClass('fa-spin');

                        this.push();
                    }
                },

                push: function () {
                    if (!this.isStop) {
                        var $this = this;

                        $.ajax({
                            url: $this.actionStart,
                            dataType: 'json',
                            method: 'POST',
                            data: $('form').serialize(),

                            success: function (json) {
                                $($this.selectorCachedImageCount).val(json.cachedImageCount);

                                if (json.error) {
                                    $($this.selectorProgress).removeClass('progress-bar-info');
                                    $($this.selectorProgress).addClass('progress-bar-danger');

                                    $this.stop();

                                    alert(json.error);
                                } else {
                                    if (json.isCompleted) {
                                        $($this.selectorProgress).removeClass('progress-bar-info');
                                        $($this.selectorProgress).addClass('progress-bar-success');
                                        $($this.selectorProgress).html('100%');
                                        $($this.selectorProgress).css('width', '100%');

                                        $this.stop();
                                    } else {
                                        $($this.selectorProgress).css('width', json.percentComplete);
                                        $($this.selectorProgress).html(json.percentComplete);
                                        $this.push();
                                    }
                                }
                            },
                            error: function () {
                                $($this.selectorProgress).removeClass('progress-bar-info');
                                $($this.selectorProgress).addClass('progress-bar-danger');
                                alert('<?= $text_js_server_error ?>');
                                $this.stop();
                            }
                        });
                    }
                },

                stop: function () {
                    this.isStop = true;

                    $(this.selectorStart).children('i').removeClass('fa-spin');

                    this.tableHoldOff();
                    this.saveLastState();
                },

                reset: function () {
                    var isConfirm = confirm('<?= $text_js_confirm ?>');
                    if (isConfirm) {
                        $(this.selectorProgress).addClass('progress-bar-info');
                        $(this.selectorProgress).removeClass('progress-bar-success');
                        $(this.selectorProgress).html('0%');
                        $(this.selectorProgress).css('width', '0%');

                        $(this.selectorCachedImageCount).val('0');
                        this.stop();
                        alert('<?= $text_js_completed ?>');
                    }
                },

                validate: function () {
                    var isSuccess = true;

                    var sizes = this.getImageSizes();
                    if (sizes.length === 0) {
                        isSuccess = false;
                    } else {
                        for (var i = 0; i < sizes.length; i++) {
                            if (sizes[i].width === '' || sizes[i].height === '') {
                                isSuccess = false;
                            }
                        }
                    }

                    return isSuccess;
                },

                getImageSizes: function () {
                    var sizes = [];
                    $(this.selectorTableSizes + ' tbody tr').each(function (key, obj) {
                        sizes.push({
                            width: $(obj).find('.width').val(),
                            height: $(obj).find('.height').val()
                        });
                    });
                    return sizes;
                },
                
                saveLastState: function () {
                    var $this = this;

                    $.ajax({
                        url: $this.actionSaveLastState,
                        dataType: 'json',
                        method: 'POST',
                        data: $('form').serialize(),
                        success: function () {
                            console.log('Last state is saved!');
                        }, 
                        error: function () {
                            console.log('Last state is not saved!');
                        }
                    });
                },

                tableHoldOn: function () {
                    $('input').prop('readonly', true);
                    $(this.selectorTableSizes + ' button').prop('disabled', true);
                },

                tableHoldOff: function () {
                    $('input').prop('readonly', false);
                    $(this.selectorTableSizes + ' button').prop('disabled', false);
                }
            }


        </script>
    </div>
<?php echo $footer; ?>