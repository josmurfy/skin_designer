<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ; 
// on se connecte à MySQL 
include 'connection.php';
//getebayproduct($connectionapi,372807463310,3527);
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//recherche des produit lister
$DateNOW = date('Y-m-d');
if(!isset($_GET['order']))$_GET['order']='P.location';
if(!isset($_GET['order2']))$_GET['order2']='ASC';
//if(!isset($_GET['order2']))$_GET['order2']='DESC';
if(isset($_GET['order3']))$_GET['order3']=' and (P.location like "%magasin%" or P.location like "H%")';
if(isset($_GET['verifier']))$_GET['order3']=' and remarque_interne !="" ';
if(isset($_GET['ebayerror']))$_GET['order3']=' and (P.unallocated_quantity+P.quantity)<>P.ebay_quantity ';
if(isset($_GET['ebayerrorprix'])){
	$_GET['order4']='(P.ebay_price<>P.price_with_shipping) and (P.unallocated_quantity+P.quantity)>0';
}else{
	$_GET['order4']='((P.quantity+P.unallocated_quantity)<>P.quantity)||((P.quantity+P.unallocated_quantity)<>P.ebay_quantity) ';
}
//echo "allo";
//echo $_POST['process2'];
//echo "allo";

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
if(isset($_POST['process2'])){
		$nbmaj=0;
		$sql = 'SELECT *,P.sku AS psku,P.quantity_anc,p.unallocated_quantity,P.location AS location_anc,P.quantity,P.location AS location_entrepot FROM `oc_product` AS P,`oc_product_description` AS PD  where P.product_id=PD.product_id    AND P.ebay_id >"0" and PD.language_id=1 ';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
					if(($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity']){						
						$updquantity=$data['unallocated_quantity']+$data['quantity'];
						
						echo 'SKU:'.$data['psku'].'<br>Nouveau Prix:'.$data['price_with_shipping'].'<br>Ancien Prix:'.$data['ebay_price'].'<br>Quantité:'.$updquantity.'<br>Nu Ebay:'.$data['marketplace_item_id'].'<br>UPC:'.(string)$data['upc'].'<br>';
						revise_ebay_product($connectionapi,$data['marketplace_item_id'],$data['product_id'],$updquantity,$db,"non");
						$nbmaj++;
						$sql2 = 'UPDATE `oc_product`SET ebay_date_modified=now(),ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$data['product_id'];
					    echo '<br>'.$sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);
					}
			}
	echo '<span style="color:GREEN;"><strong>Nb ITEM MAJ effectuer:'.$nbmaj.'</strong><br><br>';
}	
if(isset($_POST['processprix'])){
		$nbmaj=0;
		$sql = 'SELECT *,P.sku AS psku,P.quantity_anc,p.unallocated_quantity,P.location AS location_anc,P.quantity,P.location AS location_entrepot FROM `oc_product` AS P,`oc_product_description` AS PD  where P.product_id=PD.product_id    AND (P.ebay_price<>P.price_with_shipping) AND P.ebay_id >"0" and PD.language_id=1 ';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
				
						
						echo 'SKU:'.$data['psku'].'<br>Nouveau Prix:'.$data['price_with_shipping'].'<br>Ancien Prix:'.$data['ebay_price'].'<br>Quantité:'.$updquantity.'<br>Nu Ebay:'.$data['marketplace_item_id'].'<br>UPC:'.(string)$data['upc'].'<br>';
						update_to_ebay($connectionapi,$data['price_with_shipping'],"non",$data['marketplace_item_id'],$data['product_id']);
						$nbmaj++;
						$sql2 = 'UPDATE `oc_product`SET ebay_date_modified="'.$DateNOW.'",ebay_price="'.$data['price_with_shipping'].'",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$data['product_id'];
					    //echo '<br>'.$sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);

			}
	echo '<span style="color:GREEN;"><strong>Nb ITEM PRIX MAJ effectuer:'.$nbmaj.'</strong><br><br>';
}	
if(isset($_POST['check_ebay'])){
	
			
					$dateverification = new DateTime('now');
					$dateverification->modify('-5 day'); // or you can use '-90 day' for deduct
					$dateverification = $dateverification->format('Y-m-d');
				
				//$dateverification=date_parse ($dateverification);
				$sql = 'SELECT count(*) nbitem FROM `oc_product`  
where ebay_id >0 and ebay_last_check < "'.$dateverification.'"';
//Order by '.$_GET['order'].' '.$_GET['order2'].' '; //and P.ebay_id=292612778604';  $sql.'<br><br>';
// on envoie la requête
//echo $sql;
$req = mysqli_query($db,$sql);
//while($data = mysqli_fetch_assoc($req)){
	$data = mysqli_fetch_assoc($req);
//	//print("<pre>".print_r ($data,true )."</pre>");
	$nbitem= $data['nbitem'];
	$j=20;
	//$j=20;
	$nbitem=1;
	for($i=0; $i<=$nbitem-1;$i+=20){// and P.ebay_last_check < "'.$dateverification.'" 
			$sql = 'SELECT P.ebay_id 
					FROM `oc_product` AS P 
					 
					where P.ebay_last_check < "'.$dateverification.'" and P.ebay_id >"0" 
					Order by P.location ASC
					limit 0,20 ';
					
		//Order by '.$_GET['order'].' '.$_GET['order2'].' '; //and P.ebay_id=292612778604';  $sql.'<br><br>';
		// on envoie la requête
		echo $sql."<br>";
	//	echo $i."<br>";
		//$j=$j+20;
		$req = mysqli_query($db,$sql);

//while($data = mysqli_fetch_assoc($req)){
		unset($ebay_ids);
		$ebay_ids = array();
		$z=0;
		while($data = mysqli_fetch_assoc($req)){
			$ebay_ids[$z]=$data['marketplace_item_id'];
			$z++;
		}
	echo "Nb a verifier:".count($ebay_ids);
		if(count($ebay_ids)>0){
			$result=get_ebay_multiple_products($connectionapi,$ebay_ids,0);
			$json = json_decode($result, true);
			//print("<pre>".print_r ($json,true )."</pre>");

			if($json['Ack']=='PartialFailure'){
			
				$ebay_id_received= explode(",",$json['Errors']['ErrorParameters']['Value']);
				

				foreach($ebay_id_received as $ebay_id){
				
					if($ebay_id!=""){
						echo "Plus dans ebay:";
						$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price=0,ebay_quantity=0,ebay_id=0,error_ebay="'.$json['Errors']['LongMessage'].': '.$ebay_id.'",ebay_last_check="2020-09-01" WHERE ebay_id="'.$ebay_id.'"';
						echo $sql2."<br>";
						$req2 = mysqli_query($db,$sql2);
					}
				}
				
			}
			if(!isset($json['Item'][0])){
				$itemtmp=$json['Item'];
				unset($json['Item']);
				$json['Item'][0]=$itemtmp;
				//
			}
		//	//print("<pre>".print_r ($json,true )."</pre>");
			foreach($json['Item'] as $item){
				if($item['ItemID']!=""){
					$qty_restant= $item['Quantity']-$item['QuantitySold'];
					$endtime=explode("T",$item['EndTime']);
					//$endtime=explode("-",$endtime[0]);
					//$endtime_ver=$endtime[2]."/".$endtime[1]."/".$endtime[0];
					//echo $endtime[0]."<br>";
					//$endtime[0]=strtr($endtime[0], '-', '.');
					//echo $endtime[0]."<br>";
					$endtime_ver=date("Y-m-d", strtotime($endtime[0]));
					echo $endtime_ver."<br>";
					//echo $endtime[2]."/".$endtime[1]."/".$endtime[0];
					//$endtime=date('d/m/Y',$endtime[2]."/".$endtime[1]."/".$endtime[0]);
					//echo $endtime."<br>";
					//$date_convert = date_format($endtime_ver, "m/d/Y");
					//echo $date_convert."<br>";
				
					$today=date("Y-m-d");
					echo $today."<br>";
					if($today>$endtime_ver){
						echo "oui";
					//	$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price="'.$item['CurrentPrice'].'",ebay_quantity="'.$qty_restant.'" WHERE `oc_product`.`ebay_id` ="'.$item['ItemID'].'"';
						$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price=0,ebay_quantity=0,ebay_id=0,error_ebay="Plus dans ebay: '.$item['ItemID'].'" WHERE ebay_id="'.$item['ItemID'].'"';
					}else{
						$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price="'.$item['CurrentPrice'].'",ebay_quantity="'.$qty_restant.'" WHERE `oc_product`.`ebay_id` ="'.$item['ItemID'].'"';
					}
			//		$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price="'.$item['CurrentPrice'].'",ebay_quantity="'.$qty_restant.'" WHERE `oc_product`.`ebay_id` ="'.$item['ItemID'].'"';
					echo $sql2."<br>";
					$req2 = mysqli_query($db,$sql2);
				}
			}
					/*if($dateverification >= $dateebay){
						
						
						$json = json_decode($result, true);
						$ebay_quantity=$json["Item"]["Quantity"];
						$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
						$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
						$data['ebay_quantity']=$ebay_quantity-$Quantity_sold;
						$data['ebay_price']=$ebay_price;
						if($json['Item']['ItemID']==""){
							$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price=0,ebay_quantity=0,ebay_id=0,error_ebay='".$json['ERROR']."' WHERE `oc_product`.`product_id` ='.$data['product_id'];
						}else{
							$sql2 = 'UPDATE `oc_product`SET ebay_last_check="'.$DateNOW.'",ebay_price="'.$data['ebay_price'].'",ebay_quantity="'.$data['ebay_quantity'].'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
						}
						//echo $sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);

					}*/
				}
				
		}
}

