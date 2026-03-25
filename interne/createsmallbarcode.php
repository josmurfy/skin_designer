<?
//echo (string)$_POST['sku'] ;
//$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';

if ($_POST['product_id']==""){
	$new=0;
}
//echo $sku;
//echo $_GET['sku'];
if (isset($_GET['product_id']) && $_GET['product_id']!=""){
		$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and oc_product.product_id = '.$_GET['product_id'];
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

<html>
<head> 
  <title>receipt</title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <link rel="stylesheet" href="https://www.phoenixliquidation.ca/interne/paper.css">
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
   <style type="text/css" media="print">
    @page { size: 76mm 25mm margin: 0mm 0mm 0mm 0mm;} /* output size */
    body.receipt .sheet { width: 74mm; height: 25mm } /* sheet size */
    @media print { body.receipt { width: 76mm } } /* fix for Chrome */
  </style>
  </style>
</head>
<body class="receipt">
  <section class="sheet padding-0mm">
		<table cellpadding="0" cellspacing="0">  
			<tr>
			<td align="center"  valign="top">
				
				<svg class="barcode"
					jsbarcode-value="<?echo $data['sku'];?>"
					jsbarcode-textmargin="0"
					jsbarcode-height="20"
					jsbarcode-width="1"					
					jsbarcode-fontoptions="bold"
					jsbarcode-fontsize="18">
					
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script>
			</td>
			</tr>
	</table>
	</section>

<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
  <script>
$(document).ready(function () {
        window.//print();
    });

    window.onafterprint = function () {
        $('.printpage', window.parent.document).hide();
    }
  </script>
</body>
</html>
<? // on ferme la connexion � mysql mysqli_close($db); 
mysqli_close($db);?>