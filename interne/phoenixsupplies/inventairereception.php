<?
//echo $_POST['upc'];
$upc=$_POST['upc'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

if (isset($_POST['upc']) && $_POST['invoice']){
		$sql = 'SELECT * FROM `oc_product_reception` where upc ='.substr($_POST['upc'],0,12).' and invoice = '.$_POST['invoice'];
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);
		if($data['close']==1){
			echo "IMPOSSIBLE LA FACTURE EST FERME";
			die();
		}
		if(is_null ($data['close'])){
			$sql3 = 'SELECT * FROM `oc_product_reception` where upc ="'.substr($_POST['upc'],0,12).'" group by upc';
			$req3 = mysqli_query($db,$sql3);		
			// echo $sql3.'<br><br>';
			$data3 = mysqli_fetch_assoc($req3);
			if(is_null ($data3['close'])){
				$sql2 = 'INSERT INTO `oc_product_reception` (quantityrecu,upc,invoice,close) value (1,"'.substr($_POST['upc'],0,12).'","'.$_POST['invoice'].'",3)';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$bgcolor="yellow";
			}else{
				$sql2 = 'INSERT INTO `oc_product_reception` (upc,invoice,title,model,brand,pricecost,pricepotentiel,pricedetailusd,pricedetailcad,profit,recycle,close) 
				value ("'.substr($_POST['upc'],0,12).'","'.$_POST['invoice'].'","'.$data3['title'].'","'.$data3['model'].'","'.$data3['brand'].'","'.$data3['pricecost'].'","'.$data3['pricepotentiel'].'","'.$data3['pricedetailusd'].'","'.$data3['pricedetailcad'].'","'.$data3['profit'].'","'.$data3['recycle'].'","'.$data3['close'].'")';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$data['profit']=$data3['profit']; 
				$data['title']=$data3['title'];
				$data['quantityrecu']=0;	
				$data['close']=$data3['close'];				
			}
		}
		if ($data['recycle']==1)
		{
			$bgcolor="red";
			//echo "allo1";
		}elseif($data['recycle']==0 && $data['transfert']==1){
			//	echo "allo2";
			$bgcolor="pink";
		}elseif($data['recycle']==0 && $data['close']<3){
			//	echo "allo2";
			$bgcolor="green";
		}elseif($data['close']==3){
				//echo "allo3";
			$bgcolor="yellow";
		}
		$data['quantityrecu']++;
		$sql2 = 'UPDATE `oc_product_reception` SET quantityrecu="'.$data['quantityrecu'].'" where upc = "'.substr($_POST['upc'],0,12).'" and invoice="'.$_POST['invoice'].'"';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$data2 = mysqli_fetch_assoc($req2); 
		$data['quantityrecu']=0;
	$new=0;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="<?echo $bgcolor;?>">
<form id="form_67341" class="appnitro" action="inventairereception.php" method="post">
<div class="form_description">
<h1>Inventaire Reception Item</h1>
</div>
<br><h3><a href="createsmallbarcode.php?product_id=<?echo $_POST['upc'];?>" target="_blank" style="color:#ff0000"><strong>LABEL UPC</strong></a><br>
<h1><?echo $data['title'];?></h1>
<font size="50"><?echo substr($data['upc'],0,1);?></font>
<h3>INVOICE <input id="invoice" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="255" /></h3>
<h3>UPC <input id="upc" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="upc" value="" maxlength="255" autofocus /></h3>

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
<? echo 'FINI'; // on ferme la connexion à 
mysqli_close($db); ?>