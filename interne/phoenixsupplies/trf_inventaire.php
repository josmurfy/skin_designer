<? 
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache"); 

// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

if (($_POST['quantitytrf']>0 || $_POST['qtyadd'] >0 )&& $_POST['new']==1){
	if($_POST['quantitytrf']==0 && $_POST['qtyadd']==0)
	{
		$erreurquantitysold='<strong><font color="red">***ENTREZ LA QTY A transferer!</font></strong>';
		$new=1;
	}else{
		//$qtysold=$_POST['quantity']-$_POST['quantitytrf'];
			$sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity-'.$_POST['quantitytrf'].' where product_id='.$_POST['product_id'];
			$req2 = mysqli_query($db,$sql2);
			$quantityadd=$_POST['quantitytrf']+$_POST['qtyadd'];
			$sql2 = 'UPDATE `oc_product` SET quantity=quantity+'.$quantityadd.', location ="'.strtoupper($_POST['location_entrepot']).'" where product_id='.$_POST['product_id'];
		$req2 = mysqli_query($db,$sql2);
		if($_POST['qtyadd']<>0){
			$sql2 = 'UPDATE `oc_product` SET quantity=quantity+'.$_POST['qtyadd'].',ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];
			$req2 = mysqli_query($db,$sql2);
			$updquantity=$_POST['quantity_total']+$_POST['qtyadd'];
			revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],$updquantity,$db,"oui");
		}
		
	$new=0;
	$_POST['sku']="";
	}
	if($_POST['createlabel']=="yes"){
		echo '<script>window.open("https://phoenixliquidation.ca/interne/createlabeltablette.php?tablette='.$_POST['location_entrepot'].'","etiquette")</script>';
	}
}
if (isset($_POST['sku']) && $_POST['sku']!=""){
		$sql = 'SELECT *,p.quantity quantity_total,P.unallocated_quantity unallocated_quantity,P.quantity quantity, P.location location_entrepot 
		FROM `oc_product` p 
		left join `oc_product_description` pd on (p.product_id=pd.product_id) 
		 
		where pd.language_id=1 and p.sku="'.$_POST['sku'].'" or p.product_id="'.$_POST['sku'].'"';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);
		$new=1;
		if($data['marketplace_item_id']>0){
			$background="green";
		}else{
			$sql = 'SELECT *,P.unallocated_quantity unallocated_quantity,P.quantity quantity, P.location location_entrepot FROM `oc_product` p join `oc_product_description` pd on (p.product_id=pd.product_id)  where pd.language_id=1 and p.upc="'.$_POST['sku'].'" or p.product_id="'.$_POST['sku'].'"';
//echo $sql;
		// on envoie la requête
			$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
			$data = mysqli_fetch_assoc($req);
			if($data['marketplace_item_id']>0){
				$background="green";
			}else{
				$background="red";
			}
		}
}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
 <link href="<?echo $GLOBALS['WEBSITE'];?>interne/stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="<?echo $background;?>">
<form id="form_67341" class="appnitro" action="trf_inventaire.php" method="post">
<div class="form_description">
<h1>Transfer Item</h1>
</div>
<h3>SKU
 <input id="sku" class="element text medium"  <?if ($new!=1){?>autofocus<?}?> type="text" name="sku" value="<?echo $data['sku'];?>" maxlength="255" /></h3>
<br />
LABEL TABLETTE <input type="checkbox" id="createlabel" name="createlabel" value="yes"/>
<br>
<?if ($new==1){?>

<?
	/* 		$sql3 = 'SELECT * FROM `oc_condition` where condition_id='.$data['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$description.='<strong>Model : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data['name']).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$description.='<strong>Condition : </strong>'.$data3['name'].' <br>';
			echo $description; */
?>

<table style="width:100%">
	<td>
		<img height="200" src="<? echo $GLOBALS['WEBSITE'];?>/image/<?echo $data['image'];?>"/><br>
		<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="new" value="<?echo $new;?>" />
<input type="hidden" name="quantity_total" value="<?echo $data['quantity_total'];?>" />
<input type="hidden" name="ebay_id" value="<?echo $data['marketplace_item_id'];?>" />
<input type="hidden" name="status" value="1" />
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
	</td>
	<td>
		<h3>Titre : </h3><?echo $data['name'];?>
				<h3>En Entrepot: 
		<?echo $data['quantity'];?> 
		</h3>
		<h3>A Transferer: <span class="symbol" style="font-size: 13px;"></span></h3>
		<?echo $erreurquantitysold;?>
		<select id="quantitytrf" name="quantitytrf"  onchange="change_autofocus()">
			<option value="0" selected>0</option>
		<?for ($i=1; $i<$data['unallocated_quantity']+1; $i++){?>
			<option value="<?echo $i;?>" selected><?echo $i;?></option>
		<?}?>
		</select><br>
		<h3>Quantity supplementaire:</h3><br>
		<input id="qtyadd" class="element text medium"  type="text" name="qtyadd" value="0" maxlength="255" />
<br />
		<h3>Location:  </h3>
		<input id="location_entrepot" class="element text medium" type="text" name="location_entrepot" autofocus value="<?echo $data['location_entrepot'];?>" maxlength="20" />

		
	</td>

</table> 
<?}?>

<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer"> 
<script>
function change_autofocus(){
	var b = document.getElementById("location_entrepot");
	var c = document.getElementById("quantitytrf");
	c.removeAttribute("autofocus","");
	b.setAttribute("autofocus", "");
	b.focus();
}
</script>
</body>
 
</html>
<? //echo 'FINI'; // on ferme la connexion à mysql 

mysqli_close($db); 

?>