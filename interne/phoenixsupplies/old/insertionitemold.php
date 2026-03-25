<? 
//print_r($_POST['accesoires']);
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
if ($_POST['manufacturer']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer'];
	
if (isset($_POST['manufacturersupp'])&& $_POST['manufacturer_id']==0 && $_POST['manufacturersupp']!=""){
	$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	$_POST['manufacturer_id']= mysql_insert_id();
	$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	//echo $_POST['manufacturer'];
	
}
//echo $_POST['newsku'];
//echo $_POST['new'];
if (isset($_POST['category_id']) && $_POST['name_product']!="" && $_POST['sku']!="" && $_POST['etat']!=""
	&& $_POST['model']!="" && $_POST['manufacturer']!="" && $_POST['new']==1){
		$sql2 = 'INSERT INTO `oc_product` (`weight_class_id`,`length_class_id`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
		$sql2 .='`priceebaysold`, `priceterasold`, `priceebaynow`,`tax_class_id`, `status`, `condition_id`,`invoice`)';
		$sql2 .=' VALUES (7,3,"'.strtoupper($_POST['model']).'", "'.$_POST['sku'].'", "'.$_POST['upc'].'", "'.strtoupper($_POST['model']).'","0'.$_POST['quantity'].'","'.$_POST['manufacturer_id'].'",';
		$sql2 .='"'.$_POST['priceebaysold'].'", "'.$_POST['priceterasold'].'", "'.$_POST['priceebaynow'].'", "9", "0", "'.$_POST['etat'].'","'.$_POST['invoice'].'");';
		//echo $sql2.'<br><br>';
		
		$req2 = mysql_query($sql2);
		$product_id= mysql_insert_id();
		//echo $_POST['categoryarbonum'];
		// entree les category_id
		$categorynametab=explode('>', $_POST['categoryarbonum']);
		foreach($categorynametab as $categoryname) 
		{
			//echo $categoryname;
			if ($categoryname !=""){
			$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$categoryname."')";
			//echo $sql."<br>";
			$req = mysql_query($sql);
			}
			
		}
		$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
		$req = mysql_query($sql);
		$sql = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$product_id."', '".strtoupper($_POST['name_product'])."', '".strtoupper($_POST['name_product'])."', '1')";
		$req = mysql_query($sql);	
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
		$req = mysql_query($sql);	
		//VIDAGE CHAMPS
		$_POST['category_id'] ="";
		$_POST['name_product']="";
		//$_POST['sku']="";
		$_POST['etat']="";
		$_POST['model']="";
		$_POST['name_product']="";
		$_POST['manufacturer']="";
		$_POST['new']=0;
		$_POST['categoryarbonum']="";
		$_POST['upc']="";
		$_POST['priceebaysold']="";
		$_POST['priceterasold']="";
		$_POST['priceebaynow']="";
		$_POST['quantity']=0;
		//echo 'YES';
		$new=0;
				
		header("location: multiupload.php?insert=oui&sku=".$_POST['sku']); 
		exit();
}elseif (isset($_POST['category_id']) && $_POST['category_id']!=""){
		$_POST['category_id']=str_replace(array("\r","\n"),"",$_POST['category_id']);
		$categorynametab=explode('>', $_POST['category_id']);
		$category_id=0;
		$categoryarbonum="";
		if($_POST['newsku']==1)$_POST['sku']=mt_rand ( 100000000000 , 999999999999 );
		foreach($categorynametab as $categoryname) 
		{
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and parent_id='.$category_id.' and name like "'.$categoryname.'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$category_id=$data['category_id'];
			$name=$data['name'];
			$categoryarbonum.=$data['category_id'].">";
		}
		$new=1;
		if (isset($_POST['etat'])){$new2=1;}
		if (isset($_POST['manufacturer'])==false){$_POST['manufacturer']=0;}

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
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="insertionitem.php?insert=oui&sku=<? echo $_POST['sku'];?>" method="post">
<div class="form_description">
<h1>Insertion Item</h1>

</div>
<h3>SKU <a href="createmultibarcode.php" target="_blank" style="color:#ff0000"><strong>Creer plusieurs barcodes</strong></a></h3></h3>
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

<textarea id="category_id" name="category_id" rows="5" cols="50"><?echo $_POST['category_id'];?></textarea> <br>
La catégorie trouvée est : <b><span style="color: #ff0000;"><?echo $name;?></span></b>

<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<?if ($new>=1){?><br />
<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
<table width="100%" >
	<td width="34%" valign="top">
		<table width="100%">
		  <tr>
			<td valign="top"><h3>Brand :</h3></td>
			<td><select name="manufacturer">
			<option value="" selected></option>
<?
$sql = 'SELECT * FROM `oc_manufacturer` order by name';

// on envoie la requête
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {
		$selected="";
		if (isset($_POST['manufacturer_id'])){
			$test2=strtolower ($_POST['manufacturer_id']);
			$test1=strtolower ($data['manufacturer_id']);
			if ($test1==$test2) {
				$selected="selected";
			}
		}else{
			$test2=strtolower ($data['name']);
			$test1=strtolower ($_POST['name_product']);
			if (strpos($test1, $test2) !== false) {
				$selected="selected";
				//echo 'allo';
			}
		}
	
		
		
?>
			<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
<?}?>
		</select><br>
		<input type="hidden" name="manufacturer_id" value="<?echo $_POST['manufacturer'];?>" />
		
		<input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" <?if (isset($_POST['manufacturersupp']))echo "disabled";?>/>
		</td>
		  </tr>
		 <tr>
		<td><h3>Model :</h3></td>
		<td colspan="2"><input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></td>
	  </tr>
	  <tr>
		<td><h3>Facture d'achat :</h3></td>
		<td colspan="2"><input id="invoice" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="80" /></td>
	  </tr>
		</table>
		
		
		<h3>Condition :</h3>
		<span> 
		<table>
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9" <?if ($_POST['etat']==9){?>checked<?}?>/> 
				<label class="choice" for="etat_1">New</label></td>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99" <?if ($_POST['etat']==99){?>checked<?}?>/> 
				<label class="choice" for="etat_2">New (Other)</label> <br /></td>
		  </tr>
		  <tr>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2" <?if ($_POST['etat']==2){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22" <?if ($_POST['etat']==22){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Seller Refurbished</label> <br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8" <?if ($_POST['etat']==8){?>checked<?}?>/> 
				<label class="choice" for="etat_4">Used - Like New</label> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7" <?if ($_POST['etat']==7){?>checked<?}?>/> 
				<label class="choice" for="etat_5">Used - Very Good</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6" <?if ($_POST['etat']==6){?>checked<?}?>/> 
				<label class="choice" for="etat_6">Used - Good</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5" <?if ($_POST['etat']==5){?>checked<?}?>/> 
				<label class="choice" for="etat_7">Used - Poor</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1" <?if ($_POST['etat']==1){?>checked<?}?>/> 
				<label class="choice" for="etat_8">For Parts Or For Repair</label> </td>
			<td></td>
		  </tr>
		</table>
	</td>
	</tr>
	<tr>
	<td width="66%" valign="top">
	<table width="100%">
	  <tr>
		<td><h3>$ eBay Now : </h3>
		</td>
		<td><input id="priceebaynow" type="text" name="priceebaynow" value="<?echo $_POST['priceebaynow'];?>" size="10" /></td>
		<td><b></b></td>
		<td><h3>$ eBay Sold : </h3>
		</td>
		<td><input id="priceebaysold" type="text" name="priceebaysold" value="<?echo $_POST['priceebaysold'];?>" size="10" /></td>
		<td><b></b></td>
		<td><h3>$ Tera Sold : </h3>
		</td>
		<td><input id="priceterasold" type="text" name="priceterasold" value="<?echo $_POST['priceterasold'];?>" size="10" /></td>
		<td><b></b></td>
	  </tr>
	  <tr>
		<td><h3>Quantité : </h3></td>
		<td><input id="quantity" class="element text currency" type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" /></td>
		<td></td>
	  </tr>
	</table>
		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		
		<?}?>
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

	</td>
</table>
</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à mysql 
mysql_close(); ?>