<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s&eacute;lectionne la base 
mysql_select_db('phoenkv5_store',$db); 

 if ($_GET['action']=="default"){ 
	$_POST['sku']=$_GET['sku'];
	
	 if($_GET['etat']<>9)$test= modifitem (substr($_GET['sku'],0,12),$_GET['product_id'],"",9); 
	 if($_GET['etat']<>99)$test= modifitem (substr($_GET['sku'],0,12)."NO",$_GET['product_id'],"NO",99);
	 if($_GET['etat']<>22)$test= modifitem (substr($_GET['sku'],0,12)."R",$_GET['product_id'],"R",22);
	//echo 'allo';
}
if ($_GET['sku']!=""){
	$_POST['sku']=$_GET['sku'];
	//echo 'allo';
}

/*
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
//echo $_POST['category_id'];

 */
if (isset($_POST['sku']) && $_POST['sku']!=""){
			
			//echo $_POST['sku'];

			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.substr($_POST['sku'],0,12).'"';
	//echo $sql;
}else{
			$sql = 'SELECT * FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca, `oc_product_special` as sp where pr.product_id=sp.product_id and pr.product_id=ds.product_id and 
			pr.product_id=ca.product_id and pr.quantity>0 and pr.usa=1  and  pr.status=1 and not(pr.location="") and sp.price>pr.price group by upc order by pr.price desc  '; //and pr.location like "%magasin%" 
			$req = mysql_query($sql);
			echo mysql_num_rows($req);
			
			$sql = 'SELECT * FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca, `oc_product_special` as sp where pr.product_id=sp.product_id and pr.product_id=ds.product_id and 
			pr.product_id=ca.product_id and pr.quantity>0 and pr.usa=1  and  pr.status=1 and not(pr.location="") and sp.price>pr.price order by pr.price desc limit 1 '; //and pr.location like "%magasin%" 
}
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$sql3 = 'SELECT * FROM `oc_product_special` where product_id = "'.$data['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$_POST['special']=$data3['price'];
			$_POST['name_product']=$data['name'];
			$_POST['name']=$data['name'];
			$sku=substr($_POST['sku'],0,12);
			$_POST['sku']=$data['sku'];
			$_POST['etat']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			$_POST['color']=$data['color'];
			if($_POST['manufacturer_id']=="") $_POST['manufacturer_id']=$data['manufacturer_id'];
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
			$_POST['image']=$data['image'];
			$sql = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$categoryname=$data['name'];
			$_POST['category_id']=$data['category_id'];
			$_POST['category_id']=$data['category_id'];


			$new=1;
	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysql_query($sql9);
			$data9 = mysql_fetch_assoc($req9);
			$_POST['andescription']=$data9['description'];
			$algopixbrand=$data9['brand'];
			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysql_query($sql8);
			$data8 = mysql_fetch_assoc($req8);
			
if($_POST['etat']=="9")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="99")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="2")$algoetat=="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="22")$algoetat="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="8")$algoetat="Used&costUsed=".$data8[pricecost]."&costNew=".$data8[pricecost];	  

	 $_POST['andescription']=$data9['description'];
	
	 $picexterne=$data9['imageurl'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
	 }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>listingusaADMIN.php</title>

  
  
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
</head><body>

