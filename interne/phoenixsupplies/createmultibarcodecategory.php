<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

			$sql = 'SELECT category_name,category_id FROM oc_ebay_listing_sync where usa=1 group by category_name';
			 // on envoie la requête
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement




			
?>
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
	<?
	$j=0;$i=0;
		while($data = mysqli_fetch_assoc($req))
			{
	?>	
		<?

		
if ($j==0){?><tr><?}?>
		<td align="center"  valign="middle">
		<?echo $data['category_name'];?>
		<br>
			<svg class="barcode"
				jsbarcode-value="<?echo $data['category_id'];?>"
				jsbarcode-textmargin="0"
				jsbarcode-height="40"
				jsbarcode-fontoptions="bold"
				jsbarcode-fontsize="12">
				
			</svg>
			<script>
			JsBarcode(".barcode").init();
			</script><br>
		
		</td>
<?if ($j==6){?>		</tr><?$j=0;$i++;}else{$j++;}?>
		<?
		if($i==8){echo '</table><br><br><br><table style="width: 11in;" border="1" cellpadding="0" cellspacing="0">'; $i=0;}
		}
		?>

	</table>
<script type="text/javascript">
	window.//print();
</script>
</body>
</html>
</html>
<? // on ferme la connexion à mysql 
mysqli_close($db); ?>