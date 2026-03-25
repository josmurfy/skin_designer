<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");

// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//recherche des produit lister
$DateNOW = date('Y-m-d');
if(!isset($_GET['order']))$_GET['order']='P.location';
if(!isset($_GET['order2']))$_GET['order2']='DESC';
if(!isset($_GET['order2']))$_GET['order2']='DESC';
if(isset($_GET['order3']))$_GET['order3']=' and (P.location like "%magasin%" or P.location like "H%")';
if(isset($_GET['verifier']))$_GET['order3']=' and remarque_interne !="" ';
if(isset($_GET['ebayerror']))$_GET['order3']=' and (P.unallocated_quantity+P.quantity)<>P.ebay_quantity ';
/* if(isset($_POST['process'])){
		$nbmaj=0;
		$sql = 'SELECT *,P.sku AS psku,P.quantity_anc,p.unallocated_quantity,P.location AS location_anc,P.quantity,P.location AS location_entrepot FROM `oc_product` AS P,`oc_product_description` AS PD  where P.product_id=PD.product_id    AND P.ebay_id >0 and PD.language_id=1 ';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
					if(($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity']){						
						$updquantity=$data['unallocated_quantity']+$data['quantity'];
						
						echo 'SKU:'.$data['psku'].'<br>Nouveau Prix:'.$data['price_with_shipping'].'<br>Ancien Prix:'.$data['ebay_price'].'<br>Quantité:'.$updquantity.'<br>Nu Ebay:'.$data['marketplace_item_id'].'<br>UPC:'.(string)$data['upc'].'<br>';
						$data['ebay_quantity']=updatetoebay($data['price_with_shipping'],$updquantity,$data['marketplace_item_id'],(string)$data['upc']);
						$nbmaj++;
						$sql2 = 'UPDATE `oc_product`SET ebay_date_modified="'.$DateNOW.'",ebay_price="'.$data['price_with_shipping'].'",ebay_quantity="'.$data['ebay_quantity'].'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
					    //echo '<br>'.$sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);
					}
			}
	echo '<span style="color:GREEN;"><strong>Nb ITEM MAJ effectuer:'.$nbmaj.'</strong><br><br>';
} */
if(isset($_POST['maj'])){
		foreach($_POST['maj'] as $maj)  
			{	
				//$itemmaj=explode(",", $maj);
				//$itemmaj=explode(",", $maj);
				$sql = 'SELECT * FROM `oc_product` WHERE product_id="'.$maj.'"';
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req); 
				$sql2 = 'UPDATE `oc_product` SET quantity="'.$data['quantity'].'" where product_id="'.$maj.'"'; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
			}
				//$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);
}
if(isset($_POST['majentrepot'])){
		foreach($_POST['majentrepot'] as $majentrepot)  
			{	

				$sql = 'SELECT * FROM `oc_product` WHERE product_id="'.$majentrepot.'"';
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req); 
				$sql2 = 'UPDATE `oc_product` SET quantity="0",location="" where product_id="'.$majentrepot.'"'; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
			}
				//$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);
}
if(isset($_POST['majancientinventaire'])){
		foreach($_POST['majancientinventaire'] as $majancientinventaire)  
			{	
				$sql = 'SELECT P.quantity,P.unallocated_quantity AS unallocated_quantity FROM `oc_product` AS P WHERE  P.product_id="'.$majancientinventaire.'"';
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
				//echo $sql.'<br><br>';
				$updquantity=$data['unallocated_quantity']+$data['quantity'];
				$sql2 = 'UPDATE `oc_product` SET location="659",quantity="'.$updquantity.'",ebay_last_check="2020-09-01" where product_id="'.$majancientinventaire.'"'; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
			}
				//$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);
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
        var items = document.getElementsByName('maj[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('maj[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
<style> 
input[type=text] {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
textarea  {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}

select {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
select:focus {
    border: 3px solid #555;
}

input[type=text]:focus {
    border: 3px solid #555;
}
textarea:focus {
    border: 3px solid #555;
}
.fsSubmitButton
{
	border-top:		2px solid #a3ceda;
	border-left:		2px solid #a3ceda;
	border-right:		2px solid #4f6267;
	border-bottom:		2px solid #4f6267;
	height:			200px;
	width:			400px;
	padding:		10px 20px !important;
	font-size:		25px !important;
	background-color:	#ffffff;
	font-weight:		bold;
	color:			#000000;
}
</style>

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="inventairesyncinventaire.php" method="post" enctype="multipart/form-data">
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

<h3><label class="description" for="categorie">INVENTAIRE Validation: <?if ($_GET['imp']=="oui"){ echo "(Page: ".$pagenu.")"; $pagenu++;}?></label></h3>
	
<table border="1" width="100%">
	<tr>
		<th bgcolor="ff6251">
	

	</th>

	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.sku" >SKU</a>
	</th>
		<th bgcolor="ff6251">
		<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=PD.name" >Titre</a>
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.location" >ANC Location</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >STOCK</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.location" >ENTREPOT</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >STOCK</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.location" >ENTREPOT</a>
	 
	</th>
	<th bgcolor="ff6251">
	<a href="inventairesyncinventaire.php?order2=<?echo $_GET['order2'];?>&order=P.unallocated_quantity" >STOCK</a>
	
	</th>
	</tr>

<?
		$sql = 'SELECT *,P.quantity_anc,p.unallocated_quantity,P.location AS location_anc,P.quantity,P.location AS location_entrepot FROM `oc_product` AS P,`oc_product_description` AS PD  where P.product_id=PD.product_id    AND P.ebay_id =0 and PD.language_id=1 '.$_GET['order3'].' Order by '.$_GET['order'].' '.$_GET['order2'].''; //and P.ebay_id=292612778604'; 
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
	while($data = mysqli_fetch_assoc($req)){
/* 				$dateebay = new DateTime($data['ebay_date_modified']);
				$dateebay = $dateebay->format('Y-m-d');
				$dateebay=date_parse ($dateebay);
				$dateverification = new DateTime('now');
				$dateverification->modify('-1 day'); // or you can use '-90 day' for deduct
				$dateverification = $dateverification->format('Y-m-d');
				$dateverification=date_parse ($dateverification);
				if($dateverification > $dateebay){
					list ($data['ebay_quantity'], $data['ebay_price']) =getebayproduct($connectionapi,$data['marketplace_item_id']);
					$sql2 = 'UPDATE `oc_product`SET ebay_date_modified="'.$DateNOW.'",ebay_price="'.$data['ebay_price'].'",ebay_quantity="'.$data['ebay_quantity'].'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);

				} */

		if((($data['quantity']+$data['unallocated_quantity'])<>$data['quantity_anc'])){
	?>
						<tr>
						<td bgcolor="<?echo $bgcolor;?>">
								<?echo '<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';?>
								
						</td>
						<td bgcolor="<?echo $bgcolor;?>">

							<?echo $data['sku'];?>
						</td>


						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['name'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['location_anc'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?if(($data['quantity']+$data['unallocated_quantity'])>$data['quantity_anc']){?><input type="checkbox" name="majancientinventaire[]" value="<?echo $data['product_id'];?>"/><span style="color:FF0000;"><strong><?}?><?echo $data['quantity_anc'];?><?if(($data['quantity']+$data['unallocated_quantity'])<>$data['quantity_anc']){?></strong><?}?>

						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['location_entrepot'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?if($data['quantity']>$data['quantity_anc']&&$data['location_magasin']==""){?><input type="checkbox" name="maj[]" value="<?echo $data['product_id'];?>"/><span style="color:FF0000;"><strong><?}?><?echo $data['quantity'];?><?if($data['quantity']>$data['quantity_anc']&&$data['location_magasin']==""){?></strong><?}?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['location_magasin'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?if((($data['quantity']+$data['unallocated_quantity'])>$data['quantity_anc'])&&$data['location_magasin']!=""){?><input type="checkbox" name="majentrepot[]" value="<?echo $data['product_id'];?>"/><span style="color:FF0000;"><strong><?}?><?echo $data['unallocated_quantity'];?><?if((($data['quantity']+$data['unallocated_quantity'])>$data['quantity_anc'])&&$data['location_magasin']!=""){?></strong><?}?>
						
						</td>
						</tr>
				
			<?
			$itemcount++;
		}			
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