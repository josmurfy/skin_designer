<?
//echo (string)$_POST['sku'] ;
//$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
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
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script>    tinymce.init({      selector: '#mytextarea'    });  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <link rel="stylesheet" href="https://www.phoenixliquidation.ca/interne/paper.css">>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
      <style type="text/css" media="print">
    @page { size: 76mm 25mm margin: 0mm 0mm 0mm 0mm;} /* output size */
    body.receipt .sheet { width: 74mm; height: 25mm } /* sheet size */
    @media print { body.receipt { width: 76mm } } /* fix for Chrome */
  </style>
</head>
<body>

  <section class="sheet padding-0mm">
	
	<table style="border:0px solid black;margin-left:auto;margin-right:auto;">  
			<tr>
			<td align="center"  valign="top">
				
				<svg class="barcode"
					jsbarcode-value="<?echo $data['sku'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="30"
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="20">
					
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
			</td>
			</tr>

	</table>
  </section>

<script type="text/javascript">
	window.//print();
</script>
</body>
</html>
<? // on ferme la connexion � mysql mysqli_close($db); 
mysqli_close($db);?>