if(!isset($dateverification)){
	$dateverification = new DateTime('now');
	$dateverification->modify('-5 day'); // or you can use '-90 day' for deduct
	$dateverification = $dateverification->format('Y-m-d');
	$dateverification_slq=' OR (P.ebay_last_check < "'.$dateverification.'" and P.ebay_id >"0") ';
	
}else{
	$dateverification = new DateTime('now');
	$dateverification = $dateverification->format('Y-m-d');
	$dateverification_slq=' OR (P.ebay_last_check = "'.$dateverification.'" and P.ebay_id >"0") ';
}
	

$sql = 'SELECT *,P.image as image,PD.name as name ,P.quantity_anc,p.unallocated_quantity,P.location 
AS location_anc,P.quantity,P.location 
AS location_entrepot FROM `oc_product` AS P 
LEFT JOIN `oc_product_description` AS PD on P.product_id=PD.product_id 
 
 
LEFT JOIN oc_product_to_category AS PC on PC.product_id=P.product_id 
LEFT JOIN oc_category AS C on (C.category_id=PC.category_id )
LEFT JOIN oc_category_description AS CD on (C.category_id=CD.category_id and CD.language_id=1 )
where P.quantity>0 and P.ebay_id >"0" and (C.category_id=139973) and CD.ebayyes=1 and PD.language_id=1'.$_GET['order3'].' 
Order by '.$_GET['order'].' '.$_GET['order2'].' '; //and P.ebay_id=292612778604';  $sql.'<br><br>';
// on envoie la requête
echo $sql;
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement 
// merge quantity
$itemcount=0;
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
	    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
	<script type="text/javascript">

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
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="rapport_mediamail.php" method="post" >
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /><br>
MAJ Quantité sur Ebay<input type="checkbox" name="process2" value="OUI"/> <br>
MAJ PRIX sur Ebay<input type="checkbox" name="processprix" value="processprix"/><br>
Check Qty Ebay<input type="checkbox" name="check_ebay" value="check_ebay"/>
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

