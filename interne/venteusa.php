<?
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
//$db = mysqli_connect('localhost', 'n7f9655_store', ''.APIUPSPASSWORD.''); 
require 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
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
		$req2 = mysqli_query($db,$sql2);
		$data2 = mysqli_fetch_assoc($req2); 
	$new=0;
	$_POST['ebay_item_id']="";
	}
}
if (isset($_POST['ebay_item_id']) && $_POST['ebay_item_id']!=""){
		$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and oc_product.product_id=oc_product_description.product_id and (oc_product.product_id="'.$_POST['ebay_item_id'].'" or oc_product.sku="'.$_POST['ebay_item_id'].'")';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);
		$new=1;
}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="venteusa.php" method="post">
<div class="form_description">
<h1>Vente Item</h1>
</div>
<h3>Product ID <input id="ebay_item_id" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="ebay_item_id" value="<?echo $data['ebay_item_id'];?>" maxlength="255" /></h3>

<br />
<?if ($new==1){?>
<h3>SKU <input id="sku" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="sku" value="<?echo $data['sku'];?>" maxlength="255" /></h3>
<?
			$sql3 = "SELECT * FROM `oc_condition` where condition_id='".$data['condition_id']."'";
//echo $sql2;
			// on envoie la requête
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
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
		<img height="200" src="<?echo $GLOBALS['WEBSITE'];?>image/<?echo $data['image'];?>"/>
	</td>
	<td>
		<h3><label class="description" for="element_1">Titre : </label></h3><?echo $data['name'];?>
		<h3><label class="description" for="categorie">Location:  </label></h3>
		<input id="location" class="element text medium" type="text" name="location" value="<?echo $data['location'];?>" maxlength="20" />
		<h3>Quantité: <span class="symbol" style="font-size: 13px;">
		 <input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $data['quantity'];?>" size="10" /> 
		</h3>
		<h3>Quantité Vendu: <span class="symbol" style="font-size: 13px;"></h3>
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
<h1><a href="interneusa.php" >Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<? //echo 'FINI'; // on ferme la connexion à mysql 
mysqli_close($db); ?>