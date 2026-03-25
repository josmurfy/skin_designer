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
if(!isset($_GET['order']))$_GET['order']='P.upc';
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

if(is_array($_POST['a_supprimer'])){
	foreach($_POST['a_supprimer'] as $a_supprimer)  
		{	
			//$itemvendu=explode(",", $vendu);
		
			$sql2 = 'UPDATE `oc_product` SET `unallocated_quantity`=0,`quantity`=0,`location`="",ebay_last_check="2020-09-01" where product_id='.$a_supprimer; 
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);  

			
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
 
 
where (P.quantity>0 || P.unallocated_quantity>0) AND PD.language_id=1 AND P.ebay_id=0 '.$_GET['order3'].' 
Order by '.$_GET['order'].' '.$_GET['order2'].' '; //and P.ebay_id=292612778604';  $sql.'<br><br>';
// on envoie la requête
//echo $sql;
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
        var items = document.getElementsByName('a_supprimer[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('a_supprimer[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="rapport_nonlister.php" method="post" >
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /><br>
<?//MAJ Quantité sur Ebay<input type="checkbox" name="process2" value="OUI"/> <br>
//MAJ PRIX sur Ebay<input type="checkbox" name="processprix" value="processprix"/><br>
//Check Qty Ebay<input type="checkbox" name="check_ebay" value="check_ebay"/>?>
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
	<a href="rapport_nonlister.php?order3=oui&order=P.location" >ENTREPOT</a> <a href="rapport_nonlister.php?verifier=oui&order=P.location" >A vérifier</a> <a href="rapport_nonlister.php?ebayerrorprix=oui&order=P.location" >Probleme de prix</a>
<table border="1" width="100%">
<th bgcolor="ff6251">
					
					<input type="button" onclick='selectAll()' value="Select All"/><br>
					<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
		</th>
		<th bgcolor="ff6251">
					
					IMAGE
		</th>
	<th bgcolor="ff6251">
		
	<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=P.sku" >SKU</a>
	</th>
		<th bgcolor="ff6251">
		<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=PD.name" >Titre</a>
	</th>
	
	<th bgcolor="ff6251">
	<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >Qty Total</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=P.location" >Entrepot</a>
	
	</th>
	<th bgcolor="ff6251">
	<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=P.quantity" >Qty</a>
	
	</th>
	
	<th bgcolor="ff6251">
	<a href="rapport_nonlister.php?order2=<?echo $_GET['order2'];?>&order=P.unallocated_quantity" >A Placer</a>
	
	</th>
	
	<th bgcolor="ff6251">
	<a href="rapport_nonlister.php?product_id=<?echo $_GET['product_id'];?>&order=P.product_id" >ProductID</a>
	
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
						<td bgcolor="<?echo $bgcolor;?>">
					<input type="checkbox" name="a_supprimer[]" value="<?echo $data['product_id'];?>"/>
					</td>
						<td <?if ($data['remarque_interne']!=""){?>style="background-color: red; color: white;"<?}?>>
								<?echo '<img height="100" src="'.$GLOBALS['WEBSITE'].'/image/'.$data['image'].'"/>';?>
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
						
                        <a href="listing.php?sku=<?echo $data['product_id'];?>" target="listing" ><?echo $data['product_id'];?></a>
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

/interne/phoenixsupplies//rapport_nonlister.php" >Orders Business</a></h1>
		<h1><a href="'.$GLOBALS['WEBSITE'].'/interne/updateordersite.php" >Orders sur SITE</a></h1> 
<?}?>

</form>
</body>
</html>
<?  // on ferme la connexion à 

/**/
mysqli_close($db); ?>