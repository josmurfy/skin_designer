<?
//print_r($_POST['accesoires']);
if ($_GET['sku'] != ""){$_POST['sku']=$_GET['sku'];}

$sku=$_POST['sku'];
// on se connecte à MySQL 

$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on sélectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

if ($_POST['category_id']==""){
	$new=0;
}
//if ($_POST['manufacturer']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer'];
	
if (isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']!=""){//&& $_POST['manufacturer_id']==0
	$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	$_POST['manufacturer_id']= mysql_insert_id();
	$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	//echo $_POST['manufacturer'];
	$_POST['manufacturersupp']="";
	
}
		if ($_POST['manufacturer_recom']!=""){
			$_POST['manufacturer_id']=$_POST['manufacturer_recom'];
		}
//echo $_POST['manufacturer_recom'];
//echo $_POST['new'];
if (isset($_POST['category_id']) && $_POST['category_id']!="" && $_POST['name_product']!="" && $_POST['sku']!=""
	&& $_POST['model']!="" && $_POST['manufacturer_id']!="" && $_POST['new']==1){
		//echo "allo";
		
		
		$sql2 = 'INSERT INTO `oc_product` (`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
		$sql2 .='`status`, `condition_id`)';
		$sql2 .=' VALUES ("1","'.strtoupper($_POST['model']).'", "'.$_POST['sku'].'", "'.$_POST['sku'].'", "'.strtoupper($_POST['model']).'","0'.$_POST['quantity'].'","'.$_POST['manufacturer_id'].'",';
		$sql2 .='"0", "9");';
		//echo $sql2.'<br><br>';
		
		$req2 = mysql_query($sql2);
		$product_id= mysql_insert_id();
		$sql = "INSERT INTO `oc_product_index` ( `priceretailnew`, `title`, `model`, `upc`, `mpn`,`quantity`, `brand`, `priceebay`, `pricesuggest`, `pricedetailusd`, `pricedetailcad`, `pricecost`, `pricecostper`, `pricepotentiel`, `profit`, `quantityrecu`, `quantitydetruit`, `quantityinterne`, `quantityrevente`, `transfert`) VALUES
				('".addslashes($data4['priceretailnew'])."', '".addslashes(strtoupper($_POST['name_product']))."', '".strtoupper($_POST['model'])."', '".$_POST['sku']."', '".strtoupper($_POST['model'])."', '1', '".addslashes($data4['brand'])."', '".addslashes($data4['priceebay'])."', '".addslashes($data4['pricesuggest'])."', '".addslashes($data4['pricedetailusd'])."', '".addslashes($data4['pricedetailcad'])."', '".addslashes($data4['pricecost'])."', '".addslashes($data4['pricecostper'])."', '".addslashes($data4['pricepotentiel'])."', '".addslashes($data4['profit'])."', '".addslashes($quantityrecu)."', '".addslashes($quantitydetruit)."', '".addslashes($quantityinterne)."', '".addslashes($quantityrevente)."', '".addslashes($data4['transfert'])."')";
				//echo $sql2.'<br><br>';
		$req = mysql_query($sql);	
		//echo $_POST['categoryarbonum'];
		// entree les category_id
		
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$_POST['category_id'];
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$name=$data['name'];
			//echo $data[parent_id];
			while($data[parent_id]>0){
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data[parent_id];
	//echo $sql;
				$req = mysql_query($sql);
				$data = mysql_fetch_assoc($req);

			}
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
/* 		$categorynametab=explode('>', $_POST['categoryarbonum']);
		foreach($categorynametab as $categoryname) 
		{
			//echo $categoryname;
			if ($categoryname !=""){
			$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$categoryname."')";
			//echo $sql."<br>";
			$req = mysql_query($sql);
			}
			
		} */
		$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
		$req = mysql_query($sql);
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$product_id."', '".addslashes(strtoupper($_POST['name_product']))."', '".addslashes(strtoupper($_POST['name_product']))."', '1')";
		$req = mysql_query($sql);	
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
		$req = mysql_query($sql);
	
		//VIDAGE CHAMPS
		//$_POST['category_id'] ="";
		$_POST['name_product']="";
		//$_POST['sku']="";
		$_POST['etat']="";
		$_POST['model']="";
		//$_POST['name_product']="";
		$_POST['manufacturer']="";
		$_POST['new']=0;
		$_POST['categoryarbonum']="";
		$_POST['upc']="";
		$_POST['priceebaysold']="";
		$_POST['priceterasold']="";
		$_POST['priceebaynow']="";
		$_POST['quantity']=0;
		$_POST['manufacturer_id']="";
		//echo 'YES';
		$new=0;
				
		header("location: multiuploadusa.php?insert=oui&sku=".$_POST['sku']); 
		exit();
}elseif (isset($_POST['category_id']) && $_POST['category_id']!=""){
	
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$_POST['category_id'];
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$name=$data['name'];
			//echo $data[parent_id];
			while($data[parent_id]>0){
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$_POST['product_id']."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data[parent_id];
	//echo $sql;
				$req = mysql_query($sql);
				$data = mysql_fetch_assoc($req);

			}
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$_POST['product_id']."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
				

		$new=1;
		if (isset($_POST['etat'])){$new2=1;}
		if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;} 

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
<form id="form_67341" class="appnitro" action="insertionitemusa.php?insert=oui&sku=<? echo $_POST['sku'];?>" method="post">
<div class="form_description">
<h1>Insertion Item</h1>

