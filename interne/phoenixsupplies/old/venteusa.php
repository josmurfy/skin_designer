<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on sélectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

if (isset($_POST['ebay_item_id']) && $_POST['new']==1){
	if($_POST['quantitysold']==0)
	{
		$erreurquantitysold='<strong><font color="red">***ENTREZ LA QTY VENDUE!</font></strong>';
		$new=1;
	}else{
		//$qtysold=$_POST['quantity']-$_POST['quantitysold'];
		$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$_POST['quantitysold'].', location ="'.$_POST['location'].'" where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);
		$data2 = mysql_fetch_assoc($req2); 
	$new=0;
	$_POST['ebay_item_id']="";
	}
}
if (isset($_POST['ebay_item_id']) && $_POST['ebay_item_id']!=""){
		$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and oc_product.product_id=oc_product_description.product_id and (oc_product.product_id="'.$_POST['ebay_item_id'].'" or oc_product.sku="'.$_POST['ebay_item_id'].'")';
//echo $sql;
		// on envoie la requête
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysql_fetch_assoc($req);
		$new=1;
}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
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
</style>
</head>
<body bgcolor="ffbaba">
<form id="form_67341" class="appnitro" action="venteusa.php" method="post">
<div class="form_description">
<h1>Vente Item</h1>
</div>
<h3>Product ID <input id="ebay_item_id" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="ebay_item_id" value="<?echo $data['ebay_item_id'];?>" maxlength="255" /></h3>

<br />
<?if ($new==1){?>
<h3>SKU <input id="sku" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="sku" value="<?echo $data['sku'];?>" maxlength="255" /></h3>
<?
			$sql3 = 'SELECT * FROM `oc_condition` where condition_id='.$data['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$description.='<strong>Model : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data['name']).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$description.='<strong>Condition : </strong>'.$data3['name'].' <br>';
			echo $description;
?>
<br />
<table style="width:100%">
	<td>
		<img height="200" src="http://www.phoenixsupplies.ca/image/<?echo $data['image'];?>"/>
	</td>
	<td>
		<h3><label class="description" for="element_1">Titre : </label></h3><?echo $data['name'];?>
		<h3><label class="description" for="categorie">Location:  </label></h3>
		<input id="location" class="element text medium" type="text" name="location" value="<?echo $data['location'];?>" maxlength="20" />
		<h3>Quantité: <span class="symbol" style="font-size: 13px;"></span>
		<span style="font-size: 13px;"> <input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $data['quantity'];?>" size="10" /> 
		</h3>
		<h3>Quantité Vendu: <span class="symbol" style="font-size: 13px;"></span></h3>
		<?echo $erreurquantitysold;?>
		<select name="quantitysold">
			<option value="0" selected>0</option>
		<?for ($i=1; $i<$data['quantity']+1; $i++){?>
			<option value="<?echo $i;?>"><?echo $i;?></option>
		<?}?>
		</select><br>
		
		
	</td>
</table> 
<?}?>
<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="new" value="<?echo $new;?>" />
<input type="hidden" name="status" value="1" />
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à mysql 
mysql_close(); ?>