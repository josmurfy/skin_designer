<div class="">
	<div class="loader1 hide">
		<img src="view/image/loader.gif" alt="loading" title="loading"/>
	</div>
		<table>
		<?php $i=0; if(isset($products)){ ?>
			 <?php foreach ($products as $product) { ?>
		<tr>			
			<td class="text-center imagepading" width="15%"><?php if ($product['thumb']) { ?>
            <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" />
             <?php } ?></td>				  
			<td class="text-right" width="20%"><?php echo $product['name']; ?> <?php if ($product['option']) { ?>
              <?php foreach ($product['option'] as $option) { ?>
              <br />
              <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
              <?php } ?>
              <?php } ?>
            </td>
			<td class="text-right" width="15%"><div id="priceedit<?php echo $i?>"><i  class="pprices" rel="<?php echo $i?>" rel1="<?php echo $product['prices']; ?>"> <span class="productprice quickspan"><?php echo $product['price'];?></span></i></div></td>
			<td width="15%">
			 <input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" class="form-control quantity<?php echo $i?>" />
			</td>
			<td class="text-right" width="15%"><?php echo $product['total']; ?></td>
			<td width="20%">
			<button type="button" data-toggle="tooltip" title="Update" class="btn refresh update myButton1"  rel="<?php echo $product['key']; ?>" rel1="<?php echo $i?>"><i class="fa fa-refresh"></i></button>
			
			<button type="button" data-toggle="tooltip" title="Remove" class="btn refresh remove myButton2"  rel="<?php echo $product['key']; ?>"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
			</td>			
		 </tr>	
		
		<?php $i++; } } ?>
		</table>		
		 </div> 
<script type="text/javascript">

</script>

<script>
$(document).on('click','.pprices',function() {
    rel=$(this).attr('rel');
    rel1=$(this).attr('rel1');
  	 html ='<input type="text" name="price" class="form-control price'+rel+'" value="'+rel1+'" />';
    $('#priceedit'+rel).html(html);
  });
</script>
