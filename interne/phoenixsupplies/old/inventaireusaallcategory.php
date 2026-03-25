<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 


		$sql = 'SELECT price,quantity,location,sku,oc_product_description.name,image,condition_id,retailprice FROM `oc_product`,`oc_product_description`,oc_product_to_category where oc_product.product_id=oc_product_to_category.product_id and oc_product.product_id=oc_product_description.product_id  and oc_product.usa=1 and oc_product.inventaire=1 and oc_product.quantity>0 and oc_product.status=1 group by sku order by oc_product_to_category.category_id,sku desc';
//echo $sql;
		// on envoie la requ�te
		$req = mysql_query($sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$data = mysql_fetch_assoc($req);
$bgcolor=ffffff;
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>

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
<body bgcolor="ffffff">
<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

<table border="1">
	<tr>
	<th bgcolor="ff6251">
	</th>
	<th bgcolor="ff6251">
	</th>
	<th bgcolor="ff6251">
	Nom du Produit
	</th>
	<th bgcolor="ff6251">
	Condition
	</th>
	<th bgcolor="ff6251">
	Retail $
	</th>
	<th bgcolor="ff6251">
	Prix $
	</th>
	<th bgcolor="ff6251">
	Quantite
	</th>
	<th bgcolor="ff6251">
	Location
	</th>
	</tr>
<?while($data = mysql_fetch_assoc($req)){
	if ($bgcolor=="ffffff"){
		$bgcolor="c0c0c0";
	}else{
		$bgcolor="ffffff";
	}
		if($data['condition_id']=="9")$algoetat="New";
		if($data['condition_id']=="99")$algoetat="New(other)";
		if($data['condition_id']=="2")$algoetat=="Manufacturer Refurbished";
		if($data['condition_id']=="22")$algoetat="Refurbished";
		if($data['condition_id']=="8")$algoetat="Used";	?>
	<tr>
	<td bgcolor="<?echo $bgcolor;?>">
	<img height="50" src="http://www.phoenixsupplies.ca/image/<?echo $data['image'];?>"/>
	</td>
	<td>
	<svg class="barcode"
	jsbarcode-value="<?echo $data['sku'];?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
	</svg>

<script>
JsBarcode(".barcode").init();

</script>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo $data['name'];?></b>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo $algoetat;?></b>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo number_format((float)$data['retailprice']*1.3, 2, '.', '');?></b>
	</td>

	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo number_format((float)$data['price']*1.3, 2, '.', '');?></b>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo $data['quantity'];?></b>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<b><?echo $data['location'];?></b>
	</td>
	</tr>
<?}?>
</table>
</body>
</html>
<? // on ferme la connexion � 
mysql_close(); ?>