<?

include 'connection.php';
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");

include $GLOBALS['SITE_ROOT'].'translatenew.php';
//print_r($_POST['accesoires']);
		if ($_FILES['imageprincipale']['size']>0){
		//delete_photo($_POST['product_id'],"principal",$db);

		//print("<pre>".print_r ($_FILES['imageprincipale'],true )."</pre>");
			$_POST['imageprincipale']=upload_image(0,1,$db);

		}

if (isset($_GET['upc']) && $_GET['upc'] != ""){
	(string)$_POST['upc']=(string)$_GET['upc'];
	$_POST['etape']=0;

		if($_POST['name']=="" || !isset($_POST['name'])){
			$_POST['manufacturer_id']=0;
			$_POST['upctemp']=get_from_upctemp($_GET['upc']);
			$result_upctemp=json_decode($_POST['upctemp'],true);
	//echo "upc";
			$_POST['priceusd']=$result_upctemp['items'][0]['highest_recorded_price'];
			$_POST['priceusdtmp']=$result_upctemp['items'][0]['highest_recorded_price'];
			$_POST['pricecad']=$_POST['priceusd']*1.34;
			$_POST['pricecadtmp']=$_POST['priceusd']*1.34;
			
			$_POST['brand']=$result_upctemp['items'][0]['brand'];
			if($result_upctemp['items'][0]['model']=="" || !isset($result_upctemp['items'][0]['model'])){
				$_POST['model']="None";
			}else{
				$_POST['model']=$result_upctemp['items'][0]['model'];
			}
			
			
			//$search = array('lb', 'lbs');
			//$_POST['weight']=str_replace($search,"",$result_upctemp['items'][0]['weight']);
			
			
			$result_ebay=find_bestprice_ebay($connectionapi,$_POST['name'],$_POST['upc'],1,1,'');
			if($result_ebay['ebay_id_a_cloner']>0){
				$_POST['ebay_id_a_cloner']=$result_ebay['ebay_id_a_cloner'];
				$_POST['price_with_shipping']=$result_ebay['price_with_shipping']-.05+5000;//$result_ebay['price_with_shipping']-.05;
				$_POST['category_id']=$result_ebay['category_id'];
				$_POST['categoryname']=$result_ebay['categoryname'];
				$_POST['name']=addslashes($result_ebay['name']);
				$_POST['weight']=$result_ebay['weight'];
				$_POST['weight2']=$result_ebay['weight2'];
				$_POST['length']=$result_ebay['length'];
				$_POST['width']=$result_ebay['width'];
				$_POST['height']=$result_ebay['height'];
				if($result_ebay['model']!="")
					$_POST['model']=$result_ebay['model'];
				if($result_ebay['brand']!="")
					$_POST['brand']=$result_ebay['brand'];
				if($result_ebay['color']!="")
					$_POST['color']=$result_ebay['color'];
			}else{
				$_POST['name']=addslashes($result_upctemp['items'][0]['title']);
				$_POST['price_with_shipping']=9999;
			}
			$_POST['description_supp']=addslashes($result_upctemp['items'][0]['description']);
			$_POST=translate_field($_POST);
			$_POST['name']=stripslashes($_POST['name']);
			$_POST['description_supp']=stripslashes($_POST['description_supp']);
			
			$sql2 = 'SELECT * FROM `oc_manufacturer` where name like "%'.$_POST['brand'].'%" AND name not like ""';
			//echo $sql2;
					// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			if($data2['manufacturer_id']){
				$_POST['manufacturer_id']=$data2['manufacturer_id'];
			}
		}	
	}
	//$_POST['upctemp']=get_from_upctemp($_GET['upc']);
	//echo $_POST['etat'];
if($_POST['priceusd']<>$_POST['priceusdtmp']){
	$_POST['priceusdtmp']=$_POST['priceusd'];
	$_POST['pricecadtmp']=$_POST['priceusd']*1.34;
	$_POST['pricecad']=$_POST['priceusd']*1.34;
}
if($_POST['pricecad']<>$_POST['pricecadtmp']){
	$_POST['priceusdtmp']=$_POST['pricecad']/1.34;
	$_POST['priceusd']=$_POST['pricecad']/1.34;
	$_POST['pricecadtmp']=$_POST['pricecad'];
}
	
