<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';include 'function_load.php';



if ($_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
}
//echo $_GET['clone'];
if ($_GET['sku']!="" && $_GET['category_id']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	$_POST['category_id']=$_GET['category_id'];
	echo "allo";
}
if ((string)$_POST['sku'] =="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")(string)$_POST['sku'] =$_POST['skucheck'];
if($_POST['new_ebay_id']>0){
		$sql2 = 'UPDATE `oc_product`SET ebay_id="'.$_POST['new_ebay_id'].'",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$_POST['new_ebay_listing']="yes";
		
}

if ((string)$_POST['sku'] !="" && $_POST['product_id']>0 && $_GET['update']="yes"){
	
	if ($_POST['manufacturer_id']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer_id'];

 		if (isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id']= mysqli_insert_id($db);
			$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			//echo $_POST['manufacturer_id'];
			$_POST['manufacturersupp']="";
			
		}
		if ($_POST['manufacturer_recom']!=""){
			$_POST['manufacturer_id']=$_POST['manufacturer_recom'];
		} 
		
 			if (isset($_POST['category_id']) && $_POST['category_id']!=""){
				
					$sql3 = 'SELECT product_id FROM `oc_product` where upc = "'.(string)$_POST['upc'] .'"';
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){
								$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$_POST['category_id'];
						//echo $sql;
								$req = mysqli_query($db,$sql);
								$data = mysqli_fetch_assoc($req);
								$categoryname=$data['name'];
								//echo $categoryname;
								//echo $data['parent_id'];
								while($data['parent_id']>0){
									$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$data3['product_id']."', '".$data['category_id']."')";
									//echo $sql2."<br>";
									$req2 = mysqli_query($db,$sql2);
									$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data['parent_id'];
						//echo $sql;
									$req = mysqli_query($db,$sql);
									$data = mysqli_fetch_assoc($req);

								}
									$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$data3['product_id']."', '".$data['category_id']."')";
									//echo $sql2."<br>";
									$req2 = mysqli_query($db,$sql2);
						

							//if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;}

					}		
			} 
		
					$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'];

					// on envoie la requ�te
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					
						$sql3 = 'SELECT * FROM `oc_product` ';
							if($_GET['clone']==""){
								$sql3 .=' WHERE `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'" or `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'no" or `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'r"';
							}else{
								$sql3 .=' WHERE `oc_product`.`sku` ="'.(string)$_POST['sku'] .'"';
							}
							//echo $sql3;
							$req3 = mysqli_query($db,$sql3);
							$tabnocollector=explode("@",$_POST['nocollector']);
							$_POST['condition_supp'].=$tabnocollector[0];
							$_POST['condition_supp_fr'].=$tabnocollector[1];
							
								while($data3 = mysqli_fetch_assoc($req3)){			
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.htmlspecialchars_decode(addslashes($_POST['description_supp'])).'",`color`="'.htmlspecialchars_decode(addslashes(strtoupper($_POST['color']))).'",`description_mod`=1,`name`="'.addslashes(strtoupper($_POST['name'])).'",`condition_supp`="'.addslashes($_POST['condition_supp']).'",`accessory`="'.addslashes($_POST['accessory']).'",`test`="'.addslashes($_POST['test']).'" WHERE `language_id`=1 and `product_id` ='.$data3['product_id'];
									//echo $sql2;
										$req2 = mysqli_query($db,$sql2); 
										$sql2 = 'UPDATE `oc_product_description` SET `description_supp`="'.htmlspecialchars_decode(addslashes($_POST['description_suppfr'])).'",`color`="'.htmlspecialchars_decode(addslashes(strtoupper($_POST['colorfr']))).'",`description_mod`=1,`name`="'.htmlspecialchars_decode(addslashes($_POST['namefr']), ENT_QUOTES).'",`condition_supp`="'.addslashes($_POST['condition_supp_fr']).'",`accessory`="'.addslashes($_POST['accessoryfr']).'",`test`="'.addslashes($_POST['testfr']).'" WHERE `language_id`=2 and `product_id` ='.$data3['product_id'];
									//echo $sql2;
										$req2 = mysqli_query($db,$sql2); 
										mise_en_page_description($connectionapi,$data3['product_id'],$db);
										
										/* if ($_POST['processing']=="oui"){ */
											if($data3['marketplace_item_id']>1)
											{
												$result=revise_ebay_product($connectionapi,$data3['marketplace_item_id'],$data3['product_id'],"non",$db);
												//$result = json_encode($new); 
												
												//$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],$updquantity,$db);
												$json = json_decode($result, true); 
												//echo "<br>mise a jour<br>";
												//print("<pre>".print_r ($json,true )."</pre>");
												if($json["Ack"]=="Failure"){
													$resultebay.="ERREUR: ".$json["Errors"]["ShortMessage"];
												}elseif($json["Ack"]=="Warning"){
													$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
												}
											}
										/* } */
								}


							$new=1;
			

		if($_POST['weight2']<16&&$_POST['weight']>0){
			$weight=$_POST['weight']+($_POST['weight2']/16);
		}elseif($_POST['weight2']==16){
			$weight=.999999;
		}else{
			$weight=$_POST['weight2']/16;
		}
		if($_POST['manufacturer_id']=="")$_POST['manufacturer_id']=0;
		//echo $weight;
		$sql2 = 'UPDATE `oc_product` SET `model`="'.htmlspecialchars_decode(strtoupper($_POST['model'])).'", `upc`="'.(string)$_POST['upc'].'"';
		$sql2 .=', `mpn` = "'.htmlspecialchars_decode(strtoupper($_POST['model'])).'",`manufacturer_id` = "'.$_POST['manufacturer_id'].'"';
		$sql2 .=', `weight`="'.$weight.'",`height`="'.$_POST['height'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'].'",ebay_last_check="2020-09-01",`remarque_interne`="'.htmlspecialchars_decode($_POST['remarque_interne']);
		if($_GET['clone']==""){
			$sql2 .='" WHERE `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'" or `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'no" or `oc_product`.`sku` ="'.substr((string)$_POST['sku'] ,0,12).'r"';
		}else{
			$sql2 .='" WHERE `oc_product`.`sku` ="'.(string)$_POST['sku'] .'"';
		}
		//echo $sql2.'<br><br>';

		//echo $sql2.'<br><br>';	  
		$req2 = mysqli_query($db,$sql2); 

		
		 
		//mettre a jour la description recu de algopix
					

}
 if (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
			
			//echo (string)$_POST['sku'] ;
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where language_id=1 and oc_product.product_id=oc_product_description.product_id and sku = "'.(string)$_POST['sku'] .'"';
			$sql = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=1 and P.product_id=PD.product_id and P.sku = "'.(string)$_POST['sku'] .'"';
			$req = mysqli_query($db,$sql);
			
			//echo $sql;
			$data = mysqli_fetch_assoc($req);
			$_POST['name']=htmlspecialchars_decode ($data['name']);
			

	//echo $sql;
	//echo $sql3;
			//$req3 = mysqli_query($db,$sql3);
			//$_POST['nbvariance']=mysqli_num_rows($req3);
			$sql3 = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=2 and P.product_id=PD.product_id and P.sku = "'.(string)$_POST['sku'] .'"';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$_POST['namefr']=$data3['name'];
			if(mysqli_num_rows($req3)==0){
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$data['product_id']."', '', '', '2','')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";	
			}
			(string)$_POST['sku'] =$data['sku'];
			$_POST['etat']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			if($data['color_item']!=""){
				$_POST['color']=$data['color_anc'];
			}else{
				$_POST['color']=$data['color_item'];
			}
			
			$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			if($_POST['manufacturer_id']=="") $_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			(string)$_POST['upc']=(string)$data['upc'];
			$_POST['price']=$data['price'];
			$_POST['priceebaysold']=$data['priceebaysold'];
			$_POST['priceterasold']=$data['priceterasold'];
			$_POST['priceebaynow']=$data['priceebaynow'];
			$_POST['quantityinventaire']=$data['quantity'];
			$_POST['price']=$data['price'];
			$weighttab=explode('.', $data['weight']);
			$_POST['weight']=$weighttab[0];
			$_POST['weight2']=substr($weighttab[1],0,4)*16/10000;
			$_POST['length']=$data['length'];
			$_POST['width']=$data['width'];
			$_POST['height']=$data['height'];
			$_POST['location']=$data['location'];
			$_POST['invoice']=$data['invoice'];
			//$_POST['category_id'] ="";
			//$_POST['categoryarbonum']="";
			$_POST['accessory']=$data['accessory'];
			$_POST['condition_supp']=$data['condition_supp'];
			$_POST['test']=$data['test'];
			$_POST['description_supp']=$data['description_supp'];
			$_POST['color']=$data['color_item'];
			
			$_POST['accessoryfr']=$data3['accessory'];
			$_POST['condition_supp_fr']=$data3['condition_supp'];
			$_POST['testfr']=$data3['test'];
			$_POST['description_suppfr']=$data3['description_supp'];
			$_POST['colorfr']=$data3['color_item'];
			
			$_POST['image']=$data['image'];
			$_POST['remarque_interne']=$data['remarque_interne'];
			$sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
			$_POST['categoryname']=$data4['name'];
			$_POST['category_id']=$data4['category_id'];
			$_POST['category_id']=$data4['category_id'];

 if//((($_POST['namefr']==""&& $_POST['name']!="")||($_POST['colorfr']==""&& $_POST['color']!="")) )// && $_GET['clone']==""
// 	(($_POST['namefr']==""&& $_POST['name']!="")||($_POST['accessoryfr']==""&& $_POST['accessory']!="")||($_POST[condition_supp_fr]==""&& $_POST['condition_supp']!="")||($_POST['testfr']==""&& $_POST['test']!="")||($_POST['colorfr']==""&& $_POST['color']!="")||$_POST['condition_supp']!="," )

	(($_POST['namefr']==""&& $_POST['name']!="")||($_POST['accessoryfr']==""&& $_POST['accessory']!="")||($_POST['condition_supp_fr']==""&& $_POST['condition_supp']!="")||($_POST['testfr']==""&& $_POST['test']!="")||($_POST['colorfr']==""&& $_POST['color']!="") )
			{
				//echo "oui";
				//$_POST['namefr']=translate($_POST['name'],'fr','en');
			header("location: ".$GLOBALS['WEBSITE']."/translate.php?phoenixsupplies=non&product_id=".$_POST['product_id']."&targetLanguage=fr&clone=".$_GET['clone']."&sku=".(string)$_POST['sku']); 
				//$bypasstranslate="oui"
			}
/* 			if(($_POST['name']==""&& $_POST['namefr']!="")||($_POST['accessory']==""&& $_POST['accessoryfr']!="")||($_POST['condition_supp']==""&& $_POST[condition_supp_fr]!="")||($_POST['test']==""&& $_POST['testfr']!="")||($_POST['description_supp']==""&& $_POST['description_suppfr']!="")||($_POST['color']==""&& $_POST['colorfr']!=""))
			{
				//echo "ouifr";
				//$_POST['namefr']=translate($_POST['name'],'fr','en');
				//header("location: '.$GLOBALS['WEBSITE'].'/translatefr.php?product_id=".$_POST['product_id']."&targetLanguage=en&clone=".$_GET['clone']."&sku=".(string)$_POST['sku']); 
				//$bypasstranslate="oui"
			} */
	

			$new=1;
	
}
		if ($_POST['processing']=="oui" && $_POST['frenchcheck']=="oui"){
			header("location: listing.php?insert=oui&sku=".substr ((string)$_POST['upc'],0,12)); 
			exit(); 
		}
