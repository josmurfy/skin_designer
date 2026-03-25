<?php
if (!empty($errors)) {
    foreach ($errors as $error) { ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="pull-left checkbox-holder" data-toggle="tooltip" data-placement="top" title="Try double clicking here"><input type="checkbox" class="error-checkbox" value="<?php echo $error['message_hash']; ?>" /></div>
                <h4><?php echo htmlentities($error['message']); ?></h4>
                <div class="clearfix"></div>
                <?php if (!empty($error['code_preview'])) { ?>
                <div class="hidden" id="code-preview-<?php echo $error['message_hash']; ?>">
                    <?php echo $error['code_preview']; ?>
                </div>
                <?php } ?>
                <div class="hidden" id="recently-changed-<?php echo $error['message_hash']; ?>"></div>
            </div>
            <div class="panel-footer">
                <ul class="list-unstyles list-inline">
                    <li>Occurrences <span class="badge"><?php echo $error['popularity']; ?></span></li>
                    <li>First appeared on: <span class="badge"><?php echo date("Y-m-d H:i:s", $error['first_appeared']); ?></span></li>
                    <li>Last appeared on: <span class="badge"><?php echo date("Y-m-d H:i:s", $error['last_appeared']); ?></span></li>

                    <?php if (!empty($error['code_preview'])) { ?>
                    <button class="btn btn-sm btn-default code-preview" data-message-hash="<?php echo $error['message_hash']; ?>"><i class="fa fa-eye"></i>&nbsp;Quick code preview</button>
                    <?php } ?>
                    <button class="btn btn-sm btn-default recently-changed" data-message-hash="<?php echo $error['message_hash']; ?>" data-toggle="tooltip" data-placement="top" title="Show modified files a day before and after the error happened"><i class="fa fa-search"></i>&nbsp;Show modified files</button>
                    <button class="btn btn-sm btn-default request-quote" data-message-hash="<?php echo $error['message_hash']; ?>"><i class="fa fa-code"></i>&nbsp;Fix it</button>
                    <button class="btn btn-sm btn-danger pull-right clear-error" data-message-hash="<?php echo $error['message_hash']; ?>"><i class="fa fa-trash"></i>&nbsp;Clear this error</button>
                </ul>
            </div>
        </div>
    <?php } ?>
    <div class="clearfix" style="height: 5px;"></div>
    <hr>
    <div class="row">
      <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
      <div class="col-sm-6"><button class="btn btn-danger btn-clear-selected pull-right"><i class="fa fa-trash"></i>&nbsp;Clear selected errors</button></div>
    </div>
<?php } else { ?>
<h3>You have no errors, how cool is that!</h3>
<?php } ?>
