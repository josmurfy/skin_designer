<?
//echo $_POST['upc'];
$upc=$_POST['upc'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if (isset($_POST['upc']) && $_POST['invoice']){
		$sql = 'SELECT * FROM `oc_product_reception` where upc = "'.$_POST['upc'].'" and invoice = "'.$_POST['invoice'].'"';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysqli_fetch_assoc($req);

		if(is_null ($data[close])){
			echo "IMPOSSIBLE ITEM OU FACTURE INEXSISTENTE";
			break;
		}
		if ($data[profit]=="")$bgcolor="blue";
		$bgcolor="blue";
		if ($data[profit]==0)
		{
			$bgcolor="blue";
		}elseif($data[profit]>0){
			$bgcolor="blue";
		}
		$data[quantityrecu]--;
		$data[quantityinterne]++;
		$sql2 = 'UPDATE `oc_product_reception` SET quantityinterne="'.$data[quantityinterne].'" where upc = "'.$_POST['upc'].'" and invoice="'.$_POST[invoice].'"';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$data2 = mysqli_fetch_assoc($req2); 
	$new=0;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="<?echo $bgcolor;?>">
<form id="form_67341" class="appnitro" action="inventaireinterne.php" method="post">
<div class="form_description">
<h1>Inventaire Reception Item</h1>
</div>
<br><br>
<h1><?echo $data['title'];?></h1>
<h1><?echo $data['upc'];?></h1>
<h3>INVOICE <input id="invoice" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="255" /></h3>
<h3>UPC <input id="upc" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="upc" value="" maxlength="255" autofocus /></h3>

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