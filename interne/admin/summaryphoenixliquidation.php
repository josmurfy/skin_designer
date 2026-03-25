<?


//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 

$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
include_once 'functionload.php';

// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',"",$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
$transaction_id="";
//print_r($_FILES['file_import']);
if(!isset($i)){
	$i=2021;
}
if(isset($_GET['tout'])){
	$_POST['tout']=$_GET['tout'];
}elseif(isset($_POST['tout'])){
	$_POST['tout']=$_POST['tout'];
}else{
	$_POST['tout']="";
}
$sql2 = 'SELECT * FROM `admin_currency_conversion` WHERE currency = "CAD" AND rate!=0 order by date_transaction';
$req2 = mysqli_query($db,$sql2); 
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="../stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="summary.php" method="post" enctype="multipart/form-data">
<div class="form_description">
<?/* <h1>Sommaire par année</h1>
                  <select name="year" id="input-year" class="form-control">
                    <option value="2018" <?php if ($i == 2018) { ?>selected="selected"<?}?>>2018</option>
                    <option value="2019" <?php if ($i == 2019) { ?>selected="selected"<?}?>>2019</option>
                    <option value="2020" <?php if ($i == 2020) { ?>selected="selected"<?}?>>2020</option>
                    <option value="2021" <?php if ($i == 2021) { ?>selected="selected"<?}?>>2021</option> 
                 </select> */?>
ANNÉE <?echo $i;
$i=11;?>
<br><br>VENTES PhoenixSupplies
<div class="divTable blueTable">
	<div class="divTableHeading">
		<div class="divTableRow">
			<div class="divTableHead">Mois</div>
<?if($_POST['tout']=="oui"){?>
			<div class="divTableHead">Ebay</div>
			<div class="divTableHead">Bonanza</div>
			<div class="divTableHead">Invoice US</div>
			<div class="divTableHead">Invoice CA</div>
			<div class="divTableHead">MAGASIN US</div>
 <?/*			<div class="divTableHead">PAYPAL Magasin US</div>
 */	?>			<div class="divTableHead">MAGASIN CA</div>
<?}?>
 <?/*			<div class="divTableHead">PAYPAL Magasin CA</div>
 */	?>		<div class="divTableHead">TOTAL</div>
 </div>
	</div>
	<div class="divTableBody">
			  <?$a=$b=$c=$d=$e=0;
				$yearnow=date("Y");
				  $monthnow=date("m");
				   $monthnow= $monthnow+0;
				  $init=0;
				  $stop=0;
				  for ($i=2018;$i<$yearnow+1;$i++){
					  $select=strval("11-".($i-1));
					  if($init==0){?>

							<div class="divTableRow">
							<div class="divTableCell">
							<a href="https://phoenixliquidation.ca/interne/admin/monthly.php?month=11&year=<?echo $i-1;?>&tout=" target='listing' >Novembre (<?echo $i-1;?>)</a>		
							</div>
<?if($_POST['tout']=="oui"){?>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"ebay_sales","currency='USD' AND ",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"bonanza_sales","currency='USD' AND ",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"invoice_sales","currency='USD' AND ",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"invoice_sales","currency='CAD' AND ",$db),2);?></div>
 							<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i-1,$i-1,11,11,"currency_code='USD' AND ",$db),2);?></div>
<?/*						<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"website_sales","currency='USD' AND ",$db),2);?></div>
 */	?>							<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i-1,$i-1,11,11,"currency_code='CAD' AND ",$db),2);?></div>
<?/* 						<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"website_sales","currency='CAD' AND ",$db),2);?></div>
 */	?>
<?}?>
  							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,11,11,"ebay_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,11,11,"bonanza_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,11,11,"invoice_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,11,11,"invoice_sales","currency='CAD' AND ",$db)+
																				get_website_sum($i-1,$i-1,11,11,"currency_code='USD' AND ",$db)+
																				get_website_sum($i-1,$i-1,11,11,"currency_code='CAD' AND ",$db),2);
							?></div>
							</div>
