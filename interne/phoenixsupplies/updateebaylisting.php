<? 
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

if(isset($_POST['ebayinput']))
{	
		$taux=1;
		//echo $taux;
		$_POST['ebayinput']=str_replace(array("\&quot"),"",$_POST['ebayinput']);
		$ebayinputnametab=explode("\n", $_POST['ebayinput']);
		if($_POST['ebayinput']!=""){
				$sql = 'delete from `oc_ebay_listing_sync` where usa =0';
				$req = mysqli_query($db,$sql);
		}
		foreach($ebayinputnametab as $ebayinputname) 
		{	

			$ebayinputnameline=explode("\t", $ebayinputname);	
				$sql3 = 'select name from oc_category_description where category_id='.$ebayinputnameline[15].' ';
				//echo $sql2.'<br><br>';
				$req3 = mysqli_query($db,$sql3);
				$data3 = mysqli_fetch_assoc($req3);
			if($ebayinputnameline[0]!="Item ID" && strlen($ebayinputnameline[0])==12){
					$sql = 'INSERT INTO `oc_ebay_listing_sync` (variant,ebay_item_id,price,quantity,quantityvendu,category_id,category_name,product_id,usa,condition_ebay)';
					$sql = $sql.'values (9,"'.$ebayinputnameline[0].'","'.$ebayinputnameline[8].'","'.$ebayinputnameline[5].'","'.$ebayinputnameline[6].'","'.$ebayinputnameline[15].'","'.$data3['name'].'","'.$ebayinputnameline[1].'","0","'.$ebayinputnameline[21].'")';
							$i=0;
					//echo $ebayinputnameline[0]."<br>";	
					//echo $sql."<br>";
			}

		//echo strlen($ebayinputnameline[10])."<br>";
				$req = mysqli_query($db,$sql);
				//$data = mysqli_fetch_assoc($req);
//exit;
		}
			$sql = 'SELECT oc_product.product_id,quantity,price,ebay_item_id FROM `oc_ebay_listing`,oc_product where oc_ebay_listing.product_id=oc_product.product_id and usa=0 order by oc_product.product_id';
			 // on envoie la requ�te
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2 = 'UPDATE `oc_ebay_listing_sync` SET `product_id` = '.$data['product_id'].',`quantitysite` = '.$data['quantity'].',`pricesite` = '.$data['price'].' where ebay_item_id='.$data['ebay_item_id'].' ';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$i++;
			}
			//echo $i;
			$sql = 'delete from `oc_ebay_listing`';
			 // on envoie la requ�te
			$req = mysqli_query($db,$sql);
			$sql2 = 'UPDATE `oc_product` SET `ebay_id` = 0,ebay_last_check="2020-09-01" where usa=0 ';
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			$sql = 'SELECT * FROM oc_ebay_listing_sync';
			 // on envoie la requ�te
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement 
			while($data = mysqli_fetch_assoc($req))
			{
				$sql2 = 'INSERT INTO `oc_ebay_listing` (variant,ebay_item_id,product_id)';
				$sql2 = $sql2.'values (9,"'.$data['ebay_item_id'].'","'.$data['product_id'].'")'; 

				//echo $ebayinputnameline[0]."<br>";		
				//echo $sql2."<br>";
				$req2 = mysqli_query($db,$sql2);
				$price=$data['price'];
				$sql2 = 'UPDATE `oc_product` SET ebay_id= '.$data['ebay_item_id'].',ebay_last_check="2020-09-01" where product_id='.$data['product_id'].' ';
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);

			}
			//header("location: modificationitemproduct.php?sku=".$_GET['sku']);
			//header('Location: https://phoenixliquidation.ca/admin/xmlupd.php');
}
			?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="updateebaylisting.php" method="post">
<div class="form_description">
<h1>Insertion Ebay listing COMMERCIALE</h1>

<h3><label class="description" for="categorie">Ebay listing UPDATE:</label></h3>

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
mysqli_close($db); ?>