<?
//echo (string)$_POST['sku'] ;
//$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if ((string)$_POST['sku'] !="" || $_GET['sku']!=""){
	$new=0;
}


//echo $sku;
//echo $_GET['sku'];

		$sql = 'SELECT oc_product.product_id,sku,name,price,condition_id FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and oc_product_description.language_id=2 and oc_product.sku = "'.$_GET['sku'].(string)$_POST['sku'] .'" limit 1';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 

		//echo $data['product_id'];



?>

<!DOCTYPE html>
<html>
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
  <meta charset="utf-8">
  <title>receipt</title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <link rel="stylesheet" href="https://www.phoenixliquidation.ca/interne/paper.css">
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
   <style type="text/css" media="print">
    @page { size: 3.15in 1.10in landscape;} /* output size */
    body.receipt .sheet { width: 2.9in; height: .96in } /* sheet size */
    @media print { body.receipt { width: 2.9in } } /* fix for Chrome */
  </style>
  <?/*   <style type="text/css" media="print">
    @page { size: 76mm 25mm margin: 0mm 0mm 0mm 0mm;} /* output size */
   // body.receipt .sheet { width: 74mm; height: 25mm } /* sheet size */
   // @media print { body.receipt { width: 76mm } } /* fix for Chrome */
  //</style>*/?>
</head>

<body class="receipt">
<?
		$data = mysqli_fetch_assoc($req);
			$sql2 = 'SELECT * FROM `oc_product_special` where product_id = '.$data['product_id'];
	//echo $sql2;
			// on envoie la requ�te
			$req2 = mysqli_query($db,$sql2);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$data2 = mysqli_fetch_assoc($req2);
			$new=1;

if ($_GET['qtetot']>0){
	$qtetot=$_GET['qtetot'];
	$qtemag=$_GET['qtemag'];
}
	$endprix=.95;
				// }
				$data2['price']= number_format($data2['price']*1.34, 2, '.', '');
				$price_replace=explode('.',$data2['price']);
				$data2['price']=$price_replace[0]+$endprix;
				

		$i=1;
		if(strlen( $data['sku'])>12){
		for ($i=1;$i<=$qtetot;$i++){?>
  <section class="sheet padding-0mm">
	
	<table  align="center"  valign="middle">  

			<tr>
			<td align="center"  valign="middle">
			_________________<br>
				<br>
				<svg class="barcode"
					jsbarcode-value="<?echo $data['sku'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="20"
					jsbarcode-width="2"
					
					jsbarcode-fontsize="20">
					
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>	
			</td>
			</tr>
	</table>
  </section>
		<?}}/*jsbarcode-fontoptions="bold"*/?>

<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<?if ($_GET['qtetot']>0){?>


  <script>
$(document).ready(function () {
        window.//print();
    });

    window.onafterprint = function () {
        $('.printpage', window.parent.document).hide();
    }
  </script>
  <?}?>
</body>
</html>
<?/* for ($i=1;$i<=$qtemag;$i++){?>
  <section class="sheet padding-2mm">
	
	<table style="border:0px solid black;margin-left:auto;margin-right:auto;">  
			<tr>
			<td colspan="2" align="center"  valign="top" >
			
				<font size=1>----------------------------------</>
			</td>
			</tr>
<tr>
			
			
			<td align="middle"  valign="top" >
				<?echo "<font size=1><strong>Avant TX:</strong></> ";?>	
			<?echo "<font size=3><strong>$".number_format($data2['price'],2)."</strong></> ";?>	
			
			</td>
			<td align="middle"  valign="top" >
				<?echo " <font size=1><strong>TX incluses:</strong></> ";?>
				<?echo "<font size=3><strong>$".number_format($data2['price']*1.14975,2)."</strong></> ";?>
			</td>
			</tr>
			<tr>
			<td colspan="2" align="center"  valign="top" width="100%">
				
				<svg class="barcode"
				
					jsbarcode-value="<?echo $data['sku'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="30"
					jsbarcode-width="2"
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="12">	
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
				<?//echo "<font size=1><br>".substr($data['name'],80)."</>";?>
			</td>
</tr>

	</table>
  </section>
		<?} */ // on ferme la connexion � mysql mysqli_close($db); 
mysqli_close($db);?>