<h3><label class="description" for="categorie">INVENTAIRE Declaré sur ebay: <?if ($_GET['imp']=="oui"){ echo "(Page: ".$pagenu.")"; $pagenu++;}?></label></h3>
	<a href="rapport_mediamail.php?order3=oui&order=P.location" >ENTREPOT</a> <a href="rapport_mediamail.php?verifier=oui&order=P.location" >A vérifier</a> <a href="rapport_mediamail.php?ebayerrorprix=oui&order=P.location" >Probleme de prix</a>
<table border="1" width="100%">
	<tr>
		<th bgcolor="ff6251">
	

	</th>

	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.sku" >SKU</a>
	</th>
		<th bgcolor="ff6251">
		<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=PD.name" >Titre</a>
	</th>
	
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >Qty Total</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.location" >Entrepot</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >Qty</a>
	
	</th>
	
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.unallocated_quantity" >A Placer</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.ebay_id" >Ebay-ID</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?ebayerror=<?echo $_GET['ebayerror'];?>&order=P.ebay_quantity" >Qty eBay</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_mediamail.php?order2=<?echo $_GET['order2'];?>&order=P.ebay_last_check" >Dernier MAJ</a>

	
	</th>
		</th>
	<th bgcolor="ff6251">
	Prix sur Ebay
	
	</th>
		</th>
	<th bgcolor="ff6251">
	Prix Ebay BD
	
	</th>
	</tr>