if ($_GET['condition_insert'] != ""){
	$_POST['condition_insert']=$_GET['condition_insert'];
	$_POST['etape']=0;
	}	
//print("<pre>".print_r ($_POST,true )."</pre>");
//translate('Hello my name is Jonathan');
$upc=(string)$_POST['upc'];
//echo $_POST['frenchcheck'];
if(isset($_POST['ebay_id_a_cloner'])&& $_POST['ebay_id_a_cloner']!="" && ($_POST['processing']=="oui"  
	&& $_POST['frenchcheck']=="oui" && $_POST['englishcheck']=="oui" && $_POST['etat']!=""
	&& $_POST['priceebaycheck']=="oui") && $_POST['pricedetailcheck']=="oui" && $_POST['dimensioncheck']=="oui"
	&& $_POST['poidscheck']=="oui" && $_POST['infosuppcheck']=="oui" && $_POST['manufacturer_id']!=""
	&& $_POST['colorcheck']=="oui"){
	
	$ebayresult=get_ebay_product($connectionapi,$_POST['ebay_id_a_cloner']);

	$json=add_ebay_item($connectionapi,$ebayresult,$_POST,$db); 
	//print("<pre>".print_r ($json,true )."</pre>");
}
if((isset($json['ItemID']) && $json['ItemID']!="")|| ($_POST['processing']=="oui"  && $_POST['pas_ebay']=="oui" )){

		$post= array();
		if(isset($json['ItemID'])){
			$_POST['marketplace_item_id']=$json['ItemID'];
		}else{
			$_POST['marketplace_item_id']=0;
		}
		$post= $_POST;
		if(isset($_POST['clone'])||$_POST['clone']!=""){
			$etat=explode(",",$_POST['etat']);
			$espacenb=3-strlen ($etat[1]);
			$espace="";
			//for ($i=1;$i<=$espacenb;$i++){
				$espace.="_";
			//}
			$_POST['sku'][1].=$espace.$_POST['clone'];
		}
			
		$product_id=insert_item_db($connectionapi,$_POST,$db);
		//echo $_POST['sourcecode'];
		if($_POST['sourcecode']!=""){
			//echo "oui";
/* 				unlink($GLOBALS['SITE_ROOT'].'interne/test.txt');
				link($GLOBALS['SITE_ROOT'].'interne/test.txt');
				$fp = fopen($GLOBALS['SITE_ROOT'].'interne/test.txt', 'w');
				fwrite($fp, html_entity_decode("test".$_POST['sourcecode']));  */
			link_to_download($connectionapi,$product_id,$_POST['sourcecode'],"sourcecodenew",$db);
		}elseif($_POST['lien_a_cloner']){
			link_to_download($connectionapi,$product_id,$_POST['lien_a_cloner'],"",$db);
		}
		//unlink($GLOBALS['SITE_ROOT'].'image/' . $data['image']); 
		
										

		//$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$product_id,"non",$db);

		$json = json_decode($result, true); 
		$_POST['info_ebay']=$json;
		if($json["Ack"]=="Failure"){
			$resultebay.="ERREUR: ";//print("<pre>".print_r ($json,true )."</pre>");
		}else/* if($json["Ack"]=="Warning") */{
/* 			echo '<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: \'#mytextarea\'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="ffffff"><a href="pretlister.php?product_id='.$product_id.'" >Retour au MENU</a></body></html>'; */
			header("location: uploadphoto.php?product_id=".$product_id);  
			exit();
		}

 
}
$_POST['condition_supp'].=$tabnocollector[0];
							$_POST['condition_suppfr'].=$tabnocollector[1]; 
							$tabnocollector=explode("@",$_POST['dvdcase']);
							$_POST['condition_supp'].=$tabnocollector[0];
							$_POST['condition_suppfr'].=$tabnocollector[1];
							//if($_POST['category_id']==617){
							//	$_POST['condition_suppfr']='Film et Coffre inclus, Aucun CODE DIGITAL inclus';
							//	$_POST['condition_supp']='Movie and Case included, NO DIGITAL CODE included';
							//		$test=strtoupper($_POST['name']);
							//		$pos=strpos($test,"DIGITAL");
							//		if ($pos === false && $_POST['name']!="") {
							//			$_POST['name']=substr($_POST['name'],0,67);
							//			$_POST['name'].= " (NO DIGITAL)";
							//		}
							//}
						$_POST['sku']=$data['sku'];
	if ($_POST['manufacturer_id']!="" && $_POST['manufacturersupp']=="")

 		if (isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
			
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id']= mysqli_insert_id($db);
			$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
			
			$req2 = mysqli_query($db,$sql2);
			
			$_POST['manufacturersupp']="";
			$_POST['brand']=$_POST['manufacturersupp'];
			
		}
		if ($_POST['manufacturer_recom']!=""){
			$_POST['manufacturer_id']=$_POST['manufacturer_recom'];
		} 
		
 			if (isset($_POST['category_id']) && $_POST['category_id']!=""){
				
					$sql4 = 'SELECT * FROM `oc_category_description` where oc_category_description.category_id="'.$_POST['category_id'].'" and language_id=1';
					//echo $sql;
					$req4 = mysqli_query($db,$sql4);
					$data4 = mysqli_fetch_assoc($req4);
					$_POST['categoryname']=$data4['name'];
					$_POST['category_id']=$data4['category_id'];
			} 
		
					$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'];

					// on envoie la requête
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					$_POST['brand']=$data2['name'];

 if	(($_POST['namefr']==""&& $_POST['name']!="")||($_POST['accessoryfr']==""&& $_POST['accessory']!="")||($_POST['condition_suppfr']==""&& $_POST['condition_supp']!="")||($_POST['testfr']==""&& $_POST['test']!="")||($_POST['colorfr']==""&& $_POST['color']!="") )
			{
				$_POST['name']=addslashes($_POST['name']);
				$_POST['description_supp']=addslashes($_POST['description_supp']);
				$_POST=translate_field($_POST);
				$_POST['name']=stripslashes($_POST['name']);
				$_POST['description_supp']=stripslashes($_POST['description_supp']);
				//print("<pre>".print_r ($_POST,true )."</pre>");
			}