<form method="post" action="listingusaADMIN2.php" name="listing">

  
  <table style="text-align: left; width: 1053px; height: 100%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">

    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 112px;">
		<img style="width: 488px; height: 145px;" alt="" src="http://www.phoenixsupplies.ca//image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: middle; background-color: rgb(255, 204, 204); font-weight: bold; text-align: center;"><h3><?if($new==1){?><a href="listingusaADMIN.php" class="button--style-red">Changer d'item</a><?}?> <a href="menulisting.php" class="button--style-red">Retour au MENU</a> 		<?if($new==1){?>
		
		<?}?></h3>
        </td>
        <td style="vertical-align: middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: left;">Sku<br>
        </td>
        <td style="vertical-align: middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: left;"><input type="hidden" name="skucheck" value="<?echo $_POST['sku'];?>" />
		<input id="sku"  type="text" name="sku"  value="<?echo $_POST['sku'];?>" maxlength="255"  autofocus>
		
        </td>
        <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: center;">
        </td>
      </tr>
      <tr>
        <td colspan="1" rowspan="9" style="vertical-align:  middle; height: 24px; width: 342px;">
				<?
			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);	


			
			echo '<img src="https://www.phoenixsupplies.ca/image/'.$data['image'].'" width="450">';
				$imagenew=	'<img src="https://www.phoenixsupplies.ca/image/'.$data['image'].'" width="150">';	
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysql_query($sql2); 
			while($data2 = mysql_fetch_assoc($req2))
			{
				if($i<13){
					echo '<img src="https://www.phoenixsupplies.ca/image/'.$data2['image'].'" width="450">';
				$i++;
				}
			}
			?><br>
        <br>
        <br>
        </td>
        <td style="vertical-align:  middle; height: 0px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Titre:</span></td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 506px;font-weight: bold;"><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><?echo strtoupper ($_POST['name_product']);?><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><span style="font-weight: bold;"></span><br>
        </td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"><br>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 25px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Modele:</span></td>
        <td style="vertical-align:  middle; height: 25px; width: 506px;font-weight: bold;"><?echo $_POST['model'];?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>

      <tr>
        <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Brand:</span><br>
        </td>
        <td style="vertical-align:  middle; width: 506px;font-weight: bold;">

<?
$sql = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'];

// on envoie la requête
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
$brandrecom="";
$data = mysql_fetch_assoc($req);
echo $data['name'];
		
?>

        </td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Categorie:</span><br>
        </td>
        <td style="vertical-align:  middle; width: 506px;font-weight: bold;"><?echo $categoryname;?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 13px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Couleur</span></td>
        <td style="vertical-align:  middle; height: 13px; width: 506px;font-weight: bold;"><?echo $_POST['color'];?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 74px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Description:</span></td>
        <td style="vertical-align:  middle; height: 74px; width: 506px;"><?echo $_POST['andescription'];?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 15px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Dimension:</span></td>
        <td style="vertical-align:  middle; height: 15px; width: 506px;font-weight: bold;"><?echo number_format($_POST['length'],1);?> x <?echo number_format($_POST['width'],1);?>  x <?echo number_format($_POST['height'],1);?></td>	
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Poids:</span></td>
        <td style="vertical-align:  middle; height: 16px; width: 506px;font-weight: bold;"><?echo number_format($_POST['weight'],2);?> Lbs</td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: rgb(255, 204, 204); color: white; width: 112px;"> 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
		<a href="insertionitemusa.php?sku=<? echo $sku;?>&action=listingusaADMIN"><strong>Ajouter</strong></a> 
		
		
		</td>
		
	 
	  </tr>
    </tbody>
  
  </table>

