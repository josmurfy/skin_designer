		 <div class="table-responsive">
			<table class="table table-bordered table-hover">
			  <thead>
				<tr>
				  <td class="text-center">Order ID</td>
				  <td class="text-left">Product </td>
				  <td class="text-left">Model</td>
				  <td class="text-center">Shipped Qty</td>
				  <td class="text-left">Shipping Partner</td>
				  <td class="text-center">Tracking Code</td>
				  <td class="text-right">Delivery Date</td>
				  <td class="text-center">Mail Status</td>
				  <td class="text-right">Date Added</td>
				  <td class="text-right">Date Modified</td>
				</tr>
			  </thead>
			  <tbody>
				<?php if ($records) { ?>
				<?php foreach ($records as $record) { ?>
				<tr>
				  <td class="text-center"><a href="<?php echo $record['order_link']; ?>" target="_blank"><?php echo $record['order_id']; ?></a></td>
				  <td class="text-left"><a href="<?php echo $record['product_link']; ?>" target="_blank"><?php echo $record['product']; ?></a></td>
				  <td class="text-left"><?php echo $record['model']; ?></td> 				
	  	   		  <td class="text-center"><?php echo $record['shipped_qty']; ?></td>
				  <td class="text-left"><?php echo $record['shipping_partner']; ?></td>
				  <td class="text-center"><a href="<?php echo $record['tracking_link']; ?>" target="_blank"><?php echo $record['code']; ?></a></td>
				  <td class="text-right"><?php echo $record['delivery_date']; ?></td>
				  <td class="text-center"><?php echo $record['mail']; ?></td>
				  <td class="text-right"><?php echo $record['date_added']; ?></td>
				  <td class="text-right"><?php echo $record['date_modified']; ?></td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr><td class="text-center" colspan="10">No Records Found</td></tr>
				<?php } ?>				
			</tbody>
			</table>
		</div>
		
		<div class="row">
		  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
		  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
		</div>
