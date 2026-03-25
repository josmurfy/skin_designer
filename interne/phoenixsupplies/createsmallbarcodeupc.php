<?
//echo $_POST['sku'];
//$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if ($_POST['product_id']==""){
	$new=0;
}
//echo $sku;
//echo $_GET['sku'];
if (isset($_GET['product_id']) && $_GET['product_id']!=""){
		$sql = 'SELECT * FROM `oc_product_reception where oc_product.bluestiker = '.$_GET['product_id'];
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);
		$new=1;
	if($data['product_id']!=""){
			$height= "1.325";
			$width= "3.825";
			$border=1;
			$new=0;
	}else{
			$height= "0.825";
			$width= "3.825";
			$border=0;
			$new=1;
}

}?>

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

	<table style="width: 3.825in;" cellpadding="0" cellspacing="0">
			<tr>
			<td align="center"  valign="top">
				
				<svg class="barcode"
					jsbarcode-value="<?echo $data['upc'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="40"
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="24">
					
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
			</td>
			</tr>
	</table>

<script type="text/javascript">
	window.//print();
</script>
</body>
</html>
<? // on ferme la connexion � mysql mysqli_close($db); 
mysqli_close();?>