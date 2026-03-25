<?
//print_r($_POST['accesoires']);
$sku=$_GET['sku'];
// on se connecte � MySQL 
//$db = mysqli_connect('localhost', 'n7f9655_store', ''.APIUPSPASSWORD.'');  
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_store',$db); 
require 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");

// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;
if($_POST['condition_id']=="9")$condition=.90;
if($_POST['condition_id']=="99")$condition=.80;
if($_POST['condition_id']=="2")$condition=.70;
if($_POST['condition_id']=="22")$condition=.70;
if($_POST['condition_id']=="8")$condition=.55;


if ($_POST['pricecad']>0){ 
$_POST['price']=$_POST['pricecad']/1.34;
$_POST['pricecad']=0;
}
if ($_POST['special']==0 && $_POST['price']>0){ 
$_POST['special']=$_POST['price']*$condition;
}

if ($_POST['special']>$_POST['price_with_shipping'] && $_POST['price_with_shipping']>0){ 
$_POST['special']=$_POST['price_with_shipping'];
}

if ($_POST['status']==1 && $_POST['price']>0 && $_POST['price_with_shipping']>0 && $_POST['special']>0){
	
				$Date = date('Y-m-d H:i:s');
				$sql2 = 'UPDATE `oc_product` SET tax_class_id=9,date_price_upd="'.$Date.'", stock_status_id=7,status=1,`price` = '.$_POST['price'].', price_with_shipping='.$_POST['price_with_shipping'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2); 
				$sql2 = 'select * from `oc_product_special` WHERE `product_id` ='.$_POST['product_id'];

				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				if(mysqli_num_rows($req2)==0){
					$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$_POST['product_id']."', '1', '1','".$_POST['special']."', '2018-09-01','2218-09-01')";
					$req7 = mysqli_query($db,$sql7);
					//echo $sql7.'<br><br>';
				}else{
					$sql2 = 'UPDATE `oc_product_special` SET `date_start`="2019-09-01",`date_end`="2119-09-01",`price` = '.$_POST['special'].' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];
					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2); 
				}
				
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				$req = mysqli_query($db,$sql);	
				//echo "allo".$_GET['action'];	
				header("location: ".$GLOBALS['WEBSITE']."/interne/listingusaADMIN.php?sku=".$_GET['sku']);  
}else{
	echo "Price a 0$";
}

if ($_POST['price']==""){
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$_POST['price']=$data3['price'];
			$_POST['price_with_shipping']=$data3['price_with_shipping'];
			$_POST['condition_id']=$data3['condition_id'];
			(string)$_POST['sku'] =$data3['sku'];
			$sql4 = 'SELECT * FROM `oc_product_special` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql4;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
			$_POST['special']=$data4['price'];
			if(mysqli_num_rows($req4) == 0){
				$sql6 = 'INSERT INTO `oc_product_special` (`product_id`,`customer_group_id`,`priority`,`price`)';
				$sql6 .=' VALUES ("'.$_GET['product_id'].'", "1", "1", "0");';
				//echo $sql6.'<br><br>';
				$req6 = mysqli_query($db,$sql6);
				$_POST['special']="0.0000";		
			}
}else{
	
			$sql2 = 'UPDATE `oc_product` SET `price` = '.$_POST['price'].', price_with_shipping='.$_POST['price_with_shipping'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2); 
			$sql2 = 'UPDATE `oc_product_special` SET `price` = '.$_POST['special'].' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];

			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2); 
}

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<h1>Pret a lister l'item </h1>
<h3><a href="createsmallbarcode.php?product_id=<?echo $_GET['product_id'];?>&sku=<?echo $_GET['sku']?>" target="label" style="color:#ff0000"><strong>SMALL LABEL</strong></a>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="pretlisterusaADMIN.php?action=<?echo $_GET['action'] ?>&product_id=<?echo $_GET['product_id'];?>&sku=<?echo $_GET['sku'];?>" method="post">

		<td>Prix WALMART.COM: <input id="price"  type="text" name="price" value="<?echo $_POST['price'];?>" size="10" /></td>
		<td>Prix WALMART.CA: <input id="price"  type="text" name="pricecad" value="<?echo $_POST['pricecad'];?>" size="10" /></td>
		<td>Price EBAY (finir par .95): <input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo $_POST['price_with_shipping'];?>" size="10" /></td>
		<td>Price MAGASIN : <input id="special"  type="text" name="special" value="<?echo $_POST['special'];?>" size="10" /> Suggest (<?echo $_POST['price']*$condition?> US)</td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="sku" value="<?echo (string)$_POST['sku'] ;?>" />
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form><?if ($_GET['sku']!=""){?>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_GET['sku'],0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_GET['sku'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.upcitemdb.com/upc/<?echo substr ($_GET['sku'],0,12);?>","google");
</script><?}?>
 <h1><a href="interneusa.php" >Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>