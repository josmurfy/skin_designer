<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
   

    <script type="text/javascript" src="scripts/jHtmlArea-0.8.js"></script>
    <link rel="Stylesheet" type="text/css" href="style/jHtmlArea.css" />
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script> 
    <style type="text/css">
        /* body { background: #ccc;} */
        div.jHtmlArea .ToolBar ul li a.custom_disk_button 
        {
            background: url(images/disk.png) no-repeat;
            background-position: 0 0;
        }
        
        div.jHtmlArea { border: solid 1px #ccc; }
    </style>
</head>
<body>

	<table style="width: 11in;" border="1" cellpadding="0" cellspacing="0">
		
		<?$j=0;
		for ($i = 1; $i <= 32; $i++) {
			$rdsku=mt_rand ( 100000000000 , 999999999999 );
		
if ($j==0){?><tr><?}?>
		<td align="center"  valign="middle">
		<br>
			<svg class="barcode"
				jsbarcode-value="<?echo $rdsku;?>"
				jsbarcode-textmargin="0"
				jsbarcode-height="40"
				jsbarcode-fontoptions="bold"
				jsbarcode-fontsize="12">
				
			</svg>
			<script>
			JsBarcode(".barcode").init();
			</script><br>
		
		</td>
<?if ($j==3){?>		</tr><?$j=0;}else{$j++;}?>
		<?}?>

	</table>
<script type="text/javascript">
	window.//print();
</script>
</body>
</html>