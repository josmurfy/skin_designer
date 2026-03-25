<?



//echo (string)$_POST['sku'] ;



//$sku=(string)$_POST['sku'] ;



// on se connecte � MySQL 



include 'connection.php';



// on s�lectionne la base 



//mysqli_select_db('n7f9655_storeliquidation',$db); 



// on cr�e la requ�te SQL verifier les ordres 



// savoir ledernier id 



?>







<!DOCTYPE html>



<html>



<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>



  <meta charset="utf-8">



  <title>receipt</title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>



  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">



 <link rel="stylesheet" href="<?echo $GLOBALS['WEBSITE'];?>/interne/paper.css">



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



$rangees=array(1,2,3,4,5,6,7,8,9,10,11,12);



$sections=array(0,0,0,0,0,0,0,0,0,0,1,1);//3,3,3,3,3,4,4,5,1



$etages=array(18,24,24,24,24,18,18,18,12,13,15,15);//3,3,3,3,3,4,4,5,2



/* $rangees=array(6,7);



$sections=array(4,4); */



$lettres= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R");



		$z=0;



			foreach($rangees as $rangee) {



				



				$nbsection=$sections[$rangee-1];



				for ($j=0;$j<$nbsection;$j++){



					$lettre=$lettres[$j];



					



				



				



					for($i=1;$i<=$etages[$z];$i++){//.'&#x2B07



					



					?>



				<section class="sheet padding-0mm">



				<table class="center">



				<tr>



				<td class="center">



				<div  style="text-align: center;font-size: 65px; "><strong><?echo $rangee.$lettre.$i; ?></strong>|</div>



				</td>



				<td class="center">



								<div id="qrcode<?echo $rangee.$lettre.$i; ?>">									



					<script type="text/javascript">



				  var qrcode = new QRCode("qrcode<?echo $rangee.$lettre.$i; ?>", {



					text: "<?echo $rangee.$lettre.$i; ?>",



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



				<?	}



				/*	for($i=1;$i<=6;$i++){//.'&#x2B07



					



					?>



				<section class="sheet padding-0mm">



				<table class="center">



				<tr>



				<td class="center">



				<div  style="text-align: center;font-size: 65px; "><strong><?echo $rangee.$lettre.$i; ?>&#x2B07;</strong>|</div>



				</td>



				<td class="center">



								<div id="qrcode<?echo $rangee.$lettre.$i; ?>BAS">									



					<script type="text/javascript">



				  var qrcode = new QRCode("qrcode<?echo $rangee.$lettre.$i; ?>BAS", {



					text: "<?echo $rangee.$lettre.$i; ?>BAS",



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



				



				}



				$z++;



			}



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



								<div id="qrcode<?echo $rangee.$lettre.$i; ?>">									



					<script type="text/javascript">



				  var qrcode = new QRCode("qrcode<?echo $rangee.$lettre.$i; ?>", {



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



<? // on ferme la connexion � mysql mysqli_close($db); 



?>