<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
//migrateinventoryebay(293548139731);
//getebayproduct($connectionapi,372807463310,3527);
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//recherche des produit lister
$DateNOW = date('Y-m-d');

if(!isset($_GET['order2']))$_GET['order2']='DESC';
if(!isset($_GET['order2']))$_GET['order2']='DESC';
if(isset($_GET['order3']))$_GET['order3']=' and (P.location like "%magasin%" or P.location like "H%")';
if(isset($_GET['verifier']))$_GET['order3']=' and remarque_interne !="" ';
if(isset($_GET['ebayerror']))$_GET['order3']=' and (P.unallocated_quantity+P.quantity)<>P.ebay_quantity ';
if(isset($_GET['ebayerrorprix'])){
	$_GET['order4']='(P.ebay_price<>P.price_with_shipping) and (P.unallocated_quantity+P.quantity)>0';
}else{
	$_GET['order4']='((P.quantity+P.unallocated_quantity)<>P.quantity)||((P.quantity+P.unallocated_quantity)<>P.ebay_quantity) ';
}



if(isset($_POST['verif_fait'])){
		foreach($_POST['verif_fait'] as $verif_fait)  
			{	
				$sql2 = 'UPDATE `oc_product` SET verif_fait="1" where product_id="'.$verif_fait.'"'; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
			}
}

