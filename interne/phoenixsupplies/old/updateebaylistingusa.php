<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if(isset($_POST['ebayinput']))
{	
		$taux=75/(100-$_POST['taux']);
		//echo $taux;
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);
		if($_POST['ebayinput']!=""){
				$sql = 'delete from `oc_ebay_listing_sync` where usa =1';
				$req = mysql_query($sql);
		}
		foreach($ebayinputnametab as $ebayinputname) 
		{	

			$ebayinputnameline=explode("\t", $ebayinputname);	
				$sql3 = 'select name from oc_category_description where category_id='.$ebayinputnameline[15].' ';
				//echo $sql2.'<br><br>';
				$req3 = mysql_query($sql3);
				$data3 = mysql_fetch_assoc($req3);
			if($ebayinputnameline[0]!="Item ID" && strlen($ebayinputnameline[0])==12){
					$sql = 'INSERT INTO `oc_ebay_listing_sync` (variant,ebay_item_id,price,quantity,quantityvendu,category_id,category_name,product_id,usa,condition_ebay)';
					$sql = $sql.'values (9,"'.$ebayinputnameline[0].'","'.number_format(str_replace(",","",str_replace("$","",$ebayinputnameline[8]))*$taux,2).'","'.$ebayinputnameline[5].'","'.$ebayinputnameline[6].'","'.$ebayinputnameline[15].'","'.$data3[name].'","'.$ebayinputnameline[1].'","1","'.$ebayinputnameline[21].'")';
							$i=0;
					//echo $ebayinputnameline[0]."<br>";	
					//echo $sql."<br>";
			}

		//echo strlen($ebayinputnameline[10])."<br>";
				$req = mysql_query($sql);
				//$data = mysql_fetch_assoc($req);
//exit;
		}
			$sql = 'SELECT oc_product.product_id,quantity,price,ebay_item_id FROM `oc_ebay_listing`,oc_product where oc_ebay_listing.product_id=oc_product.product_id and usa=1 order by oc_product.product_id';
			 // on envoie la requ�te
			$req = mysql_query($sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			//echo $sql.'<br><br>';
			while($data = mysql_fetch_assoc($req))
			{
				$sql2 = 'UPDATE `oc_ebay_listing_sync` SET `product_id` = '.$data[product_id].',`quantitysite` = '.$data[quantity].',`pricesite` = '.$data[price].' where ebay_item_id='.$data[ebay_item_id].' ';
				echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);
				$i++;
			}
			//echo $i;
			$sql = 'delete from `oc_ebay_listing`';
			 // on envoie la requ�te
			$req = mysql_query($sql);
			$sql2 = 'UPDATE `oc_product` SET `ebay_id` = 0 where usa=1 ';
			//echo $sql2.'<br><br>';
			$req2 = mysql_query($sql2);
			$sql = 'SELECT * FROM oc_ebay_listing_sync';
			 // on envoie la requ�te
			$req = mysql_query($sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			while($data = mysql_fetch_assoc($req))
			{
				$sql2 = 'INSERT INTO `oc_ebay_listing` (variant,ebay_item_id,product_id)';
				$sql2 = $sql2.'values (9,"'.$data[ebay_item_id].'","'.$data[product_id].'")'; 

				//echo $ebayinputnameline[0]."<br>";		
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
				$price=$data[price];
				//$sql2 = 'UPDATE `oc_product` SET price = '.$price.',ebay_id= '.$data[ebay_item_id].' where product_id='.$data[product_id].' ';
				//echo $sql2.'<br><br>';
				//$req2 = mysql_query($sql2);
				$sql2 = 'UPDATE `oc_product` SET ebay_id= '.$data[ebay_item_id].' where product_id='.$data[product_id].' ';
				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2);

			}
			//header("location: modificationitemusa.php?sku=".$_GET['sku']);
header('Location: https://phoenixliquidation.ca/admin/xmlupdtous.php');
}
			?>

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
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="updateebaylistingusa.php" method="post">
<div class="form_description">
<h1>Insertion Ebay listing</h1>

<h3><label class="description" for="categorie">Ebay listing UPDATE:</label></h3>
<h3>Taux d'escompte ebay : <input id="taux" type="taux" name="taux" value="<?echo $_POST['taux'];?>" maxlength="80" /></h3>
<textarea id="ebayinput" name="ebayinput" rows="5" cols="50"></textarea> <br>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer">�
</body>
</html>
<?  // on ferme la connexion � mysql 
mysql_close(); ?>