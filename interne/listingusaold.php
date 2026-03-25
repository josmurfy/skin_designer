<?
//print_r($data['accesoires']);
$_POST['sku']=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';

// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
$dateverification = new DateTime('now');
$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
$dateverification = $dateverification->format('Y-m-d');
$dateverification=date_parse ($dateverification);
if($_GET['sku']!="")$_POST['sku']=$_GET['sku'];
if($_POST['changeupc']=="changer"){
		$sql = 'SELECT * FROM oc_product where upc = "'.(string)$_POST['upc'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		while($data = mysqli_fetch_assoc($req)){
			$finsku=substr((string)$data['sku'] ,12,10);
			$sql2 = 'UPDATE `oc_product`SET upc="'.(string)$_POST['newupc'].'",sku="'.substr((string)$_POST['newupc'] ,0,12).$finsku.'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
			//echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
		}	
		$_POST['upc']=(string)$_POST['newupc'];
	$_POST['changeupc']="";
}
if($_GET['changeupc']=="oui"){
	$_POST['changeupc']="changer";
}
 if ($_GET['action']=="default"){
	(string)$_POST['sku'] =$_GET['sku'];
	
	/*  if($_GET['etat']<>9)modifier_item($connectionapi,substr($_GET['sku'],0,12),$_GET['product_id'],"",9,$db);  */
	 if($_GET['etat']<>99)modifier_item($connectionapi,substr($_GET['sku'],0,12)."NO",$_GET['product_id'],"NO",99,$db,$_GET['retailprice']);
	 if($_GET['etat']<>22)modifier_item($connectionapi,substr($_GET['sku'],0,12)."R",$_GET['product_id'],"R",22,$db,$_GET['retailprice']);
/* 	 if($_GET['retailprice']>0){
		modifier_item($connectionapi,substr($_GET['sku'],0,12),$_GET['product_id'],"",9,$db,$_GET['retailprice']);
	 } */
	//echo 'allo'; 
}
if ($_GET['sku']!=""){
	(string)$_POST['sku'] =(string)$_GET['sku'];
	//echo 'allo';
}
			$sql = 'SELECT * FROM `oc_product` AS P where (P.quantity>0 || P.unallocated_quantity>0) AND P.ebay_id=0 order by P.product_id desc';
			//$sql = 'SELECT upc FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca where pr.product_id=ds.product_id and pr.product_id=ca.product_id and pr.quantity>0   and pr.status=0 and pr.remarque_interne="" group by pr.upc,pr.product_id order by pr.price desc '; //and (pr.location like "%magasin%") and (pr.location like "%magasin%")
			$req = mysqli_query($db,$sql);
			echo $sql;
			$_POST['nb_nonlistersurebay']=mysqli_num_rows($req);

if($_POST['nonlister']=="oui"){
			//echo $sql;
			$choix=mt_rand ( 1 , $_POST['nb_nonlistersurebay']-1 );
			//$sql = 'SELECT *,PD.color AS colorpd,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where (P.quantity>0 || P.unallocated_quantity>0) AND P.ebay_id=0 AND PD.language_id=1 and P.remarque_interne="" order by P.quantity desc limit '.$choix.',1';
			$sql = 'SELECT * FROM `oc_product` AS P where (P.quantity>0 || P.unallocated_quantity>0) AND P.ebay_id=0 limit '.$choix.',1';
			
			//$sql = 'SELECT * FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca where pr.product_id=ds.product_id and 
			//pr.product_id=ca.product_id and pr.quantity>0 and pr.status=0 and pr.remarque_interne="" order by ca.product_id desc limit 1 '; //and (pr.location like "%magasin%") and pr.remarque_interne=""
			echo "<br>".$sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$_POST['sku'] =$data['sku'];
			//echo (string)$_POST['sku'];
}
if (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
			
/* 			$rowverif= mysqli_affected_rows($db);
			if($rowverif==0){
				$sql2 = 'SELECT *,PD.color AS colorpd,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=1 and P. sku= "'.(string)$_POST['sku'] .'"';
				//echo $sql."<br>";
				$req2 = mysqli_query($db,$sql2);
				$data2 = mysqli_fetch_assoc($req2);
				$ajout=cloner_item("OK","",9,substr((string)$_POST['sku'] ,0,12),$data2['product_id'],$db);
				$req = mysqli_query($db,$sql);
			} */
			
					//echo (string)$_POST['sku'];
			$retailpriceverif=0;
			$ebay_id_refer="&ebay_id_refer=0";
			if(strlen($_POST['sku'])>12 && strlen($_POST['sku'])<15){
				$endsql='P.sku= "'.(string)substr($_POST['sku'],0,12 ).'"';
			}elseif(strlen($_POST['sku'])>6){
				$endsql='P.sku= "'.$_POST['sku'].'"';
			}else{
				$endsql='P.product_id= "'.$_POST['sku'].'"';
			}
			$sqln = 'SELECT *,PD.color AS colorpd,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=1 and '.$endsql;
	//echo $sqln;
			$reqn = mysqli_query($db,$sqln);
			$rowverif= mysqli_affected_rows($db);
			$datan = mysqli_fetch_assoc($reqn);
			if($rowverif>0){
				$_POST['upc']=(string)$datan['upc'];
			}else{
				echo "oui";
				$_POST['upc']=(string)substr($_POST['sku'],0,13);
			}
			$sqlnfr = 'SELECT *,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=2  and P. sku= "'.(string)substr($_POST['sku'],0,12 ).'"';
	//echo $sql;
			$reqnfr = mysqli_query($db,$sqlnfr);
			//$rowverif= mysqli_affected_rows($db);
			$datanfr = mysqli_fetch_assoc($reqnfr);
			$_POST['sku']=substr((string)$_POST['sku'] ,0,12);
			if($datan['priceretail']>0){
				$retailpriceverif=1;
			}else{
				$retailpriceverif=0;
			}
			//$_POST['sku']=(string)$datan['sku'];
			$err_n=0;
			if($datan['marketplace_item_id']>0)$ebay_id_refer="&ebay_id_refer=".$datan['marketplace_item_id'];
			$datever_n=$datan['date_price_upd'];
			$datevermag_n=$datan['date_price_upd_magasin'];
			$_POST['priceretail']=$datan['priceretail'];
			//echo $_POST['retailprice'];
			$date = new DateTime($datan['date_price']);
			$datan['date_price_upd'] = $date->format('Y-m-d');
			$datan['date_price_upd']=date_parse ($datan['date_price_upd']);
			$date = new DateTime($datan['date_price_upd_magasin']);
			$datan['date_price_upd_magasin'] = $date->format('Y-m-d');
			$datan['date_price_upd_magasin']=date_parse ($datan['date_price_upd_magasin']);
			$data['new']=1;
			if ($datan['weight']==0 || $datan['length']==0 || $datan['width']==0 || $datan['height']==0)$_POST['erreur_dimensions_poids']="Les dimensions ou le poids n'est pas inscrit";
			

			$sql3 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$datan['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$categoryname=$data3['name'];
			$data['category_id']=$data3['category_id'];
			$data['category_id']=$data3['category_id'];

			//echo $datano['location'];
			
			
				if($datan['condition_supp']!="" && $datan['condition_supp']!=","){
					$descriptionn.='<b>Conditions Supplementaire:</b><br>';
					
					if(strpos($datan['condition_supp'],",")===FALSE){
						$descriptionn.='<font color="red"><strong>- '.$datan['condition_supp'].'</strong></font><br>';
					}else{
						$conditionsupp=explode(',', $datan['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$descriptionn.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
									//echo $i;		
								}
							}	
					}
				}
							
				if($datan['accessory']!="" && $datan['accessory']!=","){
					$descriptionn.='<b>Accessories Included :</b><br>';
				
					if(strpos($datan['accessory'],",")===FALSE){
						$descriptionn.='- '.$datan['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $datan['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$descriptionn.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}	
				}				
				//echo $data['test'];		
				if($datan['test']!="" && $datan['test']!=","){
					$descriptionn.='<b>Tests - Repairs Done :</b><br>';
				
					if(strpos($datan['test'],",")===FALSE){
						$descriptionn.='- '.$datan['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $datan['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$descriptionn.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}
				}


		

			$sqlno = 'SELECT *,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=1 and P. sku= "'.(string)$_POST['sku'] .'no"';
	//echo $sqlno; 
			$reqno = mysqli_query($db,$sqlno);			
			$rowverif= mysqli_affected_rows($db);
			//$ajout=cloner_item($rowverif,"NO",99,substr((string)$_POST['sku'] ,0,12),$data['product_id'],$db);
			$datano = mysqli_fetch_assoc($reqno);
			
			if($datan['priceretail']==$datano['priceretail']&&$retailpriceverif==1){
				$retailpriceverif=1;
			}else{
				$retailpriceverif=0;
			}	
			$err_no=0;
			if($datano['marketplace_item_id']>0&&$ebay_id_refer!="")$ebay_id_refer="&ebay_id_refer=".$datano['marketplace_item_id'];
			$datever_no=$datano['date_price_upd'];
			$datevermag_no=$datano['date_price_upd_magasin'];
			$date = new DateTime($datano['date_price']);
			$datano['date_price_upd'] = $date->format('Y-m-d');
			$datano['date_price_upd']=date_parse ($datano['date_price_upd']);
			$date = new DateTime($datano['date_price_upd_magasin']);
			$datano['date_price_upd_magasin'] = $date->format('Y-m-d');
			$datano['date_price_upd_magasin']=date_parse ($datano['date_price_upd_magasin']);
			//echo $datano['location'];
			if ($_POST['priceretail']<$datano['priceretail']){
				echo "allo2";
				$_POST['priceretail']=$datano['priceretail'];
			}
			
				if($datano['condition_supp']!="" && $datano['condition_supp']!=","){
					$descriptionno.='<b>Conditions Supplementaire:</b><br>';
				
					if(strpos($datano['condition_supp'],",")===FALSE){
						$descriptionno.='<font color="red"><strong>- '.$datano['condition_supp'].'</strong></font><br>';
					}else{
						$conditionsupp=explode(',', $datano['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$descriptionno.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
									//echo $i;		
								}
							}	
					}
				}
							
				if($datano['accessory']!="" && $datano['accessory']!=","){
					$descriptionno.='<b>Accessories Included :</b><br>';
				
					if(strpos($datano['accessory'],",")===FALSE){
						$descriptionno.='- '.$datano['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $datano['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$descriptionno.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}		
				}				
				//echo $data['test'];		
				if($datano['test']!="" && $datano['test']!=","){
					$descriptionno.='<b>Tests - Repairs Done :</b><br>';
				
					if(strpos($datano['test'],",")===FALSE){
						$descriptionno.='- '.$datano['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $datano['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$descriptionno.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}
				}				
	
			$sqlr = 'SELECT *,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=1 and P. sku= "'.(string)$_POST['sku'] .'r"';
	//echo $sqlr;
			$reqr = mysqli_query($db,$sqlr);
			$datar = mysqli_fetch_assoc($reqr);
			
			if($datar['priceretail']==$datano['priceretail']&&$retailpriceverif==1){
				$retailpriceverif=1;
			}else{
				$retailpriceverif=0;
			}
			$err_r=0;
			if($datar['marketplace_item_id']>0&&$ebay_id_refer!="")$ebay_id_refer="&ebay_id_refer=".$datar['marketplace_item_id'];
			$datever_r=$datar['date_price_upd'];
			$datevermag_r=$datar['date_price_upd_magasin'];
			$date = new DateTime($datar['date_price']);
			$datar['date_price_upd'] = $date->format('Y-m-d');
			$datar['date_price_upd']=date_parse ($datar['date_price_upd']);
			$date = new DateTime($datar['date_price_upd_magasin']);
			$datar['date_price_upd_magasin'] = $date->format('Y-m-d');
			$datar['date_price_upd_magasin']=date_parse ($datar['date_price_upd_magasin']);
			if ($_POST['priceretail']<$datar['priceretail']){
				$_POST['priceretail']=$datar['priceretail'];
				echo "allo3";
			}	
				if($datar['condition_supp']!="" && $datar['condition_supp']!=","){
					$descriptionr.='<b>Conditions Supplementaire:</b><br>';
				
					if(strpos($datar['condition_supp'],",")===FALSE){
						$descriptionr.='<font color="red"><strong>- '.$datar['condition_supp'].'</strong></font><br>';
					}else{
						$conditionsupp=explode(',', $datar['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$descriptionr.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
									//echo $i;		
								}
							}	
					}
				}
							
				if($datar['accessory']!="" && $datar['accessory']!=","){
					$descriptionr.='<b>Accessories Included :</b><br>';
				
					if(strpos($datar['accessory'],",")===FALSE){
						$descriptionr.='- '.$datar['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $datar['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$descriptionr.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}	
				}
				
				//echo $data['test'];		
				if($datar['test']!="" && $datar['test']!=","){
					$descriptionr.='<b>Tests - Repairs Done :</b><br>';
				
					if(strpos($datar['test'],",")===FALSE){
						$descriptionr.='- '.$datar['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $datar['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$descriptionr.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}				
				}
$sql6 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$datan['manufacturer_id'];

// on envoie la requête
$req6 = mysqli_query($db,$sql6);
// on fait une boucle qui va faire un tour pour chaque enregistrement
$brandrecom="";
$data6 = mysqli_fetch_assoc($req6);
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
  
  
  <title>listing.php</title>
  <script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>    tinymce.init({      selector: '#mytextarea'    });  </script>
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
  </script>
  
  <link href="stylesheet.css" rel="stylesheet">
</head><body>

<form method="post" action="listing.php" name="listing">

  
  <table style="text-align: left; width: 1053px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">

    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 112px;">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><h3><?if($new==1){?><a href="listing.php" >Changer d'item</a><?}?> <a href="menulisting.php" >Retour au MENU</a> 		<?if($new==1){?>
		
		<?}?></h3>
        </td>
        <td colspan="4" style="vertical-align: middle;height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Information Produit</h1><?if ($_POST['erreur_dimensions_poids']!="") echo '<h3><font color="red">'.$_POST['erreur_dimensions_poids'].'</font></h3>'; ?>
        </td>
      </tr>



      </tr>
      <tr>
        <td colspan="1" rowspan="11" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;">
				<?
			if($datan['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$datan['image'].'" width="200">';
			?><br>
			<a href="multiupload.php?sku=<?echo (string)$_POST['sku'] ?>" >Modifier Photos</a>
        <br>
        <br>
        </td>
		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Sku
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; ">
		<input id="sku"  type="text" name="sku"  value="<?echo (string)$_POST['sku'] ;?>" maxlength="255"  autofocus>
		
		</td>
	 </tr>
	      <tr>
        <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		UPC:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?if($_POST['changeupc']=="changer"){?><input id="price"  type="text" name="newupc" value="<?echo $_POST['newupc'];?>" size="30" />
		<input type="hidden" name="upc" value="<?echo $datan['upc']	;?>"/>	<?}else{?>
		<?echo (string) ($datan['upc']);?>
		<br><a href="listing.php?changeupc=oui&sku=<? echo (string)$_POST['sku'];?>">Corriger UPC de l'item</a>
		<?}?>
		
		
		</td>

		</tr>
      <tr>
        <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Titre ANGLAIS:</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle;height: 0px; "><?echo $datan['name'];?></td>

		</tr>
      <tr>
         <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Titre FRANCAIS:</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; <?if($datan['name']==$datanfr['name']){?> background-color: red; color: white;<?}?>"><?echo $datanfr['name'];?></td>

        
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 112px;">Modèle:</td>
        <td style="vertical-align:  middle; height: 25px; "><?echo $datan['model'];?></td>

      </tr>

      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 112px;">Brand:<br>
        </td>
        <td style="vertical-align:  middle; ">

<?


echo $data6['name'];

?>

        </td>

      </tr>
      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 112px;">Categorie:<br>
        </td>
        <td style="vertical-align:  middle; "><?echo $categoryname;?></td>

      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 13px; background-color: #030282; color: white; width: 112px;">Couleur</td>
        <td style="vertical-align:  middle; height: 13px; "><?echo $datan['colorpd'];?></td>

      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 74px; background-color: #030282; color: white; width: 112px;">Description:</td>
        <td style="vertical-align:  middle; height: 74px; "><?echo $datan['andescription'];?></td>

      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;">Dimension:</td>
        <td style="vertical-align:  middle; height: 15px; "><?echo number_format($datan['length'],1);?> x <?echo number_format($datan['width'],1);?>  x <?echo number_format($datan['height'],1);?></td>	

      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 112px;">Poids:</td>
        <td style="vertical-align:  middle; height: 16px; "><?echo number_format($datan['weight'],2);?> Lbs</td>

      </tr>
	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: #e4bc03; width: 112px;"> 
		
		
		 <strong>Afficher les non listé sur eBay (<?echo $_POST['nb_nonlistersurebay'];?>)</strong> 
		 <input type="checkbox" name="nonlister" value="oui" <?if($_POST['nonlister']=='oui')echo 'checked';?>/>	
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
		<input type="hidden" name="changeupc" value="<?echo $_POST['changeupc'];?>"/> 
		</td>
		
	 
	  </tr>
    </tbody>
  
  </table>

</form>

<table style="text-align: left; width: 1053px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">

  <tbody>
    <tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #030282; color: white; width: 33%;">NEUF
     </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #030282; color: white; width: 33%;">BOITE OUVERTE (Grade A)
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #030282; color: white; width: 33%;">BOITE OUVERTE (Grade B)
      </td>
    </tr>
	<tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; <?if($datan['marketplace_item_id']==0&&$datan['unallocated_quantity']+$datan['quantity']>0){?>background-color:red; color: white;<?}elseif($datan['marketplace_item_id']>0&&$datan['unallocated_quantity']+$datan['quantity']>0){?>background-color:green; color: white;<?}else{?>background-color:e4bc03; <?}?> width: 33%;">Ebay ID: <? echo $datan['marketplace_item_id'];?>
	  <?
	  if ($datan['name']==""&&$_POST['sku']!=""){ ?>
	  <br><a href="insertionitemusa.php?upc=<? echo (string)$_POST['upc'];?>&action=listingusa&condition_insert=n&condition_id=9"><strong>Ajouter</strong></a> <?}?>
  
 </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; <?if($datano['marketplace_item_id']==0&&$datano['unallocated_quantity']+$datano['quantity']>0){?>background-color:red; color: white;<?}elseif($datano['marketplace_item_id']>0&&$datano['unallocated_quantity']+$datano['quantity']>0){?>background-color:green; color: white;<?}else{?>background-color:e4bc03; <?}?> width: 33%;">Ebay ID: <? echo $datano['marketplace_item_id'];?>
 	   <?if ($datan['name']==""&&$_POST['sku']!=""){?><br><a href="insertionitemusa.php?upc=<? echo (string)$_POST['upc'];?>&action=listingusa&condition_insert=no&condition_id=99"><strong>Ajouter</strong></a> <?}?>

	  </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; <?if($datar['marketplace_item_id']==0&&$datar['unallocated_quantity']+$datar['quantity']>0){?>background-color:red; color: white;<?}elseif($datar['marketplace_item_id']>0&&$datar['unallocated_quantity']+$datar['quantity']>0){?>background-color:green; color: white;<?}else{?>background-color:e4bc03; <?}?> width: 33%;">Ebay ID: <? echo $datar['marketplace_item_id'];?>
	   <?if ($datan['name']==""&&$_POST['sku']!=""){?><br><a href="insertionitemusa.php?upc=<? echo (string)$_POST['upc'];?>&action=listingusa&condition_insert=r&condition_id=22"><strong>Ajouter</strong></a> <?}?>
   

   </td>
    </tr>
	<tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #e4bc03; width: 33%;"><?echo $datan['name'];?>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #e4bc03; width: 33%;"><?echo $datano['name'];?>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #e4bc03; width: 33%;"><?echo $datar['name'];?>
      </td>
    </tr>
    <tr>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%;"><?if ($datan['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$datan['image'].'" width="150">';?>
	 <br><?echo $descriptionn;?>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px;"><?if ($datano['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$datano['image'].'" width="150">';?>
	  <br><?echo $descriptionno;?>
      </td>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px;"><?if ($datar['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$datar['image'].'" width="150">';?>
     <br><?echo $descriptionr;?>
	 </td>
    </tr>
    <tr>
      <td style="vertical-align:  middle;  text-align: center; <?if($retailpriceverif==1){?>background-color: green;<?}else{?>background-color: red;<?}?> color: white; height: 0px; width: 16%;">
	  Retail: <? echo "$ ".number_format($datan['priceretail']*1.34,2);?> cad
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datan['date_price_upd']||$datever_n==""){?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
		<? if($dateverification > $datan['date_price_upd'] && $datever_n=="" && $datevermag_n=="" && $_POST['erreur_dimensions_poids']==""){?><a href="modifierprix.php?product_id_r=<? echo $datar['product_id'];?>&product_id_no=<? echo $datano['product_id'];?>&product_id=<? echo $datan['product_id'];?>">
	  <strong>Ebay: <? echo "$ ".number_format($datan['price_with_shipping'],2);?> us</strong></a><?}else{?> 
	  Ebay: <? echo "$ ".number_format($datan['price_with_shipping'],2);?> us<?}?> 
      </td>		
      <td style="vertical-align:  middle;  text-align: center; <?if($retailpriceverif==1){?>background-color: green;<?}else{?>background-color: red;<?}?> color: white; height: 0px; width: 16%;">
	  Retail: <? echo "$ ".number_format($datano['priceretail']*1.34,2);?> cad
      </td>	     
	  <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datano['date_price_upd']||$datever_no==""||(($datan['price_with_shipping']*.90)-$datano['price_with_shipping']>.50)||$datano['price_with_shipping']>$datan['price_with_shipping']){?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datano['date_price_upd']||$datever_no==""||(($datan['price_with_shipping']*.90)-$datano['price_with_shipping']>.50)||$datano['price_with_shipping']>$datan['price_with_shipping']){?><strong>Ebay: <? echo "$ ".number_format($datano['price_with_shipping'],2);?> us (<?echo ($datan['price_with_shipping']*.90)-$datano['price_with_shipping'];?>)</strong><?}else{?> 
	  
	  Ebay: <? echo "$ ".number_format($datano['price_with_shipping'],2);?> us<?}?>
      </td>	      
	  <td style="vertical-align:  middle;  text-align: center; <?if($retailpriceverif==1){?>background-color: green;<?}else{?>background-color: red;<?}?> color: white; height: 0px; width: 16%;"> 
	  Retail: <? echo "$ ".number_format($datar['priceretail']*1.34,2);?> cad
      </td>	      
	  <td style="vertical-align:  middle;  text-align: center; background-color:<?if($dateverification > $datar['date_price_upd']||$datever_r==""||(($datan['price_with_shipping']*.80)-$datar['price_with_shipping']>.50)||$datar['price_with_shipping']>$datan['price_with_shipping']){?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datar['date_price_upd']||$datever_r==""||(($datan['price_with_shipping']*.80)-$datar['price_with_shipping']>.50)||$datar['price_with_shipping']>$datan['price_with_shipping']){?><strong>Ebay: <? echo "$ ".number_format($datar['price_with_shipping'],2);?> us (<?echo ($datan['price_with_shipping']*.80)-$datar['price_with_shipping'];?>)</strong><?}else{?> 
		
	 Ebay: <? echo "$ ".number_format($datar['price_with_shipping'],2);?> us<?}?>
      </td>
		</tr>
	  <tr>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a href="createbarcodemagasinun.php?product_id=<?echo $datan['product_id'];?>&sku=<?echo (string)$datan['sku']; ?>" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datan['date_price_upd_magasin']||$datevermag_n==""){$err_n=1;?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datan['date_price_upd_magasin'] && $datevermag_n=="" && $_POST['erreur_dimensions_poids']==""){$err_n=1;?><a href="modifierprix.php?product_id_r=<? echo $datar['product_id'];?>&product_id_no=<? echo $datano['product_id'];?>&product_id=<? echo $datan['product_id'];?>"><strong>Magasin: <? echo "$ ".number_format($datan['price_magasin'],2);?> cad</strong></a><?}else{?> 
	  Magasin: <? echo "$ ".number_format($datan['price_magasin']*1.34,2);?> cad<?}?>
      </td>		
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a href="createbarcodemagasinun.php?product_id=<?echo $datano['product_id'];?>&sku=<?echo (string)$datano['sku']; ?>" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>

      </td>	     
	  <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datano['date_price_upd_magasin']||$datevermag_no==""){$err_no=1;?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datano['date_price_upd_magasin'] && $datevermag_no==""){$err_no=1;?><strong>Magasin: <? echo "$ ".number_format($datano['price_magasin'],2);?> cad</strong><?}else{?> 
	  Magasin: <? echo "$ ".number_format($datano['price_magasin']*1.34,2);?> cad<?}?>
      </td>	      
	  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
		<a href="createbarcodemagasinun.php?product_id=<?echo $datar['product_id'];?>&sku=<?echo (string)$datar['sku']; ?>" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>
      </td>	      
	  <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datar['date_price_upd_magasin']||$datevermag_r==""){$err_r=1;?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datar['date_price_upd_magasin'] && $datevermag_r==""){$err_r=1;?><strong>Magasin: <? echo "$ ".number_format($datar['price_magasin'],2);?> cad</strong><?}else{?> 
	  Magasin: <? echo "$ ".number_format($datar['price_magasin']*1.34,2);?> cad<?}?>
      </td>
	</tr>
	<tr>	
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <?if($_POST['erreur_dimensions_poids']==""){?><a href="pretlisterusa.php?etat=9&sku=<? echo (string)$datan['sku'] ;?>&product_id=<? echo $datan['product_id'].$ebay_id_refer;?>&action=pretlisterusa&new=0"><strong>Pr&ecirc;t a Lister</strong></a><?}?>
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	 	<a href="listing.php?etat=9&sku=<? echo (string)$datan['sku'] ;?>&product_id=<? echo $datan['product_id'];?>&action=default&retailprice=<?echo $_POST['priceretail'];?>"><strong>Par défault</strong></a>
	  </td>


      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <?if($_POST['erreur_dimensions_poids']==""){?><a href="pretlisterusa.php?etat=99&sku=<? echo (string)$datano['sku'];?>&product_id=<? echo $datano['product_id'].$ebay_id_refer;?>&action=pretlisterusa&new=0"><strong>Pr&ecirc;t a Lister</strong></a><?}?>
	  </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	 
	  </td>

      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <?if($_POST['erreur_dimensions_poids']==""){?><a href="pretlisterusa.php?etat=22&sku=<? echo (string)$datar['sku'];?>&product_id=<? echo $datar['product_id'].$ebay_id_refer;?>&action=pretlisterusa&new=0"><strong>Pr&ecirc;t a Lister</strong></a><?}?>
	  </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	 
	  </td>
    </tr>
	    <tr>

      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a  href="modificationitemusa.php?sku=<? echo (string)$datan['sku'] ;?>&product_id=<? echo $datan['product_id'];?>&action=listingusa"><strong>Modifier</strong></a> 
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	 	<a href="clonerusa.php?etat=9&sku=<? echo (string)$datan['sku'] ;?>&product_id=<? echo $datan['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a>
		</td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
    <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a href="clonerusa.php?etat=99&sku=<? echo (string)$datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a>
	  </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
     <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a href="clonerusa.php?etat=22&sku=<? echo (string)$datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=modificationitemusa"><strong>Cloner</strong></a>
	  </td>
    </tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité total:
      </td>
      <td style="vertical-align:  middle; width: 174px;<?if($datan['unallocated_quantity']+$datan['quantity'] <> $datan['quantity_total']){?>background-color:red; color: white;<?}else{?>background-color:green; color: white;<?}?>">
	  <a href="inventaire.php?lien=linstingusa&sku=<? echo (string)$datan['sku'];?>&product_id=<? echo $datan['product_id'];?>"><?echo $datan['quantity_total'];?></a>
      </td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Quantité total:</td>
      <td style="vertical-align:  middle; <?if($datano['unallocated_quantity']+$datano['quantity'] <> $datano['quantity_total']){?>background-color:red; color: white;<?}else{?>background-color:green; color: white;<?}?>">
	  <a href="inventaire.php?lien=linstingusa&sku=<? echo (string)$datano['sku'];?>&product_id=<? echo $datano['product_id'];?>"><?echo $datano['quantity_total'];?></a></td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Quantité
total:<br>
      </td>
      <td style="vertical-align:  middle; <?if($datar['unallocated_quantity']+$datar['quantity'] <> $datar['quantity_total']){?>background-color:red; color: white;<?}else{?>background-color:green; color: white;<?}?>">
	  <a href="inventaire.php?lien=linstingusa&sku=<? echo (string)$datar['sku'];?>&product_id=<? echo $datar['product_id'];?>"><?echo $datar['quantity_total'];?></a></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; background-color: #030282; color: white;">Location magasin:<br>
      </td>
      <td style="vertical-align:  middle;"><?echo $datan['location_magasin'];?></td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Location magasin:<br>
      </td>
      <td style="vertical-align:  middle; "><?echo $datano['location_magasin'];?></td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Location magasin:<br>
      </td>
      <td style="vertical-align:  middle; "><?echo $datar['location_magasin'];?></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité
magasin:<br>
      </td>
      <td style="vertical-align:  middle; width: 174px;">
	   <a href="inventairemagasin.php?lien=linstingusa&sku=<? echo (string)$datan['sku'];?>&product_id=<? echo $datan['product_id'];?>&action=default"><?echo $datan['unallocated_quantity'];?></a>  
      </td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Quantité
magasin:<br>
      </td>
      <td style="vertical-align:  middle; ">
	  <a href="inventairemagasin.php?lien=linstingusa&sku=<? echo (string)$datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=default"><?echo $datano['unallocated_quantity'];?></a>  
	  </td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Quantité
magasin:<br>
      </td>
      <td style="vertical-align:  middle; ">
	  <a href="inventairemagasin.php?lien=linstingusa&sku=<? echo (string)$datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=default"><?echo $datar['unallocated_quantity'];?></a>  
	  </td>
    </tr>
	    <tr>
      <td style="vertical-align:  middle; background-color: #030282; color: white;">Location entrep&ocirc;t:<br>
      </td>
      <td style="vertical-align:  middle;"><?echo $datan['location_entrepot'];?></td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Location entrep&ocirc;t:<br>
      </td>
      <td style="vertical-align:  middle; "><?echo $datano['location_entrepot'];?></td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">Location entrep&ocirc;t::<br>
      </td>
      <td style="vertical-align:  middle; "><?echo $datar['location_entrepot'];?></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">
	  Quantité entrep&ocirc;t:
      </td>
	    <td style="vertical-align:  middle; ">
      <a href="inventaireentrepot.php?lien=linstingusa&sku=<? echo (string)$datan['sku'];?>&product_id=<? echo $datan['product_id'];?>&action=default"><?echo $datan['quantity'];?></a>  
      </td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">
	  Quantité entrep&ocirc;t:
      </td>
      <td style="vertical-align:  middle; ">
	  	  <a href="inventaireentrepot.php?lien=linstingusa&sku=<? echo (string)$datano['sku'];?>&product_id=<? echo $datano['product_id'];?>&action=default"><?echo $datano['quantity'];?></a>  
	  </td>
      <td style="vertical-align:  middle;  background-color: #030282; color: white;">
	  Quantité entrep&ocirc;t:
      </td>
      <td style="vertical-align:  middle; ">
	        <a href="inventaireentrepot.php?lien=linstingusa&sku=<? echo (string)$datar['sku'];?>&product_id=<? echo $datar['product_id'];?>&action=default"><?echo $datar['quantity'];?></a>  

	  </td>
    </tr>
  </tbody>
</table>
<?

if (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
					$sqlo = 'SELECT *,P.sku,P.location AS location_entrepot,p.unallocated_quantity, P.quantity,P.price AS priceretail,PS.price AS price_magasin,PS.date_price_upd AS date_price_upd_magasin  FROM `oc_product` AS P LEFT JOIN `oc_product_special` AS PS ON P.product_id=PS.product_id LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where PD.language_id=1 and  P.sku like "'.(string)$_POST['sku'] .'%"
					and (P.sku != "'.(string)$_POST['sku'] .'R" and P.sku != "'.(string)$_POST['sku'] .'NO" and P.sku != "'.(string)$_POST['sku'] .'")';
			//echo $sqlo;
					$reqo = mysqli_query($db,$sqlo);
					$nbclone=mysqli_num_rows($reqo);
					$j=0;

			//echo $datao['sku'];
			// var_dump( $datatab[$j]['product_id']);
			if($nbclone>0){
				$j=0;
		?>
		<table style="text-align: left; width: 1053px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
			<tr>
				<td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">ARTICLES CLONES
				</td>
			</tr>
		</table>

<table style="text-align: left; width: 1053px;  margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
	<tr >
	<?while($datao = mysqli_fetch_assoc($reqo)){
					$err_o=0;
			if($datao['marketplace_item_id']>0)$ebay_id_refer="&ebay_id_refer=".$datao['marketplace_item_id'];
			$datever_o=$datao['date_price_upd'];
			$datevermag_o=$datao['date_price_upd_magasin'];
			$date = new DateTime($datao['date_price']);
			$datao['date_price_upd'] = $date->format('Y-m-d');
			$datao['date_price_upd']=date_parse ($datao['date_price_upd']);
			$date = new DateTime($datao['date_price_upd_magasin']);
			$datao['date_price_upd_magasin'] = $date->format('Y-m-d');
			$datao['date_price_upd_magasin']=date_parse ($datao['date_price_upd_magasin']);
			$data['new']=1;
						if($datao['condition_supp']!="" && $datao['condition_supp']!=","){
					$descriptiono.='<b>Conditions Supplementaire:</b><br>';
				
					if(strpos($datao['condition_supp'],",")===FALSE){
						$descriptiono.='<font color="red"><strong>- '.$datao['condition_supp'].'</strong></font><br>';
					}else{
						$conditionsupp=explode(',', $datao['condition_supp']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$descriptiono.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
									//echo $i;		
								}
							}	
					}
				}
							
				if($datao['accessory']!="" && $datao['accessory']!=","){
					$descriptiono.='<b>Accessories Included :</b><br>';
				
					if(strpos($datao['accessory'],",")===FALSE){
						$descriptiono.='- '.$datao['accessory'].'<br>';
					}else{
						$conditionsupp=explode(',', $datao['accessory']);
							foreach($conditionsupp as $conditioncheck){
								if( $conditioncheck!=""){
									$descriptiono.='- '.$conditioncheck.'<br>';
									//echo $i;		
								}
							}	
					}	
				}
				
				//echo $data['test'];		
				if($datao['test']!="" && $datao['test']!=","){
					$descriptiono.='<b>Tests - Repairs Done :</b><br>';
				
					if(strpos($datao['test'],",")===FALSE){
						$descriptiono.='- '.$datao['test'].'<br>';
					}else{
						$conditionsupp=explode(',', $datao['test']);
						foreach($conditionsupp as $conditioncheck){
							if($conditioncheck!=""){
								$descriptiono.='- '.$conditioncheck.'<br>';
								//echo $i;		
							}
						}
					}				
				}

	
			?>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; width: 33%">
		<table style="text-align: left; width: 351px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">

  <tbody>
    <tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #030282; color: white; width: 33%;"><?echo $datao['sku'];?>
      </td>
    </tr>
	<tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; <?if($datao['marketplace_item_id']==0&&$datao['unallocated_quantity']+$datao['quantity']>0){?>background-color:red; color: white;<?}elseif($datao['marketplace_item_id']>0&&$datao['unallocated_quantity']+$datao['quantity']>0){?>background-color:green; color: white;<?}else{?>background-color:e4bc03; <?}?> width: 33%;">Ebay ID: <? echo $datao['marketplace_item_id'];?>
      </td>
    </tr>
	<tr >
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #e4bc03; width: 33%;"><?echo $datao['name'];?>
      </td>
    </tr>
    <tr>
      <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%;"><?if ($datao['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$datao['image'].'" width="150">';?>
	 <br><?echo $descriptiono;?>
      </td>
    </tr>
    <tr>
      <td style="vertical-align:  middle;  text-align: center; <?if($retailpriceverif==1){?>background-color: green;<?}else{?>background-color: red;<?}?> color: white; height: 0px; width: 16%;">
	  Retail: <? echo "$ ".number_format($datao['priceretail']*1.34,2);?> cad
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datao['date_price_upd']||$datever_o==""){?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datao['date_price_upd']||$datever_o==""){?><a href="modifierprix.php?clone=clone&product_id=<? echo $datao['product_id'];?>">
	  <strong>Ebay: <? echo "$ ".number_format($datao['price_with_shipping'],2);?> us</strong></a><?}else{?> 
	  Ebay: <? echo "$ ".number_format($datao['price_with_shipping'],2);?> us<?}?>
      </td>		
		</tr>
	  <tr>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
			  		<a href="createbarcodemagasinun.php?product_id=<?echo $datao['product_id'];?>&sku=<?echo (string)$datao['sku']; ?>" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>

      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: <?if($dateverification > $datao['date_price_upd_magasin']||$datevermag_o==""){$err_o=1;?>red; color: white;<?}else{?>green; color: white;<?}?> height: 0px; width: 16%;">
	  <?if($dateverification > $datao['date_price_upd_magasin']||$datevermag_o==""){$err_o=1;?><a href="modifierprix.php?clone=clone&product_id=<? echo $datao['product_id'];?>"><strong>Magasin: <? echo "$ ".number_format($datao['price_magasin'],2);?> cad</strong></a><?}else{?> 
	  Magasin: <? echo "$ ".number_format($datao['price_magasin']*1.34,2);?> cad<?}?>
      </td>		     
	</tr>
	<tr>	
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <?if($retailpriceverif==1 || $datao['marketplace_item_id']>0){?><a href="pretlisterusa.php?etat=9&sku=<? echo (string)$datao['sku'] ;?>&product_id=<? echo $datao['product_id'].$ebay_id_refer;?>&action=pretlisterusa&new=0"><strong>Pr&ecirc;t a Lister</strong></a><?}?>
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  </td>
    </tr>
	    <tr>

      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
	  <a  href="modificationitemusa.php?sku=<? echo (string)$datao['sku'] ;?>&product_id=<? echo $datao['product_id'];?>&action=listingusa&clone=oui"><strong>Modifier</strong></a> 
      </td>
      <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
			</td>

    </tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité total:
      </td>
      <td style="vertical-align:  middle; width: 174px;<?if($datao['unallocated_quantity']+$datao['quantity'] <> $datao['quantity_total']){?>background-color:red; color: white;<?}else{?>background-color:green; color: white;<?}?>">
	  <a href="inventaire.php?lien=linstingusa&sku=<? echo (string)$datao['sku'];?>&product_id=<? echo $datao['product_id'];?>"><?echo $datao['quantity_total'];?></a> 
      </td>
 </tr>
    <tr>
      <td style="vertical-align:  middle; background-color: #030282; color: white;">Location magasin:<br>
      </td>
      <td style="vertical-align:  middle;"><?echo $datao['location_magasin'];?></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité
magasin:<br>
      </td>
      <td style="vertical-align:  middle; width: 174px;">
	   <a href="inventairemagasin.php?lien=linstingusa&sku=<? echo (string)$datao['sku'];?>&product_id=<? echo $datao['product_id'];?>&action=default"><?echo $datao['unallocated_quantity'];?></a>  
      </td>

    </tr>
	    <tr>
      <td style="vertical-align:  middle; background-color: #030282; color: white;">Location entrep&ocirc;t:<br>
      </td>
      <td style="vertical-align:  middle;"><?echo $datao['location_entrepot'];?></td>
    </tr>
    <tr>
      <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">
	  Quantité entrep&ocirc;t:
      </td>
	    <td style="vertical-align:  middle; ">
      <a href="inventaireentrepot.php?lien=linstingusa&sku=<? echo (string)$datao['sku'];?>&product_id=<? echo $datao['product_id'];?>&action=default"><?echo $datao['quantity'];?></a>  
      </td>
    </tr>
  </tbody>
</table>
		</td>
		<?if ($j==2 ||$j==5 || $j==8) echo '</tr>	<tr >';?>
		
	<?
		$j++;}
	?>
	</tr>
</table>
			<?}?>	
	
<?}?>
</body></html>

<?


mysqli_close($db);

?>