<? 
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['product_id']!=""){
		$sql2 = 'UPDATE `oc_product` SET `inventaire`="1" ';
		$sql2 .=' WHERE `oc_product`.`product_id` ='.$_GET['product_id'];
		$req2 = mysqli_query($db,$sql2);
		//echo $sql2;
}

		$sql = 'SELECT * FROM `oc_product`,`oc_product_description`,oc_ebay_listing where oc_product.product_id=oc_ebay_listing.product_id and oc_product.product_id=oc_product_description.product_id and quantity=0 and oc_ebay_listing.status=1 order by oc_product.product_id desc';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while($data = mysqli_fetch_assoc($req)){
		$sql2 = 'UPDATE `oc_product` SET `inventaire`="0",ebay_last_check="2020-09-01",`status`="0" ';
		$sql2 .=' WHERE `oc_product`.`product_id` ='.$data['product_id'];
		$req2 = mysqli_query($db,$sql2);
		echo $sql2."<br>";		
		$sql2 = 'delete from `oc_ebay_listing`';
		$sql2 .=' WHERE `oc_ebay_listing`.`ebay_item_id` ='.$data['ebay_item_id'];
		echo $sql2."<br><br>";
		$req2 = mysqli_query($db,$sql2);	
		
		}

$bgcolor=ffffff;
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>

<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

<table border="1">
	<tr>
	<th bgcolor="#1a1d5b">
	</th>
	<th bgcolor="#1a1d5b">
	EBAY number
	</th>
	<th bgcolor="#1a1d5b">
	Titre
	</th>

	<th bgcolor="#1a1d5b">
	Location
	</th>
	<th bgcolor="#1a1d5b">
	Quantite
	</th>
	</tr>
<?while($data = mysqli_fetch_assoc($req)){
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
		<?echo $data['ebay_item_id'];?>
		
		<a href="noinventaire.php?product_id=<?echo $data['product_id'];?>" class="button--style-red">En inventaire</a> 
		<a href="modificationitem.php?sku=<?echo $data['sku'];?>" class="button--style-red">Modification</a>
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
mysqli_close($db); ?>