<?

	while($data = mysqli_fetch_assoc($req)){
		$dateebay = new DateTime($data['ebay_last_check']);
		$dateebay = $dateebay->format('Y-m-d');
		$dateebay=date_parse ($dateebay);
		$dateverification = new DateTime('now');
		$dateverification->modify('-1 day'); // or you can use '-90 day' for deduct
		$dateverification = $dateverification->format('Y-m-d');
		$dateverification=date_parse ($dateverification);
		

		//if((($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity'])||($data['quantity']>$data['quantity_anc']&&$data['location_magasin']=="" || strpos($data['location_entrepot'],"agasin") ||strpos($data['location_entrepot'],"MAGASIN"))){
	?>
						<tr>
						<td <?if ($data['remarque_interne']!=""){?>style="background-color: red; color: white;"<?}?>>
								<?echo '<img height="50" src="'.$GLOBALS['WEBSITE'].'/image/'.$data['image'].'"/>';?>
		<?if ($data['remarque_interne']!=""){?>
								<br>
								<a href="modificationitem.php?product_id=<?echo $data['product_id'];?>" >A vérifier</a>
		<?}?>
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
						<?echo $data['name'];?>
						</td>
						
						<td <?if(($data['quantity']+$data['unallocated_quantity'])<>$data['quantity_anc']){?>style="background-color: red; color: white;"<?}?>>
						<?if(($data['quantity']+$data['unallocated_quantity'])<>$data['quantity_anc']){?><input type="checkbox" name="majancientinventaire[]" value="<?echo $data['product_id'];?>"/><?}?>
						<?echo $data['quantity_anc'];?>

						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['location_entrepot'];?>
						</td>
						<td <?if($data['quantity']>$data['quantity_anc']&&$data['location_magasin']==""){?>style="background-color: yellow;"<?}elseif(strpos($data['location_entrepot'],"agasin")){?>style="background-color: red; color: white;"<?}?>>
						<?if($data['quantity']>$data['quantity_anc']&&$data['location_magasin']=="" || strpos($data['location_entrepot'],"agasin")){?><input type="checkbox" name="maj[]" value="<?echo $data['product_id'];?>"/><?}?>
						<?echo $data['quantity'];?>
						</td>
						<?
						$pos=strpos($data['location_entrepot'],"agasin");
						$pos2=strpos($data['location_entrepot'],"MAGASIN");
						?>
						<td <?if(($pos!==false || $pos2!==false) &&($data['unallocated_quantity']>0)){?>style="background-color: red; color: white;"<?}?>>
						<?if(($pos!==false || $pos2!==false) &&($data['unallocated_quantity']>0)){?>
						<input type="checkbox" name="majentrepot[]" value="<?echo $data['product_id'];?>"/><?}?>
						
						<?echo $data['unallocated_quantity'];?>
						
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $data['marketplace_item_id'];?>
						</td>
						<td <?if(($data['quantity']+$data['unallocated_quantity'])<>$data['ebay_quantity']){?>style="background-color: red; color: white;"<?}?>>
						<?echo $data['ebay_quantity'];?>		
						</td>
						<td <?if($dateverification >= $dateebay){?>style="background-color: red; color: white;"<?}?>>
						<?echo $data['ebay_last_check'];?>		
						</td>
						<td>
						<?echo number_format($data['price_with_shipping'],2);?>		
						</td>
						<td <?if($data['ebay_price']<>$data['price_with_shipping']){?>style="background-color: red; color: white;"<?}?>>
						<?echo number_format($data['ebay_price'],2);?>		
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
		<h1><a href="<?echo $GLOBALS['WEBSITE'];?>

/interne/phoenixsupplies//rapport_mediamail.php" >Orders Business</a></h1>
		<h1><a href="'.$GLOBALS['WEBSITE'].'/interne/updateordersite.php" >Orders sur SITE</a></h1> 
<?}?>

</form>
</body>
</html>
<?  // on ferme la connexion à 

/**/
mysqli_close($db); ?>