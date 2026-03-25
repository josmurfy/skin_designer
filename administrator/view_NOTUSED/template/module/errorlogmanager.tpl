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
       <?php if ($error_warning) { ?>
            <div class="alert alert-danger autoSlideUp"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
             <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success autoSlideUp"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i>&nbsp;<span style="vertical-align:middle;font-weight:bold;">Module settings</span></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                    <div class="tabbable">
                        <div class="tab-navigation form-inline">
                            <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
                                <li><a href="#control_panel" data-toggle="tab"><i class="fa fa-power-off"></i>&nbsp;Control Panel</a></li>
                            </ul>
                            <div class="tab-buttons">
                              <div class="input-group">
                                <input class="form-control" placeholder="Search" id="searchInput" />
                                <span class="input-group-addon" id="btnSearch"><i class="fa fa-search"></i></span>
                              </div>
                              <select class="form-control" id="option-file" title="Choose an error log file">
                                <?php foreach ($log_files as $file) { ?>
                                <option value="<?php echo $file;?>"<?php echo ($file == $main_log_file) ? ' selected' : ''; ?>><?php echo $file;?></option>
                                <?php } ?>
                              </select>
                              <button class="btn btn-default" id="btnRefresh" onclick="refresh_db_entries(); return false;"><i class="fa fa-refresh"></i>&nbsp;Re-scan</button>
                            </div> 
                        </div><!-- /.tab-navigation --> 
			<div id="options">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-4">
							<fieldset>
								<legend>Date range</legend>
								<div class="row">
									<div class="col-md-6">
										<div class="input-group date">
											<input type="text" class="form-control" id="filter-from" placeholder="Date From" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="input-group date">
											<input type="text" class="form-control" id="filter-to" placeholder="Date To" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
						<div class="col-md-4">
							<fieldset>
								<legend>Extension</legend>
								<div>
									<select class="form-control" id="option-extension">
										<option value="">None</option>
										<?php foreach ($extensions as $group=>$exts) { ?>
										<optgroup label="<?php echo $group;?>">
											<?php foreach ($exts as $e) { ?>
											<option value="<?php echo $e['file'];?>"><?php echo $e['title'];?></option>
											<?php } ?>
										</optgroup>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</div>
						<div class="col-md-4">
							<fieldset>
								<legend>Sort order</legend>
								<div>
									<select class="form-control" id="option-sort">
										<option value="popularity desc" selected>Most occurrences first</option>
										<option value="popularity asc">Least occurrences first</option>
										<option value="timestamp desc">Most recent first</option>
										<option value="timestamp asc">Least recent first</option>
									</select>
								</div>
							</fieldset>
						</div>
					</div>
					<hr>
					<button class="btn btn-primary pull-right" id="btnFiltersApply"><i class="fa fa-check"></i>&nbsp;Filter</button>
				</div>
			<hr>
			</div>
                        <div class="tab-content">
                        <?php
                        if (!function_exists('modification_vqmod')) {
                        	function modification_vqmod($file) {
                        		if (class_exists('VQMod')) {
                       				return VQMod::modCheck(modification($file), $file);
                        		} else {
                        			return modification($file);
                       			}
                        	}
                        }
						?>
                            <div id="control_panel" class="tab-pane fade"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/module/errorlogmanager/tab_controlpanel.php'); ?></div>
                        </div> <!-- /.tab-content --> 
                    </div><!-- /.tabbable -->
                </form>
            </div> 
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="requestQuoteModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalLabel">Request quote</h4>
      </div>
      <div class="modal-body">
	<div class="alert alert-success init-hidden" role="alert" id="modal-alert-success"></div>
	<div class="alert alert-danger init-hidden" role="alert" id="modal-alert-danger"></div>
	<div class="init-hidden" id="modal-on-fail"></div>
	<div class="init-hidden" id="modal-on-quote"></div>
	<div id="modal-body">
		<p>This option will provide you with a quote for fixing the error. Once you purchase the fix, our team will contact you to let us know how can we connect to your server and fix the error. Please make sure the contact information below is correct.</p>
		<form id="quoteForm">
			<div class="form-group">
				<label>E-mail:</label>
				<input class="form-control" type="text" placeholder="<?php echo $admin_mail; ?>" value="<?php echo $admin_mail; ?>" name="admin_mail" required />
			</div>

			<div class="form-group">
				<label>Name:</label>
				<input class="form-control" type="text" placeholder="<?php echo $admin_name; ?>" value="<?php echo $admin_name; ?>" name="admin_name" required />
			</div>
		</form>
	</div>
      </div>
      <div class="modal-footer">
	<i class="fa fa-spinner fa-spin init-hidden modalSpinner" style="font-size: 24px;"></i>&nbsp;
        <button type="button" class="btn btn-default init-hidden" data-dismiss="modal" id="btn-modal-thanks">Thank you!</button>
        <button type="button" class="btn btn-success init-hidden" data-dismiss="modal" id="btn-modal-purchase" onclick="goToPage(this);">Buy now</button>
        <button type="button" class="btn btn-default modal-main-button" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary modal-main-button" id="btn-modal-get-quote" onclick="requestQuote(this);" >Request fix</button>
        <button type="button" class="btn btn-primary modal-main-button init-hidden" id="btn-modal-mail" onclick="requestQuoteMail(this);" ><i class="fa fa-paper-plane-o"></i>&nbsp;Send message</button>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
$('#mainTabs a:first').tab('show'); // Select first tab
if (window.localStorage && window.localStorage['currentTab']) {
	$('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
}
if (window.localStorage && window.localStorage['currentSubTab']) {
	$('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
}
$('.fadeInOnLoad').css('visibility','visible');
$('.mainMenuTabs a[data-toggle="tab"]').click(function() {
	if (window.localStorage) {
		window.localStorage['currentTab'] = $(this).attr('href');
	}
});
$('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .review_tabs a[data-toggle="tab"])').click(function() {
	if (window.localStorage) {
		window.localStorage['currentSubTab'] = $(this).attr('href');
	}
});

$('.date').datetimepicker({
	pickTime: false
});
</script>
