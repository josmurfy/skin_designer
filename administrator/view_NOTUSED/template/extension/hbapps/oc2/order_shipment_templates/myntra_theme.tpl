<div class="">
  <div class="aHl"></div>
  <div id=":39i" tabindex="-1"></div>
  <div id=":384" class="ii gt">
    <div id=":385" class="a3s aXjCH msg-1366546044976533188"><u></u>
      <div style="font-family:Lato,Helvetica,Arial,sans-serif;font-size:16px;color:#282c3f;line-height:1.5">
        <center>
          <table style="width:680px!important" width="500">
            <tbody>
              <tr>
                <td><table border="0" style="width:100%;border:solid 1px #fff;padding:10px 30px 30px 30px">
                    <tbody>
                      {block1}
                      {block2}
					  <?php foreach ($products as $product) { ?>	
                      <tr>
                        <td style="padding:0 0px 10px 0px"><table style="width:100%;border:solid 1px #ccc;border-radius:4px" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
							 
							<tr>
								<td rowspan="7" class="m_-1366546044976533188imgPadding" style="width:25%;border-right:solid 1px #ccc;padding:0px">
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
                      {block3}
                      {block4}
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table>
        </center>
        <div class="yj6qo"></div>
        <div class="adL"> </div>
      </div>
      <div class="adL"> </div>
    </div>
  </div>
  <div id=":39d" class="ii gt" style="display:none">
    <div id=":39e" class="a3s aXjCH undefined"></div>
  </div>
  <div class="hi"></div>
</div>
