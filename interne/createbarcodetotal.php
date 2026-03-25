<?
//echo (string)$_POST['sku'] ;
//$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';
require('print/fpdf.php');

// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
?>

<!DOCTYPE html>
<html>
<head> 
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
   <style>
 table, th, td {
  border: 0px ;
  padding: 8px;
}

   .center {
  margin-left: auto;
  margin-right: auto;
}
      </style>
  <script src="<?echo $GLOBALS['WEBSITE'];?>catalog/view/javascript/qr/qrcode.min.js" type="text/javascript"></script>
<script src="<?echo $GLOBALS['WEBSITE'];?>catalog/view/javascript/qr/qrcode.js" type="text/javascript"></script>
</head>

<body class="receipt">


<?

		
			$new=1;

if ($_GET['qtetot']>0){
	$qtetot=$_GET['qtetot'];
	$qtemag=$_GET['qtemag'];
}
	

		$i=1;
	//	if(!is_numeric( $_GET['sku'])){
			for ($i=1;$i<=$qtetot;$i++){
						//echo $i;
					?>
				<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				
				<td  >
								<div id="qrcode<?echo $i; ?>">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode<?echo $i; ?>", {
					text: "<?echo $_GET['sku']; ?>",
					width: 70, 
					height: 70,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
					
				</div>
				</td>
				<td >
				
				<div  style="text-align: center;font-size: 17px; "><strong><?echo $_GET['sku']; ?></strong></div>
				
				<div  style="text-align: center;font-size: 17px; "></div>
				</td>
				
				</tr>
				</table>
					  </section>
				<?
				$label='<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				
				<td  >
								<div id="qrcode'.$i.'">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode'.$i.'", {
					text: "'.$_GET['sku'].'",
					width: 70, 
					height: 70,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
					
				</div>
				</td>
				<td >
				
				<div  style="text-align: center;font-size: 17px; "><strong>'.$_GET['sku'].'</strong></div>
				
				<div  style="text-align: center;font-size: 17px; "></div>
				</td>
				
				</tr>
				</table>
					  </section>';	}
	//	}
	
			
		?>

<?

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,$label);
$content = $pdf->Output('doc.pdf','F');
?>

<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
  <script>
$(document).ready(function () {
        window.//print();
    });

    window.onafterprint = function () {
      //  $('.printpage', window.parent.document).hide();
	  window.close();
    }
  </script>
</body>
</html>
<? // on ferme la connexion � mysql mysqli_close($db); 
?>