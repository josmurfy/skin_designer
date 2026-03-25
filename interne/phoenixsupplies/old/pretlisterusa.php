<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku']; 
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s&eacute;lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);


if ($_POST['quantity']<>0){
	
	
				$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$_POST['product_id']."', '183628')";
				$req = mysql_query($sql);
				//echo $sql;
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183630'";
				$req = mysql_query($sql);	
				//echo $sql;
				$sql2 = 'UPDATE `oc_product` SET `quantity` = quantity+0'.$_POST['quantity'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2); 
				header("location: ".$_GET['action'].".php"); 
}?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
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
<h1>Pret a lister l'item </h1>
<h3><a href="createsmallbarcode.php?product_id=<?echo $_GET['product_id'];?>" target="label" style="color:#ff0000"><strong>SMALL LABEL</strong></a>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="pretlisterusa.php?action=<?echo $_GET['action'] ?>" method="post">

		<h3>Quantite en inventaire (<?echo $data3['quantity'];?>)</h3>
		
		<td>Quantite a ajouter : <input id="quantity"  type="text" name="quantity" value="" size="10" /></td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>

 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion � mysql 
mysql_close(); ?>