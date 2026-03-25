<?
//echo $_POST['upc'];
$upc=$_POST['upc'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

	if (isset($_GET['modif'])){
		if ($_GET['modif']=="green"){
			$sql2 = 'UPDATE `oc_product_reception` SET recycle=0,profit=9999 where upc = "'.$_GET['upc'].'"';
			$req2 = mysqli_query($db,$sql2);
			//echo $sql2;
		}else{
			$sql2 = 'UPDATE `oc_product_reception` SET recycle=1,profit=-9999 where upc = "'.$_GET['upc'].'"';
			$req2 = mysqli_query($db,$sql2);
			//echo $sql2;
		}
		$_POST['upc']=$_GET['upc'];
		$_POST['invoice']=$_GET['invoice'];
	}
if (isset($_POST['upc'])){

	
	
		$sql = 'SELECT * FROM `oc_product_reception` where upc ="'.$_POST['upc'].'"';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);
		if($data['close']==1){
			echo "IMPOSSIBLE LA FACTURE EST FERME";
			die();
		}
		if(is_null ($data['close'])){
		}
		if ($data['profit']<0)
		{
			$bgcolor="red";
			$modif="green";
			//echo "allo1";
		}elseif($data['profit']>0){
			//	echo "allo2";
			$bgcolor="green";
			$modif="red";
		}elseif($data['close']==3){
				//echo "allo3";
			$bgcolor="yellow";
		}
		//$data['quantityrecu']++;
		//$sql2 = 'UPDATE `oc_product_reception` SET quantityrecu="'.$data['quantityrecu'].'" where upc = "'.$_POST['upc'].'" and invoice="'.$_POST['invoice'].'"';
		//echo $sql2.'<br><br>';
		//$req2 = mysqli_query($db,$sql2);
		//$data2 = mysqli_fetch_assoc($req2); 
		//$data['quantityrecu']=0;
	$new=0;
}


?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="<?echo $bgcolor;?>">
<form id="form_67341" class="appnitro" action="inventairemodif.php" method="post">
<div class="form_description">
<h1>Inventaire Reception Item</h1>
</div>
<br><br>
<h1><?echo $data['title'];?></h1>
<h1><?echo $data['upc'];?></h1>
<h3>UPC <input id="upc" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="upc" value="<?echo $_POST['upc'];?>" maxlength="255" autofocus /></h3>
 <h1><a href="inventairemodif.php?invoice=<?echo $_POST['invoice'];?>&upc=<?echo $_POST['upc'];?>&modif=<?echo $modif;?>" class="button--style-red">Changer le status ROUGE/VERT</a></h1>
<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="new" value="<?echo $new;?>" />
<input type="hidden" name="status" value="1" />
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer">�
</body>
</html>
<? echo 'FINI'; // on ferme la connexion � 
mysqli_close($db); ?>