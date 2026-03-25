<table border="0" style="width:100%;border:solid 1px #fff;padding:10px 5px 30px 5px">
<tbody>
  <?php foreach ($products as $product) { ?>	
  <tr>
	<td style="padding:0 0px 10px 0px"><table style="width:100%;border:solid 1px #ccc;border-radius:4px" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		 
		  <tr>
			<td rowspan="7" style="width:25%;border-right:solid 1px #ccc;padding:0px">
			<img src="<?php echo $product['image']; ?>" style="text-align:center;border-radius:4px 0 0 4px;border:0;padding:10px;margin:0;vertical-align:top" tabindex="0">
			  </td>
			<td rowspan="3" style="width:50%;padding:10px 0px 0px 16px;text-align:left;font-weight:bold" valign="top"> <?php echo $product['name']; ?>
				<?php foreach ($product['option'] as $option) { ?>
								  <br />
								  &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
								  <?php } ?> </td>
			<td style="width:25%;text-align:right;padding:10px 15px 0px 0px"> </td>
		  </tr>
		  <tr>
			<td style="text-align:right;padding-right:15px"><?php echo $text_quantity; ?>: <?php echo $product['quantity']; ?></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td style="padding-left:16px;text-align:left"><?php echo $text_price; ?></td>
			<td style="padding-right:15px;text-align:right"><?php echo $product['price']; ?> </td>
		  </tr>
		  <tr>
			<td style="padding-left:16px;text-align:left;font-weight:bold;border-bottom:solid 1px #ccc;"><?php echo $text_total; ?></td>
			<td style="padding-right:15px;text-align:right;font-weight:bold;border-bottom:solid 1px #ccc;"><?php echo $product['total']; ?> </td>
		  </tr>
		 
		</tbody>
	  </table></td>
  </tr>
   <?php } ?>
</tbody>
</table>