if(isset($_POST['verif_prix'])){
		foreach($_POST['verif_prix'] as $verif_prix)  
			{	
				$price=0; 
				$verif_prixtmp=explode("@",$verif_prix);
				$verif_prixtmp[1]= number_format($verif_prixtmp[1], 2, '.', '');
				//echo $verif_prixtmp[1];
				if($verif_prixtmp[1]>9000){
					$price=9000;
				}elseif($verif_prixtmp[1]>5000){
					$price=5000;
				}
				$sql2 = 'UPDATE `oc_product` SET status=1,price="'.number_format($verif_prixtmp[1]-$price, 2, '.', '').'",price_with_shipping="'.number_format($verif_prixtmp[1]-$price, 2, '.', '').'",ebay_last_check="2020-09-01" where product_id="'.$verif_prixtmp[0].'"'; 
				$req2 = mysqli_query($db,$sql2); 
				
			//	echo $sql2.'<br><br>';
				
$sql3 = 'SELECT *
		FROM `oc_product` 
		where product_id='.$verif_prixtmp[0]; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
		// on envoie la requête
	//	echo $sql3;
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
$data3 = mysqli_fetch_assoc($req3);


				if($data3['UPS_com']==9999 && $data3['USPS_com']==9999){
					$frais_shipping=9999;
				}elseif($data3['USPS']>0 && ($data3['USPS']< $data3['UPS_com'])){
					$frais_shipping=$data3['USPS'];
					$carrier='USPS';
					$other=$data3['UPS_com'];
				}elseif($data3['USPS_com']>0 && ($data3['USPS_com']< $data3['UPS_com'])){
					$frais_shipping=$data3['USPS_com'];
					$carrier='USPS';
					$other=$data3['UPS_com'];
				}elseif($data3['USPS_com']>0 && $data3['UPS_com']< $data3['USPS_com']){
					$frais_shipping=$data3['UPS_com'];
					$carrier='UPS';
					$other=$data3['USPS_com'];
				}
				$sql2 = 'UPDATE `oc_product_special` SET price="'.number_format($verif_prixtmp[1]-$price-$frais_shipping, 2, '.', '').'" where product_id="'.$verif_prixtmp[0].'"'; 
			//	echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
				mise_en_page_description($connectionapi,$verif_prixtmp[0],$db);
				revise_ebay_product($connectionapi,$verif_prixtmp[2],$verif_prixtmp[0],$verif_prixtmp[3],$db,"oui");
				$price=0;
			}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
	    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
	<script type="text/javascript">
	$(function() {
		  $(document).ready(function () {
			
		   var todaysDate = new Date(); // Gets today's date
			
			// Max date attribute is in "YYYY-MM-DD".  Need to format today's date accordingly
			
			var year = todaysDate.getFullYear(); 						// YYYY
			var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2);	// MM
			var day = ("0" + todaysDate.getDate()).slice(-2);			// DD

			var minDate = (year +"-"+ month +"-"+ day); // Results in "YYYY-MM-DD" for today's date 
			
			// Now to set the max date value for the calendar to be today's date
			$('.departDate input').attr('min',minDate);
		 
			  });
	});
    function selectAll() {
        var items = document.getElementsByName('verif_fait[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('verif_fait[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="inventaireaverifier.php" method="post" enctype="multipart/form-data">
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<div class="form_description">
<?if(!isset($dir_name)){
	date_default_timezone_set("America/New_York"); 
	$pagenu=1;$itemcount=0;?>
<?
		if($pagenu&1){
			echo '<p class="single_record"> </p>';
			//echo "odd";
		}else{
			echo '<p class="single_record"> </p><br><p class="single_record"> </p>';
			//echo "even";
		}
		$pagenu=1;$itemcount=0;?>


<table border="1" width="100%">
	<tr>
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
		<th bgcolor="ff6251">
	

	</th>

	<th bgcolor="ff6251">
SKU
	</th>
		<th bgcolor="ff6251">
	Titre
	</th>

	<th bgcolor="ff6251">STOCK
	
	</th>
		<th bgcolor="ff6251">

	
	</th>
	<th bgcolor="ff6251">
	Ebay-ID
	
	</th>

		</th>
	<th bgcolor="ff6251">
	Prix Ebay
	
	</th>
		</th>
	<th bgcolor="ff6251">
	Prix Retail
	
	</th>
	</tr>

<?
		$sql = 'SELECT *,PD.name as nameen,PD1.name as namefr,P.quantity_anc,
		p.unallocated_quantity,P.location AS location_anc,
		P.quantity,P.location AS location_entrepot 
		FROM `oc_product` AS P 
		LEFT JOIN `oc_product_description` AS PD on P.product_id=PD.product_id 
		LEFT JOIN `oc_product_description` AS PD1 on (P.product_id=PD1.product_id AND PD1.language_id=2) 
		 
		
		where verif_fait is null and PD.language_id=1  and P.quantity >0 and P.price_with_shipping >5000 Order by P.price_with_shipping DESC '; //LIMIT 200 and P.ebay_id=292612778604';  $sql.'<br><br>';/*P.username = "marissa"*/
		// on envoie la requête
		//echo $sql;and P.price_with_shipping>499
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
	while($data = mysqli_fetch_assoc($req)){
		
		//if((($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity'])||($data['quantity']>$data['quantity_anc']&&$data['location_magasin']=="" || strpos($data['location_entrepot'],"agasin") ||strpos($data['location_entrepot'],"MAGASIN"))){
	?>
						<tr>
						<td <?if ($data['remarque_interne']!=""){?>style="background-color: red; color: white;"<?}?>>
								<input type="checkbox" name="verif_fait[]" value="<?echo $data['product_id'];?>"/><?echo '<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';?>

						</td>
						<td bgcolor="<?echo $bgcolor;?>">
<svg class="barcode"
	jsbarcode-value="<?echo (string)$data['sku'] ;?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>
<script>
JsBarcode(".barcode").init();
</script>
							
						</td>


						<td bgcolor="<?echo $bgcolor;?>">
						
			
						<a href="'.$GLOBALS['WEBSITE'].'/interne/modificationitem.php?product_id=<?echo $data['product_id'];?>&action=listing" target='listing' ><?echo $data['nameen'];?></a>
						</td>

						<td >
						<?if(($data['quantity']+$data['unallocated_quantity'])<>$data['quantity_anc']){?><input type="checkbox" name="majancientinventaire[]" value="<?echo $data['product_id'];?>"/><?}?>
						<?echo $data['quantity_anc'];?>

						</td>
			
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['location_magasin'];?>
						</td>
						
						<td bgcolor="<?echo $bgcolor;?>">
						<a href="https://www.ebay.com/sch/i.html?_nkw=<?echo $data['upc'];?>&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&_sop=15" target='ebay2' ><?echo $data['marketplace_item_id'];?></a>
						
						</td> 

						<td <?if($data['price_with_shipping']<9000){ echo 'style="background-color: red; color: white;"';}?>>
						<input type="checkbox" name="verif_prix[]" value="<?echo $data['product_id'];?>@<?echo $data['price_with_shipping'];?>@<?echo $data['marketplace_item_id'];?>@<?echo ($data['quantity']+$data['unallocated_quantity']);?>"/>
						<a href="'.$GLOBALS['WEBSITE'].'/interne/pretlister.php?product_id=<?echo $data['product_id'];?>" target='listing' ><?echo number_format($data['price_with_shipping'],2);?>	</a>	
						</td>
						<td>
						<a href="https://www.ebay.com/sch/i.html?_nkw=<?echo $data['nameen']?>&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&_sop=15" target='ebay2' ><?echo number_format($data['price'],2);?>	</a>	
						</td>
						</tr>
				
			<?
			$itemcount++;
	//	}			
		//$j++;
		//echo $j;
		
		
	}
		?>
</table>
<?
echo "NB a verifier:".$itemcount;
}
?>
		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
<?if ($_GET['imp']==""){?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interneusa.php" >Retour au MENU</a></h1>
		
<?}?>

</form>
</body>
</html>
<?  // on ferme la connexion à 

mysqli_close($db); ?>