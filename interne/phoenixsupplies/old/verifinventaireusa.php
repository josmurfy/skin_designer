<? 
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['product_id']!=""){
				$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$_GET['product_id']."', '183628')";
				$req = mysql_query($sql);
				//echo $sql;
				$sql = "delete from `oc_product_to_category` where product_id='".$_GET['product_id']."' and category_id='183630'";
				$req = mysql_query($sql);	
				//echo $sql;	
		//echo $sql2;
}

		$sql = 'SELECT * FROM `oc_product`,`oc_product_description`,oc_product_to_category where oc_product.product_id=oc_product_to_category.product_id and oc_product_to_category.category_id=183630 and oc_product.product_id=oc_product_description.product_id and quantity>0 and status=0 and usa=1 order by oc_product.sku desc';
		//$sql = 'SELECT * FROM `oc_product`,`oc_product_description`where oc_product.product_id=oc_product_description.product_id and quantity>0 and status=0 and usa=1 order by oc_product.sku desc';

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
<body bgcolor="a8c6fe">
<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

<table border="1">
	<tr>
	<th bgcolor="ff6251">
	</th>
	<th bgcolor="ff6251">
	SKU
	</th>
	<th bgcolor="ff6251">
	Titre
	</th>

	<th bgcolor="ff6251">
	Location
	</th>
	<th bgcolor="ff6251">
	Quantite
	</th>
	</tr>
<?while($data = mysql_fetch_assoc($req)){
	if ($bgcolor=="ffffff"){
		$bgcolor="c0c0c0";
	}else{
		$bgcolor="ffffff";
	}?>
	<tr>
	<td bgcolor="<?echo $bgcolor;?>">
	<img height="50" src="http://www.phoenixsupplies.ca/image/<?echo $data['image'];?>"/>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
		<?if ($data['sku']!=""){?>
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
		<?}?>
		<a  href="verifinventaireusa.php?product_id=<?echo $data['product_id'];?>" target="_blank" class="button--style-red">Verifier</a> 
		<a href="modificationitemusa.php?sku=<?echo $data['sku'];?>" target="_blank" class="button--style-red">Modification</a>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<?echo $data['name'];?>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<?echo $data['location'];?>
	</td>
	<td bgcolor="<?echo $bgcolor;?>">
	<?echo $data['quantity'];?>
	</td>
	</tr>
<?}?>
</table>
</body>
</html>
<? // on ferme la connexion � 
mysql_close(); ?>