</form>
<?
			$sqln = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'"';
	//echo $sql;
			$reqn = mysql_query($sqln);
			
			
			$rowverif= mysql_affected_rows();
			//$ajout=cloneritem($rowverif,"",9,substr($_POST['sku'],0,12),$_POST['product_id']);
			$reqn = mysql_query($sqln);
			$datan = mysql_fetch_assoc($reqn);
			$sql3 = 'SELECT * FROM `oc_product_special` where product_id = "'.$datan['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$datan['special']=$data3['price'];
			//echo $datano['location'];
			
			
				if($datan['condition']!="" && $datan['condition']!=",")$descriptionn.='<b>Conditions Supplementaire:</b><br>';
				$conditionsupp=explode(',', $datan['condition']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionn.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
						//echo $i;		
					}
				}			
							
				if($datan['accessory']!="" && $datan['accessory']!=",")$descriptionn.='<b>Accessories Included :</b><br>';
				$conditionsupp=explode(',', $datan['accessory']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionn.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}		
				//echo $_POST['test'];		
				if($datan['test']!="" && $datan['test']!=",")$descriptionn.='<b>Tests - Repairs Done :</b><br>';
				$conditionsupp=explode(',', $datan['test']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionn.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}

			$sqlno = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'no"';
	//echo $sql;
			$reqno = mysql_query($sqlno);
			
			
			$rowverif= mysql_affected_rows();

			$ajout=cloneritem($rowverif,NO,99,substr($_POST['sku'],0,12),$_POST['product_id']);
			$reqno = mysql_query($sqlno);
			$datano = mysql_fetch_assoc($reqno);
			$sql3 = 'SELECT * FROM `oc_product_special` where product_id = "'.$datano['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$datano['special']=$data3['price'];
			//echo $datano['location'];
			
			
				if($datano['condition']!="" && $datano['condition']!=",")$descriptionno.='<b>Conditions Supplementaire:</b><br>';
				$conditionsupp=explode(',', $datano['condition']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionno.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
						//echo $i;		
					}
				}			
							
				if($datano['accessory']!="" && $datano['accessory']!=",")$descriptionno.='<b>Accessories Included :</b><br>';
				$conditionsupp=explode(',', $datano['accessory']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionno.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}		
				//echo $_POST['test'];		
				if($datano['test']!="" && $datano['test']!=",")$descriptionno.='<b>Tests - Repairs Done :</b><br>';
				$conditionsupp=explode(',', $datano['test']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionno.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}		
	
			$sqlr = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'r"';
	//echo $sql;
			$reqr = mysql_query($sqlr);
			$ajout=cloneritem($rowverif,R,22,substr($_POST['sku'],0,12),$_POST['product_id']);
			//cloneritem ($rowverif, $typeetat,$numetat,$skuachanger,$default_product_id)
			$reqr = mysql_query($sqlr);
			$datar = mysql_fetch_assoc($reqr);
			$sql3 = 'SELECT * FROM `oc_product_special` where product_id = "'.$datar['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$datar['special']=$data3['price'];
				if($datar['condition']!="" && $datar['condition']!=",")$descriptionr.='<b>Conditions Supplementaire:</b><br>';
				$conditionsupp=explode(',', $datar['condition']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionr.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
						//echo $i;		
					}
				}			
							
				if($datar['accessory']!="" && $datar['accessory']!=",")$descriptionr.='<b>Accessories Included :</b><br>';
				$conditionsupp=explode(',', $datar['accessory']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionr.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}		
				//echo $_POST['test'];		
				if($datar['test']!="" && $datar['test']!=",")$descriptionr.='<b>Tests - Repairs Done :</b><br>';
				$conditionsupp=explode(',', $datar['test']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$descriptionr.='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}				



?>

<table style="text-align: left; width: 1053px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr style="font-weight: bold;">
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: black; color: white; width: 33%;">NEW<br>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: black; color: white; width: 33%;"><span style="font-weight: bold;">NEW(Other)</span><br>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: black; color: white; width: 33%;">REFURBISHED<br>
      </td>
    </tr>
    <tr>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%;"><?echo $datan['name']."<br>";?><?echo $imagenew;?>
	  <?if ($datan['status']==1){echo '<img src="https://www.phoenixsupplies.ca/image/checked.png" width="25">';}else{echo '<img src="https://www.phoenixsupplies.ca/image/checkedd.png" width="25">';}if ($datan['marketplace_item_id']>0){echo '<img src="https://www.phoenixsupplies.ca/image/ebay.png" width="50">';}?>
	 <br><?echo $descriptionn; ?>
	 <br>Price RETAIL : <?echo "$ ".number_format($datan['price'],2);?><?if ($datan['price']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	 <br>Price Ebay : <?echo "$ ".number_format($datan['price_with_shipping'],2);?><?if ($datan['price_with_shipping']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	 <br>Price sur Site : <?echo "$ ".number_format($datan['special'],2);echo "</br>".number_format(($datan['special']/$datan['price']*100),1)."%";?><?if ($datan['special']==0 || ($datan['special']/$datan['price']*100)>75){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	 
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px;"><?echo $datano['name']."<br>";?><?if ($datano['image']!="")echo '<img src="https://www.phoenixsupplies.ca/image/'.$datano['image'].'" width="150">';?>
	  <?if ($datano['status']==1){echo '<img src="https://www.phoenixsupplies.ca/image/checked.png" width="25">';}else{echo '<img src="https://www.phoenixsupplies.ca/image/checkedd.png" width="25">';}if ($datano['marketplace_item_id']>0){echo '<img src="https://www.phoenixsupplies.ca/image/ebay.png" width="50">';}?>
		<br><?echo $descriptionno;?>
		<br>Price RETAIL : <?echo "$ ".number_format($datano['price'],2);?><?if ($datano['price']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
		 <br>Price Ebay : <?echo "$ ".number_format($datano['price_with_shipping'],2);?><?if ($datano['price_with_shipping']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
		 <br>Price sur Site : <?echo "$ ".number_format($datano['special'],2);echo "<br>".number_format(($datano['special']/$datano['price']*100),1)."%";?><?if ($datano['special']==0 || ($datano['special']/$datano['price']*100)>75){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	  </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px;"><?echo $datar['name']."<br>";?><?if ($datar['image']!="")echo '<img src="https://www.phoenixsupplies.ca/image/'.$datar['image'].'" width="150">';?>
	  <?if ($datar['status']==1){echo '<img src="https://www.phoenixsupplies.ca/image/checked.png" width="25">';}else{echo '<img src="https://www.phoenixsupplies.ca/image/checkedd.png" width="25">';}if ($datar['marketplace_item_id']>0){echo '<img src="https://www.phoenixsupplies.ca/image/ebay.png" width="50">';}?>
     <br><?echo $descriptionr;?>	 
	 <br>Price RETAIL : <?echo "$ ".number_format($datar['price'],2);?><?if ($datar['price']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	 <br>Price Ebay : <?echo "$ ".number_format($datar['price_with_shipping'],2);?><?if ($datar['price_with_shipping']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	 <br>Price sur Site : <?echo "$ ".number_format($datar['special'],2);echo "<br>".number_format(($datar['special']/$datar['price']*100),1)."%";?><?if ($datar['special']==0 || ($datar['special']/$datar['price']*100)>75){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
	  </td>
    </tr>
    <tr>

      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="pretlisterusaADMIN.php?product_id=<? echo $_POST['product_id'];?>&action=listingusaADMIN"><strong>Pret a Lister</strong></a>
      </td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	 	<a href="listingusaADMIN.php?etat=9&sku=<? echo $_POST['sku'];?>&product_id=<? echo $_POST['product_id'];?>&action=default"><strong>Par Default</strong></a></td>

      </td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="pretlisterusaADMIN.php?product_id=<? echo $datano['product_id'];?>&action=listingusaADMIN"><strong>Pret a Lister</strong></a></td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="listingusaADMIN.php?etat=99&sku=<? echo $datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=default"><strong>Par Default</strong></a></td></td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="pretlisterusaADMIN.php?product_id=<? echo $datar['product_id'];?>&action=listingusaADMIN"><strong>Pret a Lister</strong></a></td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="listingusaADMIN.php?etat=22&sku=<? echo $datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=default"><strong>Par Default</strong></a></td>
    </tr>
	    <tr>

      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a  href="modificationitemusa.php?sku=<? echo $_POST['sku'];?>&product_id=<? echo $_POST['product_id'];?>&action=listingusaADMIN"><strong>Modifier</strong></a> 
      </td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	 	<a href="clonerusa.php?etat=9&sku=<? echo $_POST['sku'];?>&product_id=<? echo $_POST['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a></td>

      </td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a  href="modificationitemusa.php?sku=<? echo $datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=listingusaADMIN"><strong>Modifier</strong></a> 
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="clonerusa.php?etat=99&sku=<? echo $datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a></td></td>
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a  href="modificationitemusa.php?sku=<? echo $datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=listingusaADMIN"><strong>Modifier</strong></a> 
      <td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
	  <a href="clonerusa.php?etat=22&sku=<? echo $datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white;"><span style="font-weight: bold;">Location:</span><br>
      </td>
      <td style="vertical-align:  middle;font-weight: bold;"><?echo $_POST['location'];?></td>
      <td style="vertical-align:  middle; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">Location:<br>
      </td>
      <td style="vertical-align:  middle; font-weight: bold;"><?echo $datano['location'];?></td>
      <td style="vertical-align:  middle; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">Location:<br>
      </td>
      <td style="vertical-align:  middle; font-weight: bold;"><?echo $datar['location'];?></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; width: 160px; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">Quantite
en stock:<br>
      </td>
      <td style="vertical-align:  middle; width: 174px;font-weight: bold;"><?echo $_POST['quantityinventaire'];?>
      </td>
      <td style="vertical-align:  middle; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">Quantite
en stock:<br>
      </td>
      <td style="vertical-align:  middle; font-weight: bold;"><?echo $datano['quantity'];?></td>
      <td style="vertical-align:  middle; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">Quantite
en stock:<br>
      </td>
      <td style="vertical-align:  middle; font-weight: bold;"><?echo $datar['quantity'];?></td>
    </tr>
  </tbody>
</table>
<?
if (!is_null($_POST['sku'])){
					$sqlo = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku like "'.$_POST['sku'].'%"
					and (sku != "'.$_POST['sku'].'R" and sku != "'.$_POST['sku'].'NO" and sku != "'.$_POST['sku'].'")';
			//echo $sqlo;
					$reqo = mysql_query($sqlo);
					$j=0;
					$i=0;
					//$datao = mysql_fetch_assoc($reqo);
					//print_r ($datao);
				$datatab = array();
			while($datao = mysql_fetch_assoc($reqo)){
				$datatab[$i]=$datao;
				//print_r ($datatab[$i]);
				//echo "allo<br>";
				$sql3 = 'SELECT * FROM `oc_product_special` where product_id = "'.$datao['product_id'].'"';
		//echo $sql3."<br>";
				$req3 = mysql_query($sql3);
				$data3 = mysql_fetch_assoc($req3);
				$datatab[$i]['special']=$data3['price'];
				//echo $data3['price']."<br>";
								if($datao['condition']!="" && $datao['condition']!=",")$datatab[$i]['descriptiono'].='<b>Conditions Supplementaire:</b><br>';
				$conditionsupp=explode(',', $datao['condition']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$datatab[$i]['descriptiono'].='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
						//echo $i;		
					}
				}			
							
				if($datao['accessory']!="" && $datao['accessory']!=",")$datatab[$i]['descriptiono'].='<b>Accessories Included :</b><br>';
				$conditionsupp=explode(',', $datao['accessory']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$datatab[$i]['descriptiono'].='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}		
				//echo $_POST['test'];		
				if($datao['test']!="" && $datao['test']!=",")$datatab[$i]['descriptiono'].='<b>Tests - Repairs Done :</b><br>';
				$conditionsupp=explode(',', $datao['test']);
				foreach($conditionsupp as $conditioncheck){
					if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
						$datatab[$i]['descriptiono'].='- '.$conditioncheck.'<br>';
						//echo $i;		
					}
				}	
				$i++;
			}
			//print_r ($datatab);
			//echo $datatab['special'];
			// var_dump( $datatab[$j]['product_id']);
		?>
		<table style="text-align: left; width: 1053px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
			<tr>
				<td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">ARTICLES CLONES
				</td>
			</tr>
		</table>

<table style="text-align: left; width: 1053px;  margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
	<tr style="font-weight: bold">
	<?for ($j = 0; $j <$i; $j++) {?>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; width: 33%">
			<table style="text-align: left; width: 100%; height: 100%" border="1" cellpadding="2" cellspacing="2">
				<tbody>
				<tr>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: black; color: white; width: 33%;">
					<? echo $datatab[$j]['sku'];?>
				</td>
				</tr>
				<tr>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;  width: 33%;">
					<?echo $datatab[$j]['name'];?>
				</td>
				</tr>
				<tr>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%;">
					<? echo '<img src="https://www.phoenixsupplies.ca/image/'.$datatab[$j]['image'].'" width="150">';?>
					<?if ($datatab[$j]['status']==1){echo '<img src="https://www.phoenixsupplies.ca/image/checked.png" width="25">';}else{echo '<img src="https://www.phoenixsupplies.ca/image/checkedd.png" width="25">';}if ($datatab[$j]['marketplace_item_id']>0){echo '<img src="https://www.phoenixsupplies.ca/image/ebay.png" width="50">';}?>
				</td>
				</tr>
				<tr>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;  width: 33%;">
					<?echo $datatab[$j]['descriptiono']; ?>
				</td>
				</tr>
				<tr>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;  width: 33%;">
				
					 Price RETAIL : <?echo "$ ".number_format($datatab[$j]['price'],2);?><?if ($datatab[$j]['price']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
					 <br>Price Ebay : <?echo "$ ".number_format($datatab[$j]['price_with_shipping'],2);?><?if ($datatab[$j]['price_with_shipping']==0){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
					 <br>Price sur Site : <?echo "$ ".number_format($datatab[$j]['special'],2);echo "</br>".number_format(($datatab[$j]['special']/$datatab[$j]['price']*100),1)."%";?><?if ($datatab[$j]['special']==0 || ($datatab[$j]['special']/$datatab[$j]['price']*100)>75){echo '<img src="https://www.phoenixsupplies.ca/image/crossed.png" width="10">';}?>
				</td>
				</tr>
				<tr>
				<td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
					<a href="pretlisterusaADMIN.php?product_id=<? echo $datatab[$j]['product_id'];?>&action=listingusaADMIN"><strong>Pret a Lister</strong></a>
				</td>
				<td style="vertical-align:  middle; font-weight: bold; text-align: center; background-color: rgb(255, 204, 204); height: 0px; width: 16%;">
					<a  href="modificationitemusa.php?sku=<? echo $datatab[$j]['sku'];?>&product_id=<? echo $datatab[$j]['product_id'];?>&action=listingusaADMIN"><strong>Modifier</strong></a> 
				</td>
				</tr>
				<tr>
				<td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white;">
					<span style="font-weight: bold;">Location:</span><br>
				</td>
				<td style="vertical-align:  middle;font-weight: bold;">
					<?echo $datatab[$j]['location'];?></td>
				</td>
				</tr>
				<tr>
				<td style="vertical-align:  middle; width: 160px; font-weight: bold; background-color: rgb(102, 0, 0); color: white;">
					Quantite en stock:<br>
				</td>
				<td style="vertical-align:  middle; width: 174px;font-weight: bold;"><?echo $datatab[$j]['quantity'];?>
				</td>
				</tr>
				</tbody>
			</table>
		</td>
		<?if ($j==2 ||$j==5 || $j==8) echo '</tr>	<tr style="font-weight: bold;">';?>
	<?}?>
	</tr>
</table>
		
	
<?}?>
</body></html>

<?
function cloneritem ($rowverif, $typeetat,$numetat,$skuachanger,$default_product_id) {
	
 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.product_id = "'.$default_product_id.'"';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req); 
	
			if($data['length']==0 || $data['height']==0 || $data['width']==0 || $data['weight']==0)$erreurvide="***Champs vide***";
		if(($rowverif==0 && $erreurvide=="" )|| $rowverif=="OK" ){
			//echo "ALLO";
/* 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.sku = "'.$_POST['sku'].'"';
			echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req); */
			$sql6 = 'INSERT INTO `oc_product` (`weight_class_id`,`length_class_id`,`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
			$sql6 .='`weight`, `height`, `length`,`width`,`color`,`ean`,`asin`,`tax_class_id`, `status`, `condition_id`,`invoice`,`image`)';
			$sql6 .=' VALUES ("7","3","1","'.strtoupper($data['model']).'", "'.substr($data['sku'],0,12).$typeetat.'", "'.$data['sku'].'", "'.strtoupper($data['model']).'","0","'.$data['manufacturer_id'].'",';
			$sql6 .='"'.$data['weight'].'", "'.$data['height'].'", "'.$data['length'].'", "'.$data['width'].'", "'.$data['color'].'", "'.$data['ean'].'","'.$data['asin'].'","9", "0", "'.$numetat.'","'.$data['invoice'].'","'.$data['image'].'");';
			//echo $sql6.'<br><br>';
			
			$req6 = mysql_query($sql6);
			$product_id= mysql_insert_id();
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			$categorynametab=explode('>', $_POST['categoryarbonum']);
			
			//description
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];

			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Description :</h2>';
			if($_POST['andescription']!='')$description.=$_POST['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($data['name']).'<br><strong>Model : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			if($data['color']=="")$data['color']="N/A";
			$description.='<strong>Color : </strong>'.strtoupper($data['color']).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$numetat;
//echo $sql2;
			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
			$description.=strtoupper($data2['condition'])."<br>";
	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$data['product_id'];
			$req2= mysql_query($sql2); 
			while($data2 = mysql_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			//echo $description;
			//$data['name']=htmlentities($data['name'], ENT_QUOTES);
			$description=htmlentities($description, ENT_QUOTES);
			
			//
			$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$product_id."', '".addslashes(strtoupper($data['name']))."', '".addslashes(strtoupper($data['name']))."', '1','".$description."')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
			$req7 = mysql_query($sql7);
			$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$data['product_id'].'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_image` (`product_id`, `image`) VALUES ('".$product_id."', '".$data3[image]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}
			$sql3 = 'SELECT * FROM `oc_product_to_category` where product_id = "'.$data['product_id'].'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '".$data3[category_id]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}

			$sql7 = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			
			// mettre a jour les dim et poids 
			

		}
}

function modifitem ($skuachanger,$default_product_id,$typeetat,$numetat) {

			//
			if($default_product_id=="")$default_product_id=$skuachanger;
			//info de item a modifier
 			$sql8 = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.sku = "'.$skuachanger.'"';
			//echo $sql8."<br>";
			$req8 = mysql_query($sql8);
			$data8 = mysql_fetch_assoc($req8); 
			
			$product_id= $data8['product_id'];
			if($product_id=="")cloneritem ("OK", $typeetat,$numetat,substr($skuachanger,0,12),$default_product_id);
			
			//info a prendre
 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.product_id = "'.$default_product_id.'"';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req); 
			$name=$data['name'];
						
			$sql2 = 'UPDATE `oc_product` SET price="'.$data['price'].'",`model`="'.strtoupper($data['model']).'",`color`="'.strtoupper($data['color']).'", `upc`="'.substr($data['sku'],0,12).'"';
			$sql2 .=', `mpn` = "'.strtoupper($data['model']).'",`manufacturer_id` = "'.$data['manufacturer_id'].'", image="'.$data['image'].'"';
			$sql2 .=', `weight`="'.$data['weight'].'",`height`="'.$data['height'].'"';
			$sql2 .=', `width`="'.$data['width'].'",`length`="'.$data['length'];
			$sql2 .='" WHERE product_id='.$product_id;
			//echo $sql2.'<br><br>';
			
			$req2 = mysql_query($sql2);
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			// a verifer $categorynametab=explode('>', $_POST['categoryarbonum']);
			
			//description
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];

			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$brand=$data2['name'];
			//Anglais
			$description='<h2>Description :</h2>';
			$descriptionf='<h2>Description :</h2>';
			// averifer
			//if($data['andescription']!='')$description.=$data['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($name).'<br><strong>Model : </strong>'.strtoupper($data['model']);
			$descriptionf.='<strong>Titre : </strong>'.strtoupper($name).'<br><strong>Mod&egravele : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			$descriptionf.='<br><strong>Marque : </strong>'.strtoupper($data2['name']).'<br>';
			if($data['color']=="")$data['color']="N/A";
			$description.='<strong>Color : </strong>'.strtoupper($data['color']).'<br>';
			$descriptionf.='<strong>Couleur : </strong>'.strtoupper($data['color']).'<br>';
			$description.='<strong>UPC : </strong>'.substr($data['upc'],0,12).'<br>';
			$descriptionf.='<strong>UPC : </strong>'.substr($data['upc'],0,12).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$descriptionf.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$descriptionf.='<strong>Poids : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where language_id=1 and condition_id='.$numetat;
//echo $sql2;
			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
			$description.=strtoupper($data2['condition'])."<br>";
			$conditionname=strtoupper($data2['name']);

$conditionsupp=explode(',', $data8['condition']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
		//echo $i;		
	}
}			
			
			if($data8['accessory']!="" && $data8['accessory']!=",")$description.='</p><h2>Accessories Included :</h2><p>';
$conditionsupp=explode(',', $data8['accessory']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}
}		
//echo $_POST['test'];		
		if($data8['test']!="" && $data8['test']!=",")$description.='</p><h2>Tests - Repairs Done :</h2><p>';
$conditionsupp=explode(',', $data8['test']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}
}	
//francais
			$sql3 = 'SELECT * FROM `oc_condition` where language_id=2 and condition_id='.$numetat;
//echo $sql3;
			// on envoie la requête
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$descriptionf.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data3['name']).'</strong><br>';
			$descriptionf.=strtoupper($data3['condition'])."<br>";
			$conditionnamef=strtoupper($data3['name']);

			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$default_product_id.'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			$descriptionf.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
			$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$default_product_id;
			$req2= mysql_query($sql2); 
			//echo $sql2."<br>";
			while($data2 = mysql_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
					$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			$descriptionf.='</tbody></table><br>';
			//echo $description;
			// a verifier   $_POST['name_product']=htmlentities($_POST['name_product'], ENT_QUOTES);
			$description=htmlentities($description, ENT_QUOTES);
			//$descriptionf=htmlentities($descriptionf, ENT_QUOTES);
			
			//
		// modification toutjours de description
$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,tag="'.$conditionname.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionname.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionname." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$description.'", meta_description="Liquidation of this '.addslashes(strtoupper($name)).' '.$conditionname.' at the lowest price of $'.doubleval ($data['price']).' USD Model: '.$data['model'].' UPC:'.$data['upc'].'" WHERE language_id=1 and `product_id` ='.$product_id;
			//echo $sql2;
			$req2 = mysql_query($sql2);
			$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,tag="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionnamef." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$descriptionf.'", meta_description="Liquidation de l\'item '.addslashes(strtoupper($name)).' '.$conditionnamef.' au meilleur prix de $'.doubleval ($data['price']).' USD Modèle: '.$data['model'].' UPC:'.$data['upc'].'"  WHERE language_id=2 and `product_id` ='.$product_id;
			//echo $sql2;
			$req2 = mysql_query($sql2);
			
			
			echo $sql7."<br>";
			$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
			$req7 = mysql_query($sql7);
			// enlever les photos
			$sql3 = 'DELETE FROM `oc_product_image` where product_id = "'.$product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			
			$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$default_product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);


			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_image` (`product_id`, `image`) VALUES ('".$product_id."', '".$data3[image]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}
		
// enlever les categories
/* 			$sql3 = 'DELETE FROM `oc_product_to_category` where product_id = "'.$product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3); */

			
			$sql3 = 'SELECT * FROM `oc_product_to_category` where product_id = "'.$default_product_id.'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '".$data3[category_id]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}



			


}
?>