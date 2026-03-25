<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-cron-job" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-cron-job" class="form-horizontal">
           <fieldset>
             <div class="form-group required">
               <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
               <div class="col-sm-10">
                 <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                 <?php if ($error_name) { ?>
                 <div class="text-danger"><?php echo $error_name; ?></div>
                 <?php } ?>

                 <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_name; ?></div>
               </div>
             </div>
             <div class="form-group">
               <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
               <div class="col-sm-10">
                 <select name="status" id="input-status" class="form-control">
                   <?php if ($status) { ?>
                   <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                   <option value="0"><?php echo $text_disabled; ?></option>
                   <?php } else { ?>
                   <option value="1"><?php echo $text_enabled; ?></option>
                   <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                   <?php } ?>
                 </select>
               </div>
             </div>
          </fieldset>

          <fieldset>
             <legend class="small text-center"><?php echo $legend_when; ?></legend>
             <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-month"><?php echo $entry_month; ?></label>
                <div class="col-sm-10">
                   <div class="well well-sm" style="height: 150px; overflow: auto;">
                     <?php foreach ($months_of_year as $month_of_year) { ?>
                     <div class="checkbox checkbox-column medium-column">
                      <label>
                        <?php if (in_array($month_of_year['code'], $month)) { ?>
                        <input type="checkbox" name="month[]" value="<?php echo $month_of_year['code']; ?>" checked="checked" />
                        <?php echo $month_of_year['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="month[]" value="<?php echo $month_of_year['code']; ?>" />
                        <?php echo $month_of_year['name']; ?>
                        <?php } ?>
                      </label>
                     </div>
                     <?php } ?>
                  </div>

                  <?php if ($error_month) { ?>
                  <div class="text-danger"><?php echo $error_month; ?></div>
                  <?php } ?>

                   <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_month; ?></div>
                </div>
             </div>
             <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-day"><?php echo $entry_day; ?></label>
                <div class="col-sm-10">
                   <div class="well well-sm" style="height: 150px; overflow: auto;">
                     <?php for ($day_of_month = 1; $day_of_month <= 31; $day_of_month++) { ?>
                     <div class="checkbox checkbox-column small-column">
                      <label>
                        <?php if (in_array($day_of_month, $day)) { ?>
                        <input type="checkbox" name="day[]" value="<?php echo $day_of_month; ?>" checked="checked" />
                        <?php echo $day_of_month; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="day[]" value="<?php echo $day_of_month; ?>" />
                        <?php echo $day_of_month; ?>
                        <?php } ?>
                      </label>
                     </div>
                     <?php } ?>
                   </div>

                   <?php if ($error_day) { ?>
                   <div class="text-danger"><?php echo $error_day; ?></div>
                   <?php } ?>

                   <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_day; ?></div>
                </div>
             </div>
             <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-hour"><?php echo $entry_hour; ?></label>
                <div class="col-sm-10">
                   <div class="well well-sm" style="height: 150px; overflow: auto;">
                     <?php for ($hour_of_day = 0; $hour_of_day <= 23; $hour_of_day++) { ?>
                     <div class="checkbox checkbox-column small-column">
                      <label>
                        <?php if (in_array($hour_of_day, $hour)) { ?>
                        <input type="checkbox" name="hour[]" value="<?php echo $hour_of_day; ?>" checked="checked" />
                        <?php echo $hour_of_day; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="hour[]" value="<?php echo $hour_of_day; ?>" />
                        <?php echo $hour_of_day; ?>
                        <?php } ?>
                      </label>
                     </div>
                     <?php } ?>
                   </div>

                   <?php if ($error_hour) { ?>
                   <div class="text-danger"><?php echo $error_hour; ?></div>
                   <?php } ?>

                   <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_hour; ?></div>
                </div>
             </div>
             <div class="form-group required">
              <label class="col-sm-2 control-label required" for="input-minute"><?php echo $entry_minute; ?></label>
              <div class="col-sm-10">
                 <select name="minute" id="input-minute" class="form-control">
                    <?php for ($minute_of_hour = 0;  $minute_of_hour <= 59; $minute_of_hour += $minute_step) { ?>
                    <?php if ($minute_of_hour == $minute) { ?>
                    <option value="<?php echo $minute_of_hour; ?>" selected="selected"><?php echo $minute_of_hour; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $minute_of_hour; ?>"><?php echo $minute_of_hour; ?></option>
                    <?php } ?>
                    <?php } ?>
                 </select>

                 <?php if ($error_minute) { ?>
                 <div class="text-danger"><?php echo $error_minute; ?></div>
                 <?php } ?>

                <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_minute; ?></div>
              </div>
             </div>
             <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-weekday"><?php echo $entry_weekday; ?></label>
                <div class="col-sm-10">
                   <div class="well well-sm" style="height: 150px; overflow: auto;">
                     <?php foreach ($days_of_week as $day_of_week) { ?>
                     <div class="checkbox">
                      <label>
                        <?php if (in_array($day_of_week['code'], $weekday)) { ?>
                        <input type="checkbox" name="weekday[]" value="<?php echo $day_of_week['code']; ?>" checked="checked" />
                        <?php echo $day_of_week['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="weekday[]" value="<?php echo $day_of_week['code']; ?>" />
                        <?php echo $day_of_week['name']; ?>
                        <?php } ?>
                      </label>
                     </div>
                     <?php } ?>
                   </div>

                   <?php if ($error_weekday) { ?>
                   <div class="text-danger"><?php echo $error_weekday; ?></div>
                   <?php } ?>

                   <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_weekday; ?></div>
                </div>
             </div>
          </fieldset>

          <fieldset>
             <legend class="small text-center"><?php echo $legend_what; ?></legend>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