</div>
<h3>SKU <a href="createmultibarcodeusa.php" target="_blank" style="color:#ff0000"><strong>Creer plusieurs barcodes</strong></a></h3></h3>
<input id="sku"  type="text" name="sku"  value="<?echo $_POST['sku'];?>" maxlength="255" autofocus>
<br>
<svg class="barcode"
	jsbarcode-value="<?echo $_POST['sku'];?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>
<script>
JsBarcode(".barcode").init();
</script>
<h3><label class="description" for="categorie">Categorie :</label></h3>
<input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> <br>
La categorie trouvee est : <b><span style="color: #ff0000;"><?echo $name;?></span></b>

<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<?if ($new>=1){?><br />
<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
<table width="100%" >
	<td width="34%" valign="top">
		<table width="100%">
		  <tr>
			<td valign="top"><h3>Brand :</h3></td>
			<td><select name="manufacturer_id">
			<option value="" selected></option>
<?
$sql = 'SELECT * FROM `oc_manufacturer` order by name';

// on envoie la requête
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
$brandrecom="";
while($data = mysql_fetch_assoc($req))
    {
		$selected="";
		if (isset($_POST['manufacturer_id']) && $_POST['manufacturer_id']!=0){
			$test2=strtolower ($_POST['manufacturer_id']);
			$test1=strtolower ($data['manufacturer_id']);
			if ($test1==$test2) {
				$selected="selected";
			}
			//echo "allo";
		}else{
			$test2=strtolower ($data['name']);
			$test1=strtolower ($_POST['name_product']);
			//echo "allo2";
			if (strpos($test1, $test2) !== false) {
				//$selected="selected";
				echo 'allo3';
				//$brandrecom[$i]
				$brandrecom=$brandrecom.",".$data['name']."@".$data['manufacturer_id'];
			}
		}
	

		
?>
			<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
<?}?>
		</select><br>
		<input type="hidden" name="manufacturer_id_old" value="<?echo $_POST['manufacturer_id'];?>" />
<?	
//echo $brandrecom;
$brandrecomtab=explode(',', $brandrecom);
foreach($brandrecomtab as $brandrecomtab2){
	
	if($brandrecomtab2!=null ){
		//echo $brandrecomtab2;
		$brandrecomtab3=explode('@', $brandrecomtab2);
		echo '<input id="manufacturer_recom" class="element radio" type="radio" name="manufacturer_recom" value="'.$brandrecomtab3[1].'"/> 
				<label class="choice" for="etat_1">'.$brandrecomtab3[0].'</label><br>';
	}
}	
?>		
		Ajouter si pas dans la liste : <input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" />
		</td>
		  </tr>
		 <tr>
		<td><h3>Model :</h3></td>
		<td colspan="2"><input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></td>
	  </tr>

		</table>
		
		
		<h3>Condition :</h3>
		<span> 
		<table>
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9" checked> 
				<label class="choice" for="etat_1">New</label>
			</td>
		  </tr>
		</table>
	</td>
	</tr>
	<tr>
	<td width="66%" valign="top">

		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		
		<?}?>
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

	</td>
</table>
</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à mysql 
mysql_close(); ?>