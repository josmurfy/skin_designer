<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

// VERIFIER SI LITEM EST DEJA LISTER

if (isset($_POST['invoice'])){
//echo "allo";
		$sql = 'SELECT * FROM `oc_product_reception` where transfert=1 group by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while ($data = mysql_fetch_assoc($req))
		{

				$sql2 = 'UPDATE `oc_product_reception` SET transfert="1",recycle="0" where upc='.$data[upc];
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				$data2 = mysql_fetch_assoc($req2); 

		}
		$sql = 'SELECT * FROM `oc_product_reception` where recycle=1 group by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while ($data = mysql_fetch_assoc($req))
		{

				$sql2 = 'UPDATE `oc_product_reception` SET recycle="1" where upc='.$data[upc];
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				$data2 = mysql_fetch_assoc($req2); 

		}


}






if (isset($_POST['invoice']) && $_POST['cost']>0){
//echo "allo";
		$sql = 'SELECT * FROM `oc_product_reception` where invoice = "'.$_POST['invoice'].'"';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$ValeurTotal=0;
		while ($data = mysql_fetch_assoc($req))
		{
		$ValeurTotal=$ValeurTotal+($data[priceebay]*$data[quantity]);
		$rate=$data['pricedetailcad']/$data['pricedetailusd'];
		$cost=$data['pricedetailcad']*$_POST['cost'];
		$pricesuggest=$data['pricedetailusd']*.85;
		$pricepotentiel=($data['pricedetailusd']*.85*.85*.90)-3.5-1.5;
		$profit=($pricepotentiel*$rate)-$cost;
		if ($profit<0 and $data[transfert]==0){
			$recycle=1;
		}else{
			$recycle=0;
		}
		if ($profit<0 and $data[transfert]==1){
			$recycle=0;
		}
		if (strlen($data[upc])==11){ 
		$upcvieux=$data[upc];
		$data[upc]="0".$data[upc];
		//$sql2 = 'UPDATE `oc_product` SET sku="'.$data[upc].'",upc="'.$data[upc].'" where sku='.$upcvieux;
		//echo $sql2.'<br><br>';
		//$req2 = mysql_query($sql2);
		//$data2 = mysql_fetch_assoc($req2); 
		}
		
		$sql2 = 'UPDATE `oc_product_reception` SET upc="'.$data[upc].'",recycle="'.$recycle.'",pricesuggest="'.$pricesuggest.'",pricecostper="'.$_POST['cost'].'",pricecost="'.$cost.'",pricepotentiel="'.$pricepotentiel.'", profit ="'.$profit.'" where profit>-9999 and profit<9999 and product_id='.$data['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);
		$data2 = mysql_fetch_assoc($req2); 
		}
	$new=0;
}

echo $ValeurTotal;

?>

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
<form id="form_67341" class="appnitro" action="calculcostreceptionnewnew.php" method="post">
<div class="form_description">
<h1>Calcul des couts</h1>
</div>
<h3>Invoice<input id="invoice" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="255" /></h3>


<h3><label class="description" for="categorie">Ratio du cout (x.xx):  </label></h3>
<input id="location" class="element text medium" type="text" name="cost" value="<?echo $_POST['cost'];?>" maxlength="20" />



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
mysql_close(); ?>