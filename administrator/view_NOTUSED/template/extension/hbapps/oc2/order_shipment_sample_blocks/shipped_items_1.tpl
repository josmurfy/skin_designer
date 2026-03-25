<table border="0" style="width:100%;border:solid 1px #fff;padding:10px 5px 30px 5px">
<?php foreach ($products as $product) { ?>
<tr>
<td align="left">
    <!--[if mso]><table width="560" cellpadding="0" cellspacing="0"><tr><td width="178" valign="top"><![endif]-->
    <table class="es-left" cellspacing="0" cellpadding="0" align="left">
        <tbody>
            <tr>
                <td width="178" valign="top" align="center">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <a href target="_blank"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="adapt-img" title="<?php echo $product['name']; ?>"></a></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <!--[if mso]></td><td width="20"></td><td width="362" valign="top"><![endif]-->
    <table cellspacing="0" cellpadding="0" align="right">
        <tbody>
            <tr>
                <td width="320" align="left">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td align="left">
                                    <p><br></p>
                                    <table style="width: 100%;" cellspacing="1" cellpadding="1" border="0">
                                        <tbody>
                                            <tr>
                                                <td><?php echo $product['name']; ?>
												<?php foreach ($product['option'] as $option) { ?>
												  <br />
												  &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
												  <?php } ?>
												</td>
                                                <td style="text-align: center;" width="60">x <?php echo $product['quantity']; ?></td>
                                                <td style="text-align: center;" width="100"><?php echo $product['price']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p><br></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <!--[if mso]></td></tr></table><![endif]-->
</td>
	
</tr>
<?php } ?>
</table>