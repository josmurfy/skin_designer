<?
//echo (string)$_POST['sku'] ;
//$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
ob_start();
include 'connection.php';
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
 <link rel="stylesheet" href="<?echo $GLOBALS['WEBSITE'];?>/interne/paper.css">
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
	  <style type="text/css" media="print">
@page { size: 76mm 25mm margin: 10mm 10mm 10mm 10mm;} /* output size */
body.receipt .sheet { width: 74mm; height: 25mm margin: 10mm 10mm 10mm 10mm;} /* sheet size */
@media print { body.receipt { width: 76mm } } /* fix for Chrome */
</style>
<style type="text/css">
.center {
margin-left: auto;
margin-right: auto;
}
</style>
  <script src="<?echo $GLOBALS['WEBSITE'];?>/catalog/view/javascript/qr/qrcode.min.js" type="text/javascript"></script>
<script src="<?echo $GLOBALS['WEBSITE'];?>/catalog/view/javascript/qr/qrcode.js" type="text/javascript"></script>
</head>
<body class="receipt">
<?
/* 				<svg class="barcode"
					jsbarcode-value="'.$lettre.$i.'"
					jsbarcode-format: "EAN8"
					jsbarcode-width="1"
					jsbarcode-height="40"
					jsbarcode-displayValue="false">	
				</svg>
				<script>
				JsBarcode(".barcode").init();
				</script> */
					?>
				<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				<td class="center">
<? 
$bas="";
$basascii="";
$_GET['tablette']=strtoupper($_GET['tablette']);
if(strpos($_GET['tablette'],"BAS")!==false){
	$basascii="&#x2B07;";
	$bas="BAS";
	$_GET['tablette']=str_replace("BAS","",$_GET['tablette']);
	}?>
				<div  style="text-align: center;font-size: 55px; "><strong><?echo $_GET['tablette'].$basascii; ?></strong>|</div>
				</td>
				<td class="center">
								<div id="qrcode<?echo $_GET['tablette'].$bas; ?>">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode<?echo $_GET['tablette'].$bas; ?>", {
					text: "<?echo $_GET['tablette'].$bas; ?>",
					width: 60, 
					height: 60,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
				</div>
				</td>
				</tr>
				</table>
					  </section>
				<?
				/*	for($i=1;$i<=6;$i++){//.'&#x2B07
					?>
				<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				<td class="center">
				<div  style="text-align: center;font-size: 65px; "><strong><?echo $_GET['tablette']; ?>&#x2B07;</strong>|</div>
				</td>
				<td class="center">
								<div id="qrcode<?echo $_GET['tablette']; ?>BAS">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode<?echo $_GET['tablette']; ?>BAS", {
					text: "<?echo $_GET['tablette']; ?>BAS",
					width: 70, 
					height: 70,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
				</div>
				</td>
				</tr>
				</table>
					  </section>
				<?	}*/
			//etiquette du bas
/* 			foreach($rangees as $rangee) {
				$nbsection=$sections[$rangee-1];
				for ($j=0;$j<$nbsection;$j++){
					$lettre=$lettres[$j];
					for($i=1;$i<8;$i++){//.'&#x2B07
					?>
				<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				<td class="center">
				<div  style="text-align: center;font-size: 60px; "><strong><?echo $rangee.'-'.$lettre.$i.'&#8595;'; ?></strong>|</div>
				</td>
				<td class="center">
								<div id="qrcode<?echo $_GET['tablette']; ?>">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode<?echo $_GET['tablette']; ?>", {
					text: "<?echo $rangee.'-'.$lettre.$i; ?>BAS",
					width: 50, 
					height: 50,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
				</div>
				</td>
				</tr>
				</table>
					  </section>
				<?	}
				}
			} */?>
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
  <script>
$(document).ready(function () {
       // window.//print();
    });
    window.onafterprint = function () {
      //  $('.printpage', window.parent.document).hide();
    }
  </script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); 
ob_end_flush();
?>