?>

<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="ffffff">
<form action="testing_insertionitem.php?insert=oui&upc=<? echo (string)$_POST['upc'];?>" method="post"enctype="multipart/form-data" name="addroom">

  <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="3" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
      <tr>
	   
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		

        </td>
        <td colspan="3" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
		<h1>Ajouter Produit a tester</h1>
		</td>
     </tr>
	 	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>
	 	 		<?if($resultebay!=""){?>
			   <tr>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;background-color:red;color:white">
			<?echo $resultebay;?>
        </td>
 
	  </tr>
		<?}?>
	  <tr>
	         <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
			 <input type="file" name="imageprincipale" class="ed"><br>
			 	<input id="lien_a_cloner"  type="text" name="lien_a_cloner"  value="<?echo $_POST['lien_a_cloner'];?>" maxlength="255" autofocus><br>
 <?echo '<textarea name="sourcecode" rows="25" cols="5" placeholder="copiez le code source des images a télécharger" id="sourcecode" class="form-control">'.htmlentities($_POST['sourcecode']).'</textarea>';?>
		</td>
	     <td colspan="2" style="vertical-align:  middle; text-align:center;height: 16px; background-color: white; width: 200px;"> 
		 <?if ($json){?>
	<?
			echo '<br><textarea name="json" rows="25" cols="10" placeholder="output ebay" id="info_ebay" disabled >';
			//print("<pre>".print_r ($json,true )."</pre>");
			
			echo '</textarea>';
			?>
<?}else{?>	
		 <?echo browse_ebay($connectionapi,substr((string)$_POST['name'],0,50),(string)$_POST['upc'],"10","10",$db,$_POST['marketplace_item_id']); ?>
<?}?>
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
	  		<?if (!isset($_POST['ebay_id_a_cloner'])||$_POST['ebay_id_a_cloner']=="")$_POST['ebay_id_a_cloner']= $_POST['marketplace_item_id'];?>
	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		 
			Ebay ID à cloner: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" >
			<br><input type="checkbox" name="pas_ebay" value="oui" <?if($_POST['pas_ebay']=="oui"){?>checked<?}?> />	Pas eBay 
		</td>
	</tr>
	<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix de d&eacute;tail: <input type="checkbox" name="pricedetailcheck" value="oui" <?if($_POST['pricedetailcheck']=="oui")echo "checked";?>/> 
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle;  height: 0px;<?if($_POST['pricedetailcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?> ">

		(Prix en dollar)
		<input id="price"  type="text" name="priceusd" value="<?echo number_format($_POST['priceusd'], 2,'.', '');?>" size="10" /> usd
		<input id="price"  type="text" name="pricecad" value="<?echo number_format(($_POST['priceusd']*1.34), 2,'.', '');?>" size="10" /> cad
		</td>
      </tr>
			 <tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		NOTRE Prix EBAY <input type="checkbox" name="priceebaycheck" value="oui" <?if($_POST['priceebaycheck']=="oui")echo "checked";?>/> 
		<td colspan="1" rowspan="1" style="vertical-align: middle; height: 0px; <?if($_POST['priceebaycheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		(finir par <?echo $endprix;?> en dollar am&eacute;ricain)<br><input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping'], 2,'.', '');?>" size="10" />
		</td>
</tr>

	 <tr>
	 		
	  <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		UPC:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?if($_POST['changeupc']=="changer"){?><input id="price"  type="text" name="newupc" value="<?echo $_POST['newupc'];?>" size="30" />
		<input type="hidden" name="upc" value="<?echo $_POST['upc']	;?>"/>	<?}else{?>
		<?echo (string) ($_POST['upc']);?>
		<br><a href="modifierprix.php?changeupc=oui&product_id_r=<? echo $_POST['product_id_r'];?>&product_id_no=<? echo $_POST['product_id_no'];?>&product_id=<? echo $_POST['product_id'];?>">Corriger UPC de l'item</a>
		<?}?>
		
		
		</td>

	</tr>
	

<tr>
			 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			Condition: 
        </td>
	        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; <?if ($_POST['etat']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
			<table>
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9," <?if ($_POST['etat']=="9,"){?>checked<?}?>/> 
				<label class="choice" for="etat_1">NEUF</label></td>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99,NO" <?if ($_POST['etat']=="99,NO"){?>checked<?}?>/> 
				<label class="choice" for="etat_2">Boite Ouverte (GRADE A)</label> <br /></td>
		  </tr>
		  <tr>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2,SR" <?if ($_POST['etat']=="2,SR"){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22,R" <?if ($_POST['etat']=="22,R"){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Boite Ouverte (GRADE B)</label> <br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8,LN" <?if ($_POST['etat']=="8,LN"){?>checked<?}?>/> 
				<label class="choice" for="etat_4">Used - Like New</label> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7,VG" <?if ($_POST['etat']=="7,VG"){?>checked<?}?>/> 
				<label class="choice" for="etat_5">Used - Very Good</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6,G" <?if ($_POST['etat']=="6,G"){?>checked<?}?>/> 
				<label class="choice" for="etat_6">Used - Good</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5,P" <?if ($_POST['etat']=="5,P"){?>checked<?}?>/> 
				<label class="choice" for="etat_7">Used - Poor</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1,FP" <?if ($_POST['etat']=="1,FP"){?>checked<?}?>/> 
				<label class="choice" for="etat_8">For Parts Or For Repair</label> </td>
			<td>Clone:<input type="text" name="clone" value="<?echo $_POST['clone'];?>" maxlength="1" /></td>
		  </tr>
		</table>
				</td>
	</tr>
		<tr>

        <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			Titre anglais: <input type="checkbox" name="englishcheck" value="oui" <?if($_POST['englishcheck']=="oui")echo "checked";?>/> 
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['englishcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
			<input id="name"  type="text" name="name" value="<?echo htmlentities($_POST['name']);?>" maxlength="80" />
		</td>
	</tr>

      <tr>
         <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
		 Titre francais: <input type="checkbox" name="frenchcheck" value="oui" <?if($_POST['frenchcheck']=="oui")echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['frenchcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		<input id="namefr"  type="text" name="namefr" value="<?echo htmlentities(htmlspecialchars_decode ($_POST['namefr']));?>" maxlength="255" /></h3>   
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
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>
      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">Manufacturier:
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['manufacturer_id']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
<select name="manufacturer_id">
			<option value="" selected></option>
<?
			$sql = 'SELECT * FROM `oc_manufacturer` order by name';

			// on envoie la requête
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
							//echo 'allo3';
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
        <td style="vertical-align:  middle; height: 13px; background-color: #030282; color: white; width: 200px;">Couleur
		<input type="checkbox" name="colorcheck" value="oui" <?if($_POST['colorcheck']=="oui")echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['colorcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
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
        <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 200px;">Dimension:
		<input type="checkbox" name="dimensioncheck" value="oui" <?if($_POST['dimensioncheck']=="oui")echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['dimensioncheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
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
		Poids:<input type="checkbox" name="poidscheck" value="oui" <?if($_POST['poidscheck']=="oui")echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['poidscheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		
       
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


 <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>

	  </tr>
    

	    <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 200px;">
		
		
		Infos suppl&eacute;mentaires:<input type="checkbox" name="infosuppcheck" value="oui" <?if($_POST['infosuppcheck']=="oui")echo "checked";?>/> 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px; <?if($_POST['infosuppcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		<input type="checkbox" name="nocollector" value="This product is not for collectors@Ce produit n'est pas pour les collectionneurs
">Pas pour collectionneur<br><input type="checkbox" name="dvdcase" value="Movie and Case included@Film et Coffre inclus
">Film et Coffre inclus
<input type="checkbox" name="covermulti" value="Product Multi language, Front and Back cover are BILLINGUAL English and French@Produit Multi langue. Les couvertures avant et arrière sont BILLINGUES en anglais et en français">Les couvertures avant et arrière sont BILLINGUES
		<table>
		  <tr>
		  <td>Anglais</td>
			<td width="95%"><input type="text" name="condition_supp" value="<?echo $_POST['condition_supp'];?>" maxlength="300" /></td>
			
		  </tr>
		  <tr>
			
			<td>Francais</td>
			<td><input type="text" name="condition_supp_fr" value="<?echo $_POST['condition_suppfr'];?>" maxlength="300" /></td>
		  </tr>
		</table>
	</td>
	</tr>

		  <tr>
	     <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
		 Description: 
		 </td>
        <td style="vertical-align:  middle; height: 74px; ">
		Anglais:<br><textarea  name="description_supp" rows="10" cols="50" placeholder="Description" id="input-description1" class="form-control"><?echo ($_POST['description_supp']);?></textarea><br>
		Francais:<br><textarea name="description_suppfr" rows="10" cols="50" placeholder="Description en francais" id="input-description1" class="form-control"><?echo ($_POST['description_suppfr']);?></textarea>
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

		
		
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape'];?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
		<input type="hidden" name="upc" value="<?echo (string)$_POST['upc'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner'];?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		<input type="hidden" name="condition_insert" value="<?echo $_POST['condition_insert'];?>" />
		<input type="hidden" name="old_token" value="<?echo $_POST['token'];?>" />
		<?/*<input type="hidden" name="upctemp" value="<?echo $_POST['upctemp'];?>" />*/
		?>
		<input type="hidden" name="brand" value="<?echo $_POST['brand'];?>" />
		<input type="hidden" name="imageprincipale" value="<?echo $_POST['imageprincipale'];?>" />
		<input type="hidden" name="priceusdtmp" value="<?echo $_POST['priceusd'];?>" />
		<input type="hidden" name="pricecadtmp" value="<?echo $_POST['priceusd']*1.34;?>" />


    </tbody>
  
  </table>
<?if ($_POST['start'] =="" || !isset($_POST['start'])){?>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_ItemCondition=3&LH_Complete=1&LH_BIN=1","ebaysold");
window.open("https://www.upcitemdb.com/upc/<?echo (string)$_POST['upc'];?>","upcitemdb");
window.open("https://www.ebay.com/sch/i.html?_from=R40&_nkw=<?echo (string)$_POST['upc'];?>&_sacat=0&_sop=15&rt=nc&LH_BIN=1","ebayactive");
/* window.open("https://www.ebay.com/sh/research?marketplace=EBAY-US&keywords=<?echo (string)$_POST['upc'];?>&dayRange=365categoryId=0&tabName=SOLD&tz=America%2FToronto","terapeak");
 */	<?foreach ($result_upctemp['items'][0]['offers'] as $offer){?>
	window.open("<?echo (string)$offer['link'];?>","<?echo (string)$offer['domain'];?>");
	<?}?>

</script>
<?}?>
<input type="hidden" name="start" value="1" />
</form>

</body>
</html>

<?  



// on ferme la connexion à mysql 
mysqli_close($db); ?>