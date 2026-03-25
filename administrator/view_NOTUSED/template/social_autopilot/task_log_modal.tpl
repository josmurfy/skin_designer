<div id="sap-task-log-modal" class="modal modal-ocx-sap fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $button_close; ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-uppercase"><?php echo $heading_task_log; ?></h4>
      </div>
      <div class="modal-body">
         <?php if ($total_pages) { ?>
         <div id="task-statistics">
            <div class="row">
               <div class="col-sm-4">
                  <div class="task-stats-box">
                     <div class="stats-key"><?php echo $text_total_pages; ?></div>
                     <div class="stats-value"><?php echo $total_pages; ?></div>
                  </div>
               </div>
               <div class="col-sm-4">
                  <div class="task-stats-box">
                     <div class="stats-key"><?php echo $text_total_success_pages; ?></div>
                     <div class="stats-value"><?php echo $total_success_pages; ?></div>
                  </div>
               </div>
               <div class="col-sm-4">
                  <div class="task-stats-box no-border-right">
                     <div class="stats-key"><?php echo $text_success_rate; ?></div>
                     <div class="stats-value"><?php echo $success_rate; ?> %</div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <div id="task-response"></div>
      </div>
      <div class="modal-footer">
        <a class="btn-u btn-block" data-dismiss="modal"><?php echo $button_close; ?></a>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#task-response').jsonViewer(eval('(<?php echo $task_log; ?>)'), { collapsed: false, withQuotes: false });
//--></script>
