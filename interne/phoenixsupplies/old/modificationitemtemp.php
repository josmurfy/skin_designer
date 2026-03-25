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
/* if ($_GET['sku']!=""){
	$_POST['sku']=$_GET['sku'];
	//echo 'allo';
}
if ($_GET['sku']!="" && $_GET['category_id']!=""){
	$_POST['sku']=$_GET['sku'];
	$_POST['category_id']=$_GET['category_id'];
	//echo "allo";
}
if ($_POST['sku']=="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")$_POST['sku']=$_POST['skucheck'];
//echo $_POST['newsku'];
//echo $_POST['category_id']; */
//echo $_POST['action']."<br>";

if ($_POST['product_id']!=""){
		
	//echo "ca passe";
		//$_POST['sku']=$_POST['skuold'];
		if (isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
			//echo $sql2;
			$req2 = mysql_query($sql2);
			$_POST['manufacturer_id']= mysql_insert_id();
			$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
			//echo $sql2;
			$req2 = mysql_query($sql2);
			//echo $_POST['manufacturer_id'];
			$_POST['manufacturersupp']="";
			
		}
				if ($_POST['manufacturer_recom']!=""){
					$_POST['manufacturer_id']=$_POST['manufacturer_recom'];
				}

	
		$sql2 = 'UPDATE `oc_product` SET `model`="'.strtoupper($_POST['model']).'",`color`="'.strtoupper($_POST['color']).'", `upc`=""';
		$sql2 .=', `mpn` = "'.strtoupper($_POST['model']).'",`manufacturer_id` = "'.$_POST['manufacturer_id'].'"';
		$sql2 .=', `price` ="'.$_POST['price'].'", `priceterasold` = "'.$_POST['priceterasold'].'", `priceebaynow` ="'.$_POST['priceebaynow'].'"';
		$sql2 .=', `weight`="'.$_POST['weight'].'",`height`="'.$_POST['height'].'", condition_id="'.$_POST['etat'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'].'",`remarque_interne`="'.$_POST['remarque'];
		$sql2 .='" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

		//echo $sql2.'<br><br>';
		//UPDATE `oc_product` SET `REMARQUE_CORRECTION` = '1' WHERE `oc_product`.`product_id` = 309;
		$req2 = mysql_query($sql2); 
/* 		$sql2 = 'UPDATE `oc_product` SET `weight`="'.$_POST['weight'].'",`height`="'.$_POST['height'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'];
		$sql2 .='" WHERE `oc_product`.`usa`=0 and`oc_product`.`sku` ='.$_POST['sku'];

		//echo $sql2.'<br><br>';	  
		$req2 = mysql_query($sql2);  */
		
		
		//mettre a jour la description recu de algopix
			
					
		//if ($_POST['manufacturer_id']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer_id'];


				
		if (isset($_POST['category_id']) && $_POST['category_id']!=""){
			
			
					$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$_POST['category_id'];
			//echo $sql;
					$req = mysql_query($sql);
					$data = mysql_fetch_assoc($req);
					$categoryname=$data['name'];
					//echo $categoryname;
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
			

				//if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;}

		}		

					$_POST['condition']=",";
					$i=0;
//echo "ca passe encore";
					foreach($_POST['conditionsdetailsupp'] as $conditiondetailsupp) 
					{

						if($conditiondetailsupp!="" && $_POST['conditionsdetailsuppch'][$i]>0){
							$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
							$sql2 .=' VALUES ("'.strtoupper($conditiondetailsupp).'", "1", "2", ",'.$_POST['etat'].',");';
							//echo $sql2.'<br><br>';
							
							$req2 = mysql_query($sql2);
							$conditiondetail= mysql_insert_id();
						$_POST['condition'].=$conditiondetail.",";
						$_POST['conditionsdetailsupp'][$i]="";
						$_POST['conditionsdetailsuppch'][$i]=0;
						}elseif ($conditiondetailsupp!=""){
							$_POST['condition'].=strtoupper($conditiondetailsupp).",";
						}
						
						$i++;
					}
					
					//echo $_POST['condition'];
					$_POST['accessory']=",";
					$i=0;

					foreach($_POST['accesoiressupp'] as $accessorysupp) 
					{

						if($accessorysupp!="" && $_POST['accesoiressuppch'][$i]>0){
							$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
							$sql2 .=' VALUES ("'.strtoupper($accessorysupp).'", "1", "1", ",'.$_POST['etat'].',");';
							//echo $sql2.'<br><br>';
							
							$req2 = mysql_query($sql2);
							$accessorysupp= mysql_insert_id();
						$_POST['accessory'].=$accessorysupp.",";
						$_POST['accesoiressupp'][$i]="";
						$_POST['accesoiressuppch'][$i]=0;
						}elseif ($accessorysupp!=""){
							$_POST['accessory'].=strtoupper($accessorysupp).",";
						}
						
						$i++;
					}
					//echo $_POST['accessory'];
					
					$_POST['test']=",";
					$i=0;

					foreach($_POST['testssupp'] as $testsupp) 
					{
						//echo $_POST[testssuppch][$i];
						if($testsupp!="" && $_POST['testssuppch'][$i]>0){
							$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
							$sql2 .=' VALUES ("'.strtoupper($testsupp).'", "1", "3", ",'.$_POST['etat'].',");';
							//echo $sql2.'<br><br>';
							
							$req2 = mysql_query($sql2);
							$testsupp= mysql_insert_id();
						$_POST['test'].=$testsupp.",";
						$_POST['testssuppch'][$i]=0;
						$_POST['testssupp'][$i]="";
						}elseif ($testsupp!=""){
							$_POST['test'].=strtoupper($testsupp).",";
						}
						
						$i++;
					}

				//mettre ajour les condition
								$sql2 = 'UPDATE `oc_product_description` SET name="'.addslashes(strtoupper($_POST['name_product'])).'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'",description_supp="'.addslashes($_POST['description_supp']).'" WHERE `product_id` ='.$_POST['product_id'];
								//echo $sql2;
								$req2 = mysql_query($sql2);

					if ($_POST['action']>0){
								$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'];

								// on envoie la requ�te
								$req2 = mysql_query($sql2);
								$data2 = mysql_fetch_assoc($req2);
								//echo $_POST['test'];
								// LA description Formater
								$description.='<h2>Description :</h2>';
								if($_POST['description_supp']!='')$description.=$_POST['description_supp'].'<br><br>';
								$description.='<strong>Title : </strong>'.strtoupper($_POST['name_product']).'<br><strong>Model : </strong>'.strtoupper($_POST['model']);
								$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
								if($_POST['color']=="")$_POST['color']="N/A";
								$description.='<strong>Color : </strong>'.strtoupper($_POST['color']).'<br>';
								$description.='<strong>Dimension : </strong>'.doubleval ($_POST['length']).'x'.doubleval ($_POST['width']).'x'.doubleval ($_POST['height']).' Inch<br>';
								$description.='<strong>Weight : </strong>'.doubleval ($_POST['weight']).' Lbs<br>';
								$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$_POST['etat'];
					//echo $sql2;
								// on envoie la requ�te
								$req2 = mysql_query($sql2);
								$data2 = mysql_fetch_assoc($req2);
								$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
								$description.=strtoupper($data2['condition'])."<br>";
					$conditionsupp=explode(',', $_POST['condition']);
					foreach($conditionsupp as $conditioncheck){
						if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
							$description.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
							//echo $i;		
						}
					}			
								
								$description.='</p><h2>Accessories Included :</h2><p>';
					$conditionsupp=explode(',', $_POST['accessory']);
							$description.='<font color="red"><strong>- What you see in the pictures, is what you will receive. Nothing more, Nothing less.</strong></font><br>';
					foreach($conditionsupp as $conditioncheck){
						if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
							$description.='- '.$conditioncheck.'<br>';
							//echo $i;		
						}
					}		
					//echo $_POST['test'];		
							if($_POST['test']!="" && $_POST['test']!=",")$description.='</p><h2>Tests and/or Repairs Done :</h2><p>';
					$conditionsupp=explode(',', $_POST['test']);
					foreach($conditionsupp as $conditioncheck){
						if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
							$description.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
							//echo $i;		
						}
					}	
								$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
						//echo $sql;
								$req = mysql_query($sql);
								$data = mysql_fetch_assoc($req);	


								$description.='<h2>Photos :</h2><table bgcolor="FFFFFF"style="width: 500px;" border="1" cellspacing="1" cellpadding="5" align="center"><tbody>';
								
								$description.='<tr><td style="text-align: center;" align="center" valign="middle"><img src="https://www.phoenixsupplies.ca/image/'.$data['image'].'" width="450"</td></tr>';
												
							
								$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
								$req2= mysql_query($sql2); 
								while($data2 = mysql_fetch_assoc($req2))
								{
									if($i<13){
										$description.='<tr><td style="text-align: center;" align="center" valign="middle"><img src="https://www.phoenixsupplies.ca/image/'.$data2['image'].'" width="450"</td></tr>';
									$i++;
									}
								}
								$description.='</tbody></table><br>';
								//echo $description;
								$_POST['name_product']=htmlentities($_POST['name_product'], ENT_QUOTES);
								//$description=htmlentities($description, ENT_QUOTES);
								
					// modification toutjours de description
								$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,name="'.addslashes(strtoupper($_POST['name_product'])).'", description="'.addslashes($description).'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'", description_supp="'.addslashes($_POST['description_supp']).'" WHERE `product_id` ='.$_POST['product_id'];
								//echo $sql2;
								$req2 = mysql_query($sql2);
								
								
								
								if($_POST['action']==1)$_POST['action']=0;


							$new=1;
					}
					//echo $_POST['manufacturerok'];
			if($_POST['action']==2 && $_POST['titreok']=="ok" && $_POST['modelok']=="ok" && $_POST['manufacturerok']=="ok"){
				//echo "ca passe encore";
				$sql2 = 'UPDATE `oc_product_description` SET description_mod=2 WHERE `product_id` ='.$_POST['product_id'];
				//echo $sql2;
				$req2 = mysql_query($sql2);
				$_POST['action']=0;
				//$_POST['manufacturerok']="";
				//echo $_POST['manufacturerok'];
			}elseif($_POST['action']==9 && $_POST['titreok']=="ok" && $_POST['modelok']=="ok" && $_POST['manufacturerok']=="ok")
				$sql2 = 'UPDATE `oc_product_description` SET description_mod=9 WHERE `product_id` ='.$_POST['product_id'];
				//echo $sql2;
				$req2 = mysql_query($sql2); 
				$_POST['action']=0;
				//$_POST['manufacturerok']="";
				//echo $_POST['manufacturerok'];
			}	
			
			//echo $_POST['manufacturerok'];
			//echo $_POST['sku'];
			$sql = 'SELECT count(oc_product.product_id) as total FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id  and inventaire=1 and description_mod <2 and usa=0 and quantity>0 order by price desc';
			//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			echo $data['total']/2;
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and inventaire=1 and description_mod <2 and usa=0 and quantity>0 order by price desc limit 1';
			//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['sku']=$data['sku'];
			if ($data['description_mod']>1)$_POST['manufacturerok']="";
			$_POST['etat']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			$_POST['color']=$data['color'];
			$_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			$_POST['upc']=$data['upc'];
			$_POST['price']=$data['price'];
			$_POST['priceebaysold']=$data['priceebaysold'];
			$_POST['priceterasold']=$data['priceterasold'];
			$_POST['priceebaynow']=$data['priceebaynow'];
			$_POST['quantityinventaire']=$data['quantity'];
			$_POST['price']=$data['price'];
			$_POST['weight']=$data['weight'];
			$_POST['length']=$data['length'];
			$_POST['width']=$data['width'];
			$_POST['height']=$data['height'];
			$_POST['location']=$data['location'];
			$_POST['invoice']=$data['invoice'];
			//$_POST['category_id'] ="";
			//$_POST['categoryarbonum']="";
			$_POST['accessory']=$data['accessory'];
			$_POST['condition']=$data['condition'];
			$_POST['test']=$data['test'];
			$_POST['description']=$data['description'];
			$_POST['description_supp']=$data['description_supp'];
			$_POST['image']=$data['image'];
			$_POST['remarque']=$data['remarque_interne'];
			$sql = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$categoryname=$data['name'];
			$_POST['category_id']=$data['category_id'];
			$_POST['category_id']=$data['category_id'];
			
			


			$new=1;
