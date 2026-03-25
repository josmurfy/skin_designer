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



if ($_POST['status']==1 && $_POST['price']>0 && $_POST['price_with_shipping']>0 && $_POST['special']>0){
	
	

				$sql2 = 'UPDATE `oc_product` SET stock_status_id=7,status=1,`price` = '.$_POST['price'].', price_with_shipping='.$_POST['price_with_shipping'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2); 
				$sql2 = 'UPDATE `oc_product_special` SET `price` = '.$_POST['special'].' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];

				//echo $sql2.'<br><br>';
				$req2 = mysql_query($sql2); 
				
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				$req = mysql_query($sql);	
				//echo $sql;	
				header("location: ".$_GET['action'].".php?sku=".$_GET['sku']); 
}else{
	echo "Price a 0$";
}

if ($_POST['price']==""){
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$_POST['price']=$data3['price'];
			$_POST['price_with_shipping']=$data3['price_with_shipping'];
			$_POST['condition_id']=$data3['condition_id'];
			$_POST['sku']=$data3['sku'];
			$sql4 = 'SELECT * FROM `oc_product_special` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql4;
			$req4 = mysql_query($sql4);
			$data4 = mysql_fetch_assoc($req4);
			$_POST['special']=$data4['price'];
			if(mysql_num_rows($req4) == 0){
				$sql6 = 'INSERT INTO `oc_product_special` (`product_id`,`customer_group_id`,`priority`,`price`)';
				$sql6 .=' VALUES ("'.$_GET['product_id'].'", "1", "1", "0");';
				//echo $sql6.'<br><br>';
				$req6 = mysql_query($sql6);
				$_POST['special']="0.0000";		
			}
}else{
	
			$sql2 = 'UPDATE `oc_product` SET `price` = '.$_POST['price'].', price_with_shipping='.$_POST['price_with_shipping'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

			//echo $sql2.'<br><br>';
			$req2 = mysql_query($sql2); 
			$sql2 = 'UPDATE `oc_product_special` SET `price` = '.$_POST['special'].' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];

			//echo $sql2.'<br><br>';
			$req2 = mysql_query($sql2); 
}
if($_POST['condition_id']=="9")$condition=.85;
if($_POST['condition_id']=="99")$condition=.80;
if($_POST['condition_id']=="2")$condition=.75;
if($_POST['condition_id']=="22")$condition=.75;
if($_POST['condition_id']=="8")$condition=.55;
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
<body bgcolor="ffbaba">
<h1>Pret a lister l'item </h1>
<h3><a href="createsmallbarcode.php?product_id=<?echo $_GET['product_id'];?>&sku=<?echo $_POST['sku']?>" target="label" style="color:#ff0000"><strong>SMALL LABEL</strong></a>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="pretlisterusaADMIN.php?action=<?echo $_GET['action'] ?>&product_id=<?echo $_GET['product_id'];?>&sku=<?echo $_POST['sku'];?>" method="post">

		<td>Prix RETAIL : <input id="price"  type="text" name="price" value="<?echo $_POST['price'];?>" size="10" /></td>
		<td>Price Ebay : <input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo $_POST['price_with_shipping'];?>" size="10" /></td>
		<td>Price sur SITE : <input id="special"  type="text" name="special" value="<?echo $_POST['special'];?>" size="10" /> Suggest (<?echo $_POST['price']*$condition?>)</td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="sku" value="<?echo $_POST['sku'];?>" />
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.upcitemdb.com/upc/<?echo substr ($_POST['sku'],0,12);?>","ebaysold2");
</script>
 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion � mysql 
mysql_close(); ?>