<?$a=$b=$c=$d=$e=0;?>
							<div class="divTableRow">
							<div class="divTableCell">
							<a href="https://phoenixliquidation.ca/interne/admin/monthly.php?month=12&year=<?echo $i-1;?>&tout=" target='listing' >Decembre (<?echo $i-1;?>)</a>		
							</div>
<?if($_POST['tout']=="oui"){?>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"ebay_sales","",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"bonanza_sales","",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"invoice_sales","currency='USD' AND ",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"invoice_sales","currency='CAD' AND ",$db),2);?></div>
							<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i-1,$i-1,12,12,"currency_code='USD' AND ",$db),2);?></div>
<?/* 						<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"website_sales","currency='USD' AND ",$db),2);?></div>
 */	?>							<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i-1,$i-1,12,12,"currency_code='CAD' AND ",$db),2);?></div>
 <?/*						<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"website_sales","currency='CAD' AND ",$db),2);?></div>
 */	?>
<?}?>
   							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i-1,$i-1,12,12,"ebay_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,12,12,"bonanza_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,12,12,"invoice_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i-1,$i-1,12,12,"invoice_sales","currency='CAD' AND ",$db)+
																				get_website_sum($i-1,$i-1,12,12,"currency_code='USD' AND ",$db)+
																				get_website_sum($i-1,$i-1,12,12,"currency_code='CAD' AND ",$db),2);
							?></div>
							</div>

<?
$a=$b=$c=$d=$e=0;
}
$a=$b=$c=$d=$e=0;
					  $sql2 = 'SELECT * FROM `admin_month`';
							$req2 = mysqli_query($db,$sql2); 
					while ($data2 = mysqli_fetch_assoc($req2)){
						if($stop<1){
						if($i.$data2['id']!=$yearnow.$monthnow){?>
								<div class="divTableRow">
								<div class="divTableCell">
								<a href="https://phoenixliquidation.ca/interne/admin/monthly.php?month=<?echo $data2['id']?>&year=<?echo $i;?>&tout=" target='listing' ><?echo $data2['name'];?> (<?echo $i;?>)</a>		
							</div>
<?if($_POST['tout']=="oui"){?>
								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"ebay_sales","",$db),2);?></div>
								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"bonanza_sales","",$db),2);?></div>
								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"invoice_sales","currency='USD' AND ",$db),2);?></div>
								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"invoice_sales","currency='CAD' AND ",$db),2);?></div>
								<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i,$i,$data2['id'],$data2['id'],"currency_code='USD' AND ",$db),2);?></div>
 <?/*								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"website_sales","currency='USD' AND ",$db),2);?></div>
 */	?>								<div class="divTableCell"><?echo "$ ".number_format(get_website_sum($i,$i,$data2['id'],$data2['id'],"currency_code='CAD' AND ",$db),2);?></div>									
 <?/*								<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"website_sales","currency='CAD' AND ",$db),2);?></div>
 */	?>		
<?}?> 
  							<div class="divTableCell"><?echo "$ ".number_format(get_sum_cad($i,$i,$data2['id'],$data2['id'],"ebay_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i,$i,$data2['id'],$data2['id'],"bonanza_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i,$i,$data2['id'],$data2['id'],"invoice_sales","currency='USD' AND ",$db)+
																				get_sum_cad($i,$i,$data2['id'],$data2['id'],"invoice_sales","currency='CAD' AND ",$db)+
																				get_website_sum($i,$i,$data2['id'],$data2['id'],"currency_code='USD' AND ",$db)+
																				get_website_sum($i,$i,$data2['id'],$data2['id'],"currency_code='CAD' AND ",$db),2);
							?></div>
							</div>					
						<?}else{?>
					<? $stop=1;	
					}}
					$a=$b=$c=$d=$e=0;
				 }
				  $init=1;}?>

</div>
</div>
</div>



</body>
</html>
<?  // on ferme la connexion à mysql 
mysqli_close($db); ?>