?>

<html>
<head> 
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>
	<script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/bootstrap/js/bootstrap.min.js"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/common.js" type="text/javascript"></script>

    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">


<form id="form-product" class="form-horizontal" action="listingitem.php?clone=<?echo $_GET['clone'];?>&update=yes&action=<?echo $_GET['action'];?>" method="post">
  <table style="text-align: left; width: 1000px; height: 12%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; height: 50px;">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
	 </tbody>
	 </table>
 <table style="text-align: left; width: 1000px; height: 12%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="1">
	 <tr>
	 <td style="vertical-align: text-top;">
	  <table style="text-align: left; width: 200px; height: 1210px; margin-left: auto; margin-right: auto;" border="1" cellpadding="0" cellspacing="0">
<tbody>
<tr>
       <td style=" vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px; height: 50px;">
	   <a href="listing.php?sku=<?echo (string)$_POST['upc'];?>" >Menu listing</a> 		

        </td>
</tr>
<tr>
			<td style=" vertical-align: center;  background-color: #030282; color: white; text-align: center;  width: 200px; height: 50px;" >
			Photo principale: <br>
			</td>
</tr>
	 		<td  style="vertical-align: text-top;  text-align: center; height: 200px; width: 200px;">
			<table>
			<tr>
			<td>			<?
			if($_POST['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$_POST['image'].'" width="200">';
			?>
        </td>
</tr>
<tr>
			<td style=" vertical-align: center;  background-color: #030282; color: white; text-align: center;  width: 200px; height: 50px;" >
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			if(mysqli_num_rows($req2)>0)echo 'Photos additionelles: ';
			
?>

			</td>
</tr>
	 		<td  style="vertical-align: text-top;  text-align: center; height: 928px; width: 200px;">			

<?   

			
			while($data2 = mysqli_fetch_assoc($req2))
			{

				?>
				<br>
				<img src="https://www.phoenixliquidation.ca/image/<? echo $data2['image'];?>" width="150"><br>
				
				<?
			}
			
?>
<br>
			<a href="multiupload.php?sku=<?echo (string)$_POST['sku'] ?>" >Modifier Photos</a>
			</td>
			</tr>
			</table>
			</td>

</tr>
	 
</tbody>
	 </table>
	 </td>

	 <td>
	  <table style="text-align: top; width: 770px; height: 25%; margin-left: auto; margin-right: auto;" border="1" cellpadding="0" cellspacing="0">
<tbody>
      <tr>
	
        <td colspan="4" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Modification Produit (<?echo $_POST['product_id']?>)</h1><?/*echo '<font color="red">'.$erreurvide.'</font>';*/?>
		</td>
     </tr>
	 		<?if($resultebay!=""){?>
			   <tr>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;background-color: 
		
		red
		;color:white">
			<?echo $resultebay;?>
        </td>
 
	  </tr>
		<?}?>
	 		<tr>

			 <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px; ">
			 SKU: 
			 <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
        </td>
		        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; width: 400px;text-align: center;">
<?echo (string)$_POST['sku'] ;?>

		</td>
     </tr>
	        <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		UPC:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?if($_POST['changeupc']=="changer"){?><input id="price"  type="text" name="newupc" value="<?echo $_POST['newupc'];?>" size="30" />
		<input type="hidden" name="upc" value="<?echo $_POST['upc']	;?>"/>	<?}else{?>
		<?echo (string) ($_POST['upc']);?>
		<br><a href="modifieritemusa.php?changeupc=oui&product_id_r=<? echo $_POST['product_id_r'];?>&product_id_no=<? echo $_POST['product_id_no'];?>&product_id=<? echo $_POST['product_id'];?>">Corriger UPC de l'item</a>
		<?}?>
		
		
		</td>

		</tr>
	 		<tr>
        <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Nu Ebay:
		</td>
        <?if($_POST['marketplace_item_id']==0||$_POST['marketplace_item_id']==""){?>
		<td colspan="1" rowspan="1" style="background-color:red; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
			<?
				echo $_POST['marketplace_item_id'];
?>

		<?}else{?><td colspan="1" rowspan="1" style="background-color:green; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
		<?echo $_POST['marketplace_item_id'];?><?}?> 
		</td>

		</tr>

	<tr>

        <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			Titre anglais:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; ">
			<input id="name"  type="text" name="name" value="<?echo ($_POST['name']);?>" maxlength="80" />
		</td>
	</tr>

      <tr>
         <td style="vertical-align:  middle; height: 50px; background-color: <?if($_POST['frenchcheck']=="oui"){?>#030282<?}else{?>red<?}?>; color: white; width: 200px;">
		 Titre francais: <input type="checkbox" name="frenchcheck" value="oui" <?if($_POST['frenchcheck'])echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; ">
		<input id="namefr"  type="text" name="namefr" value="<?echo ($_POST['namefr']);?>" maxlength="255" /></h3>   
		</td>

        
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 200px;">
		Mod&egrave;le:
		</td>
        <td style="vertical-align:  middle; height: 25px; ">
		<input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" />
		</td>

      </tr>

      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">Manufacturier:<br>
        </td>
        <td style="vertical-align:  middle; ">
<select name="manufacturer_id">
			<option value="" selected></option>
<?
			$sql = 'SELECT * FROM `oc_manufacturer` order by name';

			// on envoie la requ�te
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$brandrecom="";
			while($data = mysqli_fetch_assoc($req))
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
						$test1=strtolower ($_POST['name']);
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
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
		Categorie:
        </td>
        <td style="vertical-align:  middle; <?if($_POST['categoryname']=="")echo "background-color: #ff0000";?>">
		<input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> 
		<?if ($_POST['categoryname']!=""){?><br>La categorie trouvee est : <b><span style="color: #ff0000;"><?echo $_POST['categoryname'];?></span></b><?}?>
		</td>

      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 13px; background-color: #030282; color: white; width: 200px;">Couleur</td>
        <td style="vertical-align:  middle; height: 13px; ">
		<table>
		  <tr>
		  <td>Anglais</td>
			<td width="95%"><input type="text" name="color" value="<?echo $_POST['color'];?>" maxlength="80" /></td>
			
		  </tr>
		  <tr>
			
			<td>Francais</td>
			<td><input type="text" name="colorfr" value="<?echo $_POST['colorfr'];?>" maxlength="80" /></td>
		  </tr>
		</table>
		</td>

      </tr>
	        <tr>
        <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 200px;">Dimension:</td>
        <td style="vertical-align:  middle; height: 15px; ">
			<table>
		  <tr>
			<td>
			Largeur 
			</td>
				<td>
				<input id="length" class="" type="text" name="length" value="<?echo intval($_POST['length']);?>" maxlength="5" />
				</td>
					</tr>
				<tr>
				<td>
			
			Profondeur 
			</td>
				<td>
				<input id="width" class="" type="text" name="width" value="<?echo intval($_POST['width']);?>" maxlength="5" />
				</td>
								</tr>
				<tr>
				<td>

			Hauteur 
			</td>
				<td><input id="height" class="" type="text" name="height" value="<?echo intval($_POST['height']);?>" maxlength="5" />
				</td>
			</tr>
			</table>
		</td>	
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
		Poids:
		</td>
        <td style="vertical-align:  middle; text-align: center;height: 16px; ">
		<table style="vertical-align:  middle; text-align: center;height: 16px; ">
		  <tr>
			<td>Lbs
			</td>	  
			<td>
				<input id="weight" class="" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" />
				</td>
											</tr>
				<tr>	
		<td>Oz
		</td>
			
			<td>
				<input id="weight2" class="" type="text" name="weight2" value="<?echo $_POST['weight2'];?>" maxlength="5" />
		</td>

		</tr>
</table>

      </tr>
	  	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>

      <tr>
        <td style="vertical-align:  middle; height: 74px; background-color: #030282; color: white; width: 200px;">Description:</td>
        <td style="vertical-align:  middle; height: 74px; ">
		Anglais:<br><textarea  name="description_supp" rows="10" cols="50" placeholder="Description" id="input-description1" class="form-control"><?echo $_POST['description_supp'];?></textarea><br>
		Francais:<br><textarea name="description_suppfr" rows="10" cols="50" placeholder="Description en francais" id="input-description1" class="form-control"><?echo $_POST['description_suppfr'];?></textarea>
			<script>
    tinymce.init({
		selector: 'textarea',
		plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
		toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
		toolbar_mode: 'floating',
		tinycomments_mode: 'embedded',
		tinycomments_author: 'Author name',		
		});  
	</script>
		</td>

      </tr>

	    <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
		
		
		Conditions suppl&eacute;mentaires:
		</td>
        <td style="vertical-align:  middle; height: 16px; ">
		<input type="checkbox" name="nocollector" value="This product is not for collectors@Ce produit n'est pas pour les collectionneurs
">Pas pour collectionneur<br>
<input type="checkbox" name="covermulti" value="Product Multi language, Front and Back cover are BILLINGUAL English and French@Produit Multi langue. Les couvertures avant et arri�re sont BILLINGUES en anglais et en fran�ais">Les couvertures avant et arri�re sont BILLINGUES
		<table>
		  <tr>
		  <td>Anglais</td>
			<td width="95%"><input type="text" name="condition_supp" value="<?echo $_POST['condition_supp'];?>" maxlength="300" /></td>
			
		  </tr>
		  <tr>
			
			<td>Francais</td>
			<td><input type="text" name="condition_supp_fr" value="<?echo $_POST['condition_supp_fr'];?>" maxlength="300" /></td>
		  </tr>
		</table>
	</td>
	</tr>
	    <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
			
<?
?>
			Accesoires suppl&eacute;mentaires:
		</td>
        <td style="vertical-align:  middle; height: 16px; ">
				
		<table>
		  <tr>
			<td>Anglais</td>
			<td width="95%"><input type="text" name="accessory" value="<?echo $_POST['accessory'];?>" maxlength="300" /></td>
		  </tr>
		  <tr>
			
			<td>Francais</td>
			<td><input type="text" name="accessoryfr" value="<?echo $_POST['accessoryfr']?>" maxlength="300" /></td>
		  </tr> 
		</table>
		</td>
		</tr>
			  <tr>
		    <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
		
<?
?>
		Tests suppl&eacute;mentaires :
		
	  		</td>
			<td style="vertical-align:  middle; height: 16px; ">
	 		<table>
		  <tr>
		  <td>Anglais</td>
			<td width="95%"><input type="text" name="test" value="<?echo $_POST['test'];?>" maxlength="300" /></td>
			
		  </tr>
		  <tr>
			
			<td>Francais</td>
			<td><input type="text" name="testfr" value="<?echo $_POST['testfr'];?>" maxlength="300" /></td>
		  </tr> 
		</table>
			</td>
		</tr>
		<tr>
		 <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
		REMARQUE INTERNE : 
		</td>
		<td style="vertical-align:  middle; height: 16px; ">
		<input id="remarque_interne"  type="text" name="remarque_interne" value="<?echo $_POST['remarque_interne'];?>" maxlength="255" />
		</td>
		</tr>
	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>
	  
    </tbody>
  		
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape'];?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
		<input type="hidden" name="upc" value="<?echo (string)$_POST['upc'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner'];?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		<input type="hidden" name="etat" value="9" />
		<input type="hidden" name="nbvariance" value="<?echo $_POST['nbvariance'];?>" />
		<input type="hidden" name="sku" value="<?echo (string)$_POST['sku'] ;?>" />
		<input type="hidden" name="categoryname" value="<?echo $_POST['categoryname'];?>" />
		
		<input type="hidden" name="quantitytotal" value="<?echo $_POST['quantitytotal'];?>"/> 
		<input type="hidden" name="ebay_id_refer" value="<?echo $_POST['ebay_id_refer'];?>"/> 
		<input type="hidden" name="new_ebay_listing" value="<?echo $_POST['new_ebay_listing'];?>"/> 
  </table>
  </td>
</tr>
</table>
</body>
</html>

<? 
//echo $_GET['clone'];
// on ferme la connexion &agrave; mysql 
mysqli_close($db); ?>