/* 	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysql_query($sql9);
			$data9 = mysql_fetch_assoc($req9);
			
			$algopixbrand=$data9['brand']; */
/* 			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysql_query($sql8);
			$data8 = mysql_fetch_assoc($req8); */
			
if($_POST['etat']=="9")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="99")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="2")$algoetat=="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="22")$algoetat="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']<="8")$algoetat="Used&costUsed=".$data8[pricecost]."&costNew=".$data8[pricecost];	  

	 
	 $picexterne=$data9['imageurl'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
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
<h1>Modification Item </h1>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="modificationitemtemp.php" method="post">
<div class="form_description">


</div>
<h3>SKU <?if($new==1){?><a href="modificationitem.php" class="button--style-red">Changer d'item</a><?}?> <a href="interne.php" class="button--style-red">Retour au MENU</a> <a href="multiupload.php?sku=<?echo $_POST['sku']?>" class="button--style-red">Menu Photo</a> <a href="listing.php?sku=<?echo $_POST['sku']?>" class="button--style-red">Menu Listing</a></h3>
<input id="sku"  type="text" name="sku"  value="<?echo $_POST['sku'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="skucheck" value="<?echo $_POST['sku'];?>" />

<br>
<?if ($new>=1){?>
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
<?
/* if($picexterne!="")
{
	echo '<br><a href="'.$picexterne.'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
} */

?>
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

<h3><label class="description" for="categorie">Categorie :</label></h3>

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> <br>
La categorie trouvee est : <b><span style="color: #ff0000;"><?echo $categoryname;?></span></b>
<a href="ajoutcategorie.php?sku=<?echo $_POST['sku']?>&category_id=<?echo $_POST['category_id']?>" class="button--style-red">Ajout Categorie</a>
<h3><label class="description" for="element_1">Titre:</label>
		<input type="checkbox" name="titreok" value="ok" <?if($_POST['titreok']=="ok")echo "checked"?>/> VERIFIE? <input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
		
<?/*<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
*/?>
<h3><label class="description" for="element_1">Description Trouvee du produit:</label>
		
		<textarea id="description_supp" name="description_supp" rows="10" cols="50"><?echo $_POST['description_supp'];?></textarea> </h3>


		<table width="100%">
		  <tr>
			<td valign="top"><h3>Brand :
			<?echo $_POST['manufacturerok'];?>
			<input type="checkbox" name="manufacturerok" value="ok"<?if($_POST['manufacturerok']=="ok")echo "checked"?>/> VERIFIE?
			<select name="manufacturer_id">
			
			<option value="" selected></option>

<?
$sql = 'SELECT * FROM `oc_manufacturer` order by name';

// on envoie la requ�te
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
$brandrecom="";
while($data = mysql_fetch_assoc($req))
    {
		$selected="";
	/* 	if (isset($_POST['manufacturer_id']) && $_POST['manufacturer_id']!=0){ */
			$test2=strtolower ($_POST['manufacturer_id']);
			$test1=strtolower ($data['manufacturer_id']);
			if ($test1==$test2) {
				$selected="selected";
			}
			//echo "allo";
	/* 	}else{ */
			$test2=strtolower ($data['name']);
			$test1=strtolower ($_POST['name_product']);
			//echo "allo2";
			if (strpos($test1, $test2) !== false) {
				//$selected="selected";
				echo 'allo3';
				//$brandrecom[$i]
				$brandrecom=$brandrecom.",".$data['name']."@".$data['manufacturer_id'];
			}
	/* 	} */
	

		
?>
			<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
<?}?>
		</select><br>
		<input type="hidden" name="manufacturer_id_old" value="<?echo $_POST['manufacturer_id'];?>" />
<?	
//echo $brandrecom;
$brandrecomtab=explode(',', $brandrecom);
$nu=0;
foreach($brandrecomtab as $brandrecomtab2){
	
	if($brandrecomtab2!=null ){
		//echo $brandrecomtab2;
		$nu++;
		$brandrecomtab3=explode('@', $brandrecomtab2);
		echo '<input id="manufacturer_recom" class="element radio" type="radio" name="manufacturer_recom" value="'.$brandrecomtab3[1].'"/> 
				<label class="choice" for="manufacturer_recom_'.$nu.'">'.$brandrecomtab3[0].'</label><br>';
	}
}	
?>		
		Ajouter si pas dans la liste : <input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" />
		</td>
		  </tr>




<br>
<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
<table width="100%" >

		 <tr>
		<td><h3>Model : <input type="checkbox" name="modelok" value="ok"<?if($_POST['modelok']=="ok")echo "checked"?>/> VERIFIE?<input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></h3></td>
		
	  </tr>
	  		 <tr>
		<td><h3>Color : <input id="color" type="text" name="color" value="<?echo $_POST['color'];?>" maxlength="80" /></h3></td>
		
	  </tr>
		</table>

	<tr>
	<td width="66%" valign="top">
	<table width="100%">
	  <tr>
		<td><h3>$ Price : 
<input id="price" type="text" name="price" value="<?echo $_POST['price'];?>" size="10" /></h3></td>

		<td>
		</td>
		<td></td>

		<td>
		</td>
		<td></td>

	  </tr>
	  <tr>


		<td><h3><a href="createsmallbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="label" style="color:#ff0000"><strong>SMALL LABEL</strong></a> <a href="createbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="_blank" style="color:#ff0000"><strong>Creation LABEL</strong></a></h3></td>
	  </tr>

	</table>

		
	<table width="100%">
	  <tr>
		<td><h3>Dimension :</h3></td>
		<td><input id="length" class="" type="text" name="length" value="<?echo $_POST['length'];?>" maxlength="5" /></td>
		<td><b>L</b></td>
		<td><input id="width" class="" type="text" name="width" value="<?echo $_POST['width'];?>" maxlength="5" /></td>
		<td><b>P</b></td>		
		<td><input id="height" class="" type="text" name="height" value="<?echo $_POST['height'];?>" maxlength="5" /></td>
		<td><b>H</b></td>	
		<td> </td>
		<td> </td>		
	  </tr>
	  <tr>
		<td><h3>Poids : </h3></td>		
		<td><input id="weight" class="" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" /> </td>
		<td><b>Lbs</b></td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
	  </tr>
	</table>
		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		
		<?}?>
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="etatavant" value="<?echo $_POST['etat'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="quantityinventaire" value="<?echo $_POST['quantityinventaire'];?>" />
		<input type="hidden" name="skuanc" value="<?echo $_POST['sku'];?>" />
		
		<?if($new==1){?>
		<input id="action_1" class="element radio" type="radio" name="action" value="1"/> 
				<label class="choice" for="action_1">Mettre a Jour :</label></td><br>
		<input id="action_2" class="element radio" type="radio" name="action" value="2"/> 
				<label class="choice" for="action_2">Pret pour Relister :</label></td><br> 
		<input id="action_3" class="element radio" type="radio" name="action" value="9"/> 
				<label class="choice" for="action_3">Erreur a corriger :</label></td>
				<h3><label class="description" for="description">REMARQUE ERREUR : </label>
		<input id="remarque"  type="text" name="remarque" value="<?echo $_POST['remarque'];?>" maxlength="255" />
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		
	</td>
</table>	
	<table bgcolor="ffffff" align="center">
<td>
<?echo html_entity_decode($_POST['description']);?> 
</td>
</table>	
<?if($new==1){?>	
		<tr>
			<td>
		
		<input type="hidden" name="condition" value="<?echo $_POST['condition'];?>" />
<?

//ajoute les comment suppl&eacute
//echo $_POST['condition'];
$conditionsupp=explode(',', $_POST['condition']);
$i=0;
$_POST['conditionsdetailsupp'][0]="";
$_POST['conditionsdetailsupp'][1]="";
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['conditionsdetailsupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}

?>
		<h3>Conditions suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="conditionsdetailsupp[]" value="<?echo $_POST['conditionsdetailsupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="conditionsdetailsuppch[]" <?if($_POST['conditionsdetailsuppch'][0]==1){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="conditionsdetailsupp[]" value="<?echo $_POST['conditionsdetailsupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="conditionsdetailsuppch[]" <?if($_POST['conditionsdetailsuppch'][1]==2){echo 'checked';}?> value="2">Save</td>
		  </tr>
		  
		</table>
	</td>
	</tr>
	<tr>
	<td>
		
		<input type="hidden" name="accessory" value="<?echo $_POST['accessory'];?>" />
<?

//ajoute les comment suppl&eacute
//echo $_POST['condition'];
$accessorysupp=explode(',', $_POST['accessory']);
$i=0;
$_POST['accesoiressupp'][0]="";
$_POST['accesoiressupp'][1]="";
foreach($accessorysupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['accesoiressupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}?>
		<h3>Accesoires suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="accesoiressupp[]" value="<?echo $_POST['accesoiressupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="accesoiressuppch[]" <?if($_POST['accesoiressuppch'][0]==1){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="accesoiressupp[]" value="<?echo $_POST['accesoiressupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="accesoiressuppch[]" <?if($_POST['accesoiressuppch'][1]==2){echo 'checked';}?> value="2">Save</td>
		  </tr> 
		</table>
	</td>
	</tr>
		<tr>
	<td>
	
		<input type="hidden" name="test" value="<?echo $_POST['test'];?>" />
<?

//echo $_POST['condition'];
$testsupp=explode(',', $_POST['test']);
$i=0;
$_POST['testssupp'][0]="";
$_POST['testssupp'][1]="";
foreach($testsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['testssupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}?>
		<h3>Tests suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="testssupp[]" value="<?echo $_POST['testssupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="testssuppch[]" <?if($_POST['testssuppch'][0]=="1"){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="testssupp[]" value="<?echo $_POST['testssupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="testssuppch[]" <?if($_POST['testssuppch'][1]=="2"){echo 'checked';}?> value="2">Save</td>
		  </tr> 
		</table>
	</td>
	</tr>
	</table>
<?}?>
</form>

 <h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
  <script>
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysql_close(); ?>