<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");  
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
if (isset($_GET['sku'] ))$_POST['sku']=$_GET['sku'];
if (isset($_POST['sku'] ) && $_POST['new']==1){
		$inventaire=1;
		if($_POST['quantity']==0)$inventaire=0;
		$sql2 = 'UPDATE `oc_product` SET inventaire='.$inventaire.',quantity='.$_POST['quantity'].', location ="'.$_POST['location'].'",ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		//$data2 = mysqli_fetch_assoc($req2); 
	$new=0;
	header("location: listing.php?sku=".$_POST['sku']); 
		exit();
}elseif (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
		$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku like "'.(string)$_POST['sku'] .'"';
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
<form id="form_67341" class="appnitro" action="inventaire.php" method="post">
<div class="form_description">
<h1>Inventaire Item</h1>
</div>
<h3>SKU <input id="sku" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" autofocus name="sku" value="<?echo $data['sku'];?>" maxlength="255" /></h3>

<h3><a href="createmultibarcode.php" target="_blank" style="color:#ff0000"><strong>Creer plusieurs barcodes</strong></a></h3>
<?if ($new==1){?>
<h3><a href="createbarcode.php?product_id=<?echo $data['product_id'];?>" target="_blank" style="color:#ff0000"><strong>Creation LABEL</strong></a></h3>
	
<table style="width:100%">
	<td>
		<img height="200" src="<?echo $GLOBALS['WEBSITE'];?>image/<?echo $data['image'];?>"/>
	</td>
	<td>
		<h3><label class="description" for="element_1">Titre : </label></h3><?echo $data['name'];?>
		<h3><label class="description" for="categorie">Location:  </label></h3>
		<input id="location" class="element text medium" type="text" name="location" value="<?echo $data['location'];?>" maxlength="120" />
		<h3>Quantité: <span class="symbol" style="font-size: 13px;">
		 <input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $data['quantity'];?>" size="10" /> 
		</h3>
	</td>
</table> 
<?}?>
<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="new" value="<?echo $new;?>" />
<input type="hidden" name="status" value="1" />
<input id="saveForm" class="fsSubmitButton" type="submit" name="submit" value="Submit" />
<h1><a href="interne.php" class="">Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à 
mysqli_close($db); ?>