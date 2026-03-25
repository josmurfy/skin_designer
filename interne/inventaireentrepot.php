<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['action']=="ajouter"){
	(string)$_POST['sku'] =$_GET['sku'];	
	 $test= ajouter_item ($connectionapi,$_GET['sku'],$db); 
	//echo 'allo'; 
}
if ((string)$_POST['sku'] !=$_POST['skuold'] && $_POST['skuold']!=""){
	$_POST['product_id']=="";
	$_POST['pricemagasin']="";
	$_POST['price_with_shipping']="";
	(string)$_POST['upc']="";
	$_POST['upcorigine']="";
	$_POST['price']="";
	$_POST['quantity']="";
	$_POST['location']="";
	$_POST['condition_id']="";
	$_POST['marketplace_item_id']="";
	$_POST['skuold']="";
	$_POST['new']=0;
	(string)$_POST['sku'] ="";
	$_POST['quantityactuel']=0;
}

if (isset($_GET['sku']))(string)$_POST['sku'] =$_GET['sku'];
//ECHO $_GET['sku'];
if (isset($_POST['sku'] ) && $_POST['new']==1){
		$inventaire=1;
		//if($_POST['quantity']==0)$inventaire=0;
		$sql2 = 'UPDATE `oc_product` SET inventaire='.$inventaire.',quantity=quantity+'.$_POST['quantity'].', location ="'.$_POST['location'].'" where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'SELECT unallocated_quantity as quantity FROM `oc_product` where `product_id`='.$_POST['product_id'];//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		//echo $sql2.'<br><br>';
		$data2 = mysqli_fetch_assoc($req2);
		$updquantity=$data2['quantity']+$_POST['quantity']+$_POST['quantityactuel'];
		//echo $updquantity.'<br>'.$_POST['marketplace_item_id'].'<br>'.(string)$_POST['upc'].'<br>';
		update_to_ebay($connectionapi,0,$updquantity,$_POST['marketplace_item_id'],$_POST['product_id']);
		(string)$_POST['sku'] ="";
		$_POST['quantity']=0;
		header("location: listing.php?sku=".$_POST['upc']); 
		exit();
	$new=0;
}elseif (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
				$sql = 'SELECT P.product_id,P.ebay_id,P.sku,name,P.quantity,P.image,P.location,P.upc,P.price,P.price_with_shipping,P.weight,P.length,P.width,P.height,P.date_price_upd,P.condition_id FROM `oc_product` AS P,`oc_product_description` where P.product_id=oc_product_description.product_id  and P.sku like "'.(string)$_POST['sku'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data = mysqli_fetch_assoc($req);
		if(mysqli_num_rows($req)>0){
			if($_POST['product_id']==""){
					$sql2 = 'select * from `oc_product_special` WHERE `product_id` ='.$data['product_id'];

					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					//$_POST['shipping']=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc']);
					//$_POST['pricemagasin']=$data2['price'];
					//$_POST['price_with_shipping']=$data['price_with_shipping'];
					(string)$_POST['upc']=(string)$data['upc'];
					$_POST['upcorigine']=(string)$data['upc'];
					//$_POST['price']=$data['price'];
					$_POST['quantity']=0;
					$_POST['location']=$data['location'];
					//$_POST['condition_id']=$data['condition_id'];
					$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			}else{
				$_POST['pricemagasin']=$_POST['pricemagasin']/1.34;
			}
			$new=1;

			$_POST['name']=$data['name'];
			$_POST['quantityactuel']=$data['quantity'];
			$_POST['image']=$data['image'];
			$_POST['product_id']=$data['product_id'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['date_price_upd_magasin']=$data2['date_price_upd'];
			$_POST['date_price_upd']=$data['date_price_upd'];
			$datemagasin = new DateTime($data2['date_price_upd']);
			$datemagasin = $datemagasin->format('Y-m-d');
			$datemagasin=date_parse ($datemagasin);
			$dateretail = new DateTime($data['date_price_upd']);
			$dateretail = $dateretail->format('Y-m-d');
			$dateretail=date_parse ($dateretail);
			$dateverification = new DateTime('now');
			$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
			$dateverification = $dateverification->format('Y-m-d');
			$dateverification=date_parse ($dateverification);
			
			
/* 			if($dateverification > $dateretail)  print_r($dateretail);
			if($dateverification > $datemagasin) print_r($datemagasin); */
			
			//echo $_POST['shipping'].'<br><br>';
		}
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
<form id="form_67341" class="appnitro" action="inventaireentrepot.php" method="post">
<div class="form_description">
<h1>Inventaire Item en Entrepot</h1>
</div>
<h3>SKU <input id="sku" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" autofocus name="sku" value="<?echo (string)$_POST['sku'] ;?>" maxlength="255" /></h3>

<?if ($new==1){?>
<h3><a href="createsmallbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="CODE" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<h3><a href="inventaireentrepot.php?sku=<?echo substr((string)$_POST['sku'] ,0,12)."NO";?>" target="_self" style="color:#ff0000"><strong>Changer New Other</strong></a> 
	<a href="inventaireentrepot.php?sku=<?echo substr((string)$_POST['sku'] ,0,12);?>" target="_self" style="color:#ff0000"><strong>Changer New</strong></a></h3>
	<a href="inventaireentrepot.php?sku=<?echo (string)$_POST['sku'] ;?>&action=ajouter" target="_self" style="color:#ff0000"><strong>Ajouter item pour inventaire</strong></a></h3>
<table style="width:100%">
	<td>
		<img height="200" src="<?echo $GLOBALS['WEBSITE'];?>image/<?echo $_POST['image'];?>"/>
	</td>
	<td>
		<h3><label class="description" for="element_1">Titre : </label></h3><?echo $_POST['name'];?>
		<h3><label class="description" for="categorie">Location:  </label></h3>
		<input id="location" class="element text medium" type="text" name="location" value="<?echo $_POST['location'];?>" maxlength="120" />
		<h3>Quantité: <span style="color:#FF0000;"><strong>(Item en inventaire: <?echo $_POST['quantityactuel'];?>)</strong><span class="symbol" style="font-size: 13px;">
		 <input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" /> 
		</h3>
	</td>
</table> 
<?}?>
<p class="buttons">
		<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="shipping" value="<?echo $_POST['shipping'];?>" />	
		<input type="hidden" name="upc" value="<?echo (string)$_POST['upc'];?>" />
		<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>" />
		<input type="hidden" name="skuold" value="<?echo $_POST['skuold'];?>" />
		<input type="hidden" name="quantityactuel" value="<?echo $_POST['quantityactuel'];?>" />
<input id="saveForm" class="fsSubmitButton" type="submit" name="submit" value="Submit" />
<h1><a href="interne.php" class="">Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<? // on ferme la connexion à 

mysqli_close($db); ?>