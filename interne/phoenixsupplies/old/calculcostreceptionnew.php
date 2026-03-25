<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
if (isset($_POST['invoice'])){
//echo "allo";
		$sql = 'SELECT * FROM `oc_product_reception` where quantity=0 and invoice = "'.$_POST['invoice'].'" order by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$quantity=0;
		$upcancien=0;
		$ok=0;
		while ($data = mysql_fetch_assoc($req))
		{
			$ok=1;
			if ($data[upc]==$upcancien || $upcancien==0)
			{
				$quantity++;
				$product_id=$data['product_id'];
				$upcancien=$data[upc];
			}else{
				$sql2 = 'UPDATE `oc_product_reception` SET quantity="'.$quantity.'" where product_id='.$product_id;
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				$data2 = mysql_fetch_assoc($req2); 
				$product_id=$data['product_id'];
				$quantity=1;
				$upcancien=$data[upc];
			}
		}
		if($ok==1){
		$sql2 = 'delete from `oc_product_reception` where quantity=0 and invoice = "'.$_POST['invoice'].'"';
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				$data2 = mysql_fetch_assoc($req2); 	
		}
	$new=0;
}
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


// mettre ajour les orphelins non declar�

//echo "allo";
		$sql = 'SELECT * FROM `oc_product_reception` where title="" and invoice = "'.$_POST['invoice'].'" group by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while ($data = mysql_fetch_assoc($req))
		{
				$walmart=0;
				$close=0;
				$sql3 = 'SELECT * FROM `oc_product_analyse` where upc='.$data[upc].' group by upc'; 
				$req3 = mysql_query($sql3);
				$data3 = mysql_fetch_assoc($req3);
				if($data3[walmartprice]>0){
					$walmart=9999;
					$pricedetailusd=$data3[walmartprice];					
				}elseif($data3[amazonprice]>$data3[ebayprice]){
					$pricedetailusd=$data3[amazonprice];
				}else{
					$pricedetailusd=$data3[ebayprice];
				}
				if($pricedetailusd==0)$close=3;
				$data3[weight]=$data3[weight]/16;
				$sql2 = 'UPDATE `oc_product_reception` SET quantity='.$data[quantityrecu].',model="'.$data3[model].'",title="'.$data3[title].'",brand="'.$data3[brand].'",priceretailnew='.$walmart.',close='.$close.',  pricedetailusd='.$pricedetailusd.' where  title="" and upc='.$data[upc].' and invoice='.$_POST['invoice'];
				/*quantityrecu=0,quantity='.$data[quantityrecu].',*/
				echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				
                
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
		$pricesuggest=$data['pricedetailusd'];
		$pricepotentiel=($data['pricedetailusd']*.85)-2.66;
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
<form id="form_67341" class="appnitro" action="calculcostreceptionnew.php" method="post">
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