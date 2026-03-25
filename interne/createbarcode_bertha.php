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



$rangees=array('Z');



$sections=array(5,5,7);//3,3,3,3,3,4,4,5,4



//$etages=array('A','B','C','D','E');//3,3,3,3,3,4,4,5,2
$etages=array(4,4,7);


/* $rangees=array(6,0);



$sections=array(4,4); */



$lettres= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R");



		$z=0;



			foreach($rangees as $rangee) {

				$nbsection=$sections[$z+1];
            //    echo $rangee."ran<br>";
				for($qe=1;$qe<=$nbsection;$qe++){
                //    echo $etage."eta<br>";
                    if($sections[$z]>0){
                   //     echo $q."col<br>";
                        for($q=1;$q<=$etages[$z];$q++){//.'&#x2B07
                     //       echo $q."col<br>";
                     
					?>



				<section class="sheet padding-0mm">



				<table class="center">



				<tr>



				<td class="center">



				<div  style="text-align: center;font-size: 50px; "><strong><?echo $rangee.$q."-".$lettres[$qe-1]."" ?></strong>|</div>



				</td>



				<td class="center">



								<div id="qrcode<?echo $rangee.$q."-".$lettres[$qe-1]."" ?>">									



					<script type="text/javascript">



				  var qrcode = new QRCode("qrcode<?echo $rangee.$q."-".$lettres[$qe-1]."" ?>", {



					text: "<?echo $rangee.$q."-".$lettres[$qe-1]."" ?>",



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