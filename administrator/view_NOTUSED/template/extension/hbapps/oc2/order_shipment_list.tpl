		 <div class="table-responsive">
			<table class="table table-bordered table-hover">
			  <thead>
				<tr>
				  <td width="1" style="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
				  <td class="text-left"><?php echo $column_name; ?></td>
				  <td class="text-left"><?php echo $column_link; ?></td>
				  <td class="text-center"><?php echo $column_action; ?></td>
				</tr>
			  </thead>
			  <tbody>
				<?php if ($records) { ?>
				<?php foreach ($records as $record) { ?>
				<tr>
				  <td class="text-center"><input type="checkbox" name="selected[]" value="<?php echo $record['id']; ?>" /></td>
				  <td class="text-left"><?php echo $record['name']; ?></td>
				  <td class="text-left"><?php echo $record['link']; ?></td>
				  <td class="text-center"><a onclick="openEditModal('<?php echo $record['name']; ?>','<?php echo $record['link']; ?>','<?php echo $record['id']; ?>');" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr><td class="text-center" colspan="4">No Records Found</td></tr>
				<?php } ?>				
			</tbody>
			</table>
		</div>
		
		<div class="row">
		  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
		  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
		</div>
