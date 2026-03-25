<?
ob_start();
// on se connecte à MySQL 
include 'connection.php';
// Start output buffering to prevent header issues


//print("<pre>".print_r ($_POST,true )."</pre>");
if (!isset($_POST['sku'])) {
    $_POST['sku'] = isset($_GET['sku']) ? $_GET['sku'] : '';
}
if (!isset($_POST['hsec'])) {
    $_POST['hsec'] = isset($_GET['hsec']) ? $_GET['hsec'] : '0';
}
if (!isset($_POST['hhour'])) {
    $_POST['hhour'] = isset($_GET['hhour']) ? $_GET['hhour'] : '0';
}
if (!isset($_POST['hmin'])) {
    $_POST['hmin'] = isset($_GET['hmin']) ? $_GET['hmin'] : '0';
}
if (!isset($_POST['changeupc'])) {
    $_POST['changeupc'] = isset($_GET['changeupc']) ? $_GET['changeupc'] : '';
}
if (!isset($_POST['new_ebay_id'])) {
    $_POST['new_ebay_id'] = isset($_GET['new_ebay_id']) ? $_GET['new_ebay_id'] : '';
}
if (!isset($_POST['action'])) {
    $_POST['action'] = isset($_GET['action']) ? $_GET['action'] : '';
}
if (!isset($_POST['nonlister'])) {
    $_POST['nonlister'] = isset($_GET['nonlister']) ? $_GET['nonlister'] : '';
}

// Initialize the $html variable
$html = '';

// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db);  
$dateverification = new DateTime('now');
$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
$dateverification = $dateverification->format('Y-m-d');
$dateverification=date_parse ($dateverification);
if($_POST['changeupc']=="changer"){
		$sql = 'SELECT * FROM oc_product where upc = "'.(string)$_POST['upc'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		while($data = mysqli_fetch_assoc($req)){
			$finsku=substr((string)$data['sku'] ,12,10);
			$sql2 = 'UPDATE `oc_product`SET upc="'.(string)$_POST['newupc'].'",ebay_last_check="2020-09-01",sku="'.substr((string)$_POST['newupc'] ,0,12).$finsku.'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
			//echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
		}	
		$_POST['upc']=(string)$_POST['newupc'];
	$_POST['changeupc']="";
	$_POST['sku']=$_POST['skuancien'];
}
if($_POST['new_ebay_id']>0 && isset($_POST['new_ebay_id'])){
	$sql2 = 'UPDATE `oc_product`SET ebay_id="'.$_POST['new_ebay_id'].'",ebay_last_check="2020-09-01" WHERE `product_id` ="'.$_POST['hid_product_id'].'"';
	//echo $sql2."<br>";
	$req2 = mysqli_query($db,$sql2);
	$_POST['marketplace_item_id']=$_POST['new_ebay_id'];
	$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['hid_product_id'],$_POST['hid_quantity_total'],$db,"oui");
	$_POST['sku']=$_POST['skuancien'];
}
/*
if($_POST['changeupc']=="oui"){
	$_POST['changeupc']="changer";
}
 if ($_POST['action']=="default"){
	(string)$_POST['sku'] =$_GET['sku'];
}
if ($_POST['sku']!=""){
	(string)$_POST['sku'] =(string)$_POST['sku'];
	//echo 'allo';
}*/
			$sql = 'SELECT * FROM `oc_product` AS P where (P.quantity>0 || P.unallocated_quantity>0) AND P.ebay_id=0 order by P.product_id desc';
			$req = mysqli_query($db,$sql);
			if(mysqli_num_rows($req)>0){
				$_POST['nb_nonlistersurebay']=mysqli_num_rows($req);
			}else{
				$_POST['nb_nonlistersurebay']=0;
			}
if($_POST['nonlister']=="oui"){
			//echo $sql;
			$choix=mt_rand ( 1 , $_POST['nb_nonlistersurebay']-1 );
			$sql = 'SELECT * FROM `oc_product` AS P  
			 where (P.quantity>0 || P.unallocated_quantity>0) AND P.ebay_id=0 order by P.product_id desc';
			$req = mysqli_query($db,$sql);
			$products = mysqli_fetch_assoc($req);
			$_POST['sku'] =$products['sku'];
			//echo (string)$_POST['sku'];
}
//print("<pre>".print_r ($_POST,true )."</pre>");

if (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
	
		$search="";
		if(strlen($_POST['sku'])<6 && is_numeric($_POST['sku'])){
			 $products[]=get_products_by_id($_POST['sku']);
			 $_POST['sku']=$products[0]['sku'];
		}else{
			$products=get_product((string)$_POST['sku']);
		//	echo 102;//print("<pre>".print_r ($products,true )."</pre>");
			$search="";
			if(!isset($products)){
				$products=get_products_by_search($_POST['sku']);
				if(count($products)>0)
					$search="yes";
			}
			if(count($products)==0 && $search==""){
				$products=get_product(substr((string) $_POST['sku'],0,11));
			 }
		//	 echo '102'; 
		//	 //print("<pre>".print_r ($products,true )."</pre>");
			 if(isset($products) && count($products)==1){
				 foreach ($products as $product){
					if($product['name_en']==""){
						//	echo "allo";
							header("location: insertionitem.php?sku=".(string)$product['sku']."&upc=".(string)$product['upc']."&product_id=".(string)$product['product_id']."&action=exist"); 
							exit();
					}
				}
			 }
			 if(!isset($products)){
				header("location: insertionitem.php?upc=".(string)$_POST['sku']."&action=listing"); 
				exit();
		   }
		}
}	
?>
<html>
<head>
  <title>listing.php</title>
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
</head><body>
<form id="form-product" class="form-horizontal" action="listing.php" method="post">
  <table style="text-align: left; width: 1053px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 112px;">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><h3>
		<a href="menulisting.php" >Retour au MENU</a> <br>	
		<a href="listing.php?refresh_etsy=oui" >Refresh Token Etsy</a> 	
</h3>
        </td>
        <td colspan="4" style="vertical-align: middle;height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Information Produit</h1><?if (isset($_POST['erreur_dimensions_poids'])) echo '<h3><font color="red">'.$_POST['erreur_dimensions_poids'].'</font></h3>'; ?>
        </td>
      </tr>
	  	<tr>
		 <td colspan="4" style="height: 50; background-color: green; color: white;  text-align: center; "> 
			<span id="hour" ><?echo $_POST['hhour'];?></span>:<span id="min"><?echo $_POST['hmin'];?></span>:<span id="sec"><?echo $_POST['hsec'];?></span>
			<input type="hidden" name="hhour" id="hhour" value="<?echo $_POST['hhour'];?>" />
			<input type="hidden" name="hmin" id="hmin" value="<?echo $_POST['hmin'];?>" />
			<input type="hidden" name="hsec" id="hsec" value="<?echo $_POST['hsec'];?>" />
		</td>
     </tr>
      </tr>
				<?$nu=0;$j=0;
			//	//print("<pre>".print_r ($products,true )."</pre>");
 if(isset($products)){
	//print("<pre>".print_r ($products,true )."</pre>");
	  foreach($products as $product){
			$info_supplementaire="";
		  	if($product['marketplace_item_id']>0)$ebay_id_refer="&ebay_id_refer=".$product['marketplace_item_id'];
			$datever=$product['date_price_upd'];
			$product['date_price_upd_magasin']=isset($product['date_price_upd_magasin'])?$product['date_price_upd_magasin']:'1990-01-01';
			$datevermag=isset($product['date_price_upd_magasin'])?$product['date_price_upd_magasin']:'1990-01-01';
		//	$date = new DateTime($product['date_price']);
		//	$product['date_price_upd'] = $date->format('Y-m-d');
			$product['date_price_upd']=date_parse ($product['date_price_upd']);
			$date = new DateTime($product['date_price_upd_magasin']);
			$product['date_price_upd_magasin'] = $date->format('Y-m-d');
			$product['date_price_upd_magasin']=date_parse ($product['date_price_upd_magasin']);
			$data['new']=1;
						if($product['condition_supplementaire']!="" && $product['condition_supplementaire']!=","){
					$info_supplementaire.='<b>Informations Supplementaires:</b><br>';
					if(strpos($product['condition_supplementaire'],",")===FALSE){
						$info_supplementaire.='<font color="red"><strong>- '.$product['condition_supplementaire'].'</strong></font><br>';
					}else{
						$conditionsupp=explode(',', $product['condition_supplementaire']);
							foreach($conditionsupp as $conditioncheck){
								if($conditioncheck!=""){
									$info_supplementaire.='<font color="red"><strong>- '.$conditioncheck.'</strong></font><br>';
									//echo $i;		
								}
							}	
					}
				}
		  if($nu==0){
			  //echo "allo";
			  $image_principal=$product['image_product'];
			  $product_id_image_principal=$product['product_id'];
			  $main_ebay_id=$product['marketplace_item_id'];
			  $mainupc=$product['upc'];
			  if (!isset($product['specifics'])) {
				echo '<script type="text/javascript">
						window.open("https://www.phoenixliquidation.ca/interne/update_category.php?category_id=' . $product['category_id'] . '", "category_id");
					  </script>';
				// getCategorySpecifics($connectionapi, $product, $db);
			}
			  
			  $html='<tr>
				<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ">
				UPC:
				</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ';
				if($product['sku']!=$_POST['sku']){
					$html.='background-color: red; color: white;';
				}
				$html.='">';
				if($_POST['changeupc']=="changer"){
					$html.='<input id="price"  type="text" name="newupc" value="'.$_POST['newupc'].'" size="30" />
				<input type="hidden" name="upc" value="'.$product['upc'].'"/>';
				}else{
					$html.=(string) ($product['upc']).'
				<br><a href="listing.php?changeupc=oui&sku='.(string)$_POST['sku'].'">Corriger UPC de l\'item</a>';
				}
				$html.='</td>
				</tr>
				<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; ;">
				eBay ID:
				</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">';
				if($product['marketplace_item_id']==0){
					$html.='<input id="new_ebay_id"  type="text" name="new_ebay_id" value="'.$product['marketplace_item_id'].'" size="30" /><br>
					Ajouter Ebay ID';
				}else{
					$html.='<a href="https://www.ebay.com/itm/'.$product['marketplace_item_id'].'" target="ebaynew">'.$product['marketplace_item_id'].'</a>';	
				}
				$html.='
			  <tr>
				<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Titre ANGLAIS:</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle;text-align:center;height: 0px; ">'.$product['name_en'].'</td>
				</tr>
			  <tr>
				 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Titre FRANCAIS:</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px;';
				if($product['name']==$product['name_fr']){
					$html.='background-color: red; color: white;';
				}
				$html.='>">'.$product['name_fr'].'</td>
			  </tr>
			  			  <tr>
				<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Condition:</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle;text-align:center;height: 0px; ">'.$product['condition_name'].'</td>
				</tr>
			  <tr>
				<td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 112px;">Modèle:</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;height: 25px; ">'.$product['model'].'</td>
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; background-color: #030282; color: white; width: 112px;">Brand:<br>
				</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;">'.$product['brand'].'</td>
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; background-color: #030282; color: white; width: 112px;">Categorie:<br>
				</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;"><b><span style="color: #ff0000;">'.$product['category_id'].':'.$product['category_name'].'</span><b></td>
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; height: 13px; background-color: #030282; color: white; width: 112px;">Couleur</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;height: 13px; ">'.$product['color_fr'].'</td>
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; height: 74px; background-color: #030282; color: white; width: 112px;">Description:</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;height: 74px; ">'.$product['description_supp_fr'].'</td>
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;">Dimension:</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;height: 15px; ">'.number_format($product['length'],1).' x '.number_format($product['width'],1).'  x '.number_format($product['height'],1).'</td>	
			  </tr>
			  <tr>
				<td style="vertical-align:  middle; height: 16px; background-color: #030282; color: white; width: 112px;">Poids:</td>
				<td colspan="2" style="vertical-align:  middle; text-align:center;height: 16px; ">'.number_format($product['weight'],2).' Lbs</td>
			  </tr>
			  				<tr>
							<td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;"></td>
				  <td style="vertical-align:  middle;  text-align: center; ';
				  if(isset($retailpriceverif) && $retailpriceverif==1){
					  $html.='background-color: green;';
					}else{
						$html.='background-color: red;';
						}
					$html.='color: white; height: 0px; width: 16%;">
				  Retail: '."$ ".number_format($product['priceretail'],2).' 
				  </td>
				  <td style="vertical-align:  middle;  text-align: center; background-color:';
				  if($dateverification > $product['date_price_upd']||$datever==""){
					  $html.='red; color: white;';
				}else{
					$html.='green; color: white;';
					}
				$html.='height: 0px; width: 16%;">';
				if($dateverification > $product['date_price_upd']||$datever==""){
					$html.='<a href="modifierprix.php?clone=clone&product_id='.$product['product_id'].'">
				  <strong>Ebay: '."$ ".number_format($product['price_with_shipping'],2).' us</strong></a>';
				  }else{
					  $html.='Ebay: '."$ ".number_format($product['price_with_shipping'],2).' us';
					  }
					  $html.='</td>		
					</tr>
				  <tr>
				  <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;"></td>
				  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
					<a href="createbarcodemagasinun.php?product_id='.$product['product_id'].'&sku='.(string)$product['sku'].'" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>
				  </td>
				  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
					<a href="createsmallbarcode.php?product_id='.$product['product_id'].'" target="google" style="color:#ff0000"><strong>LABEL SKU</strong></a> 
				  </td>
				  </tr>
				  <tr>
				   <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;"></td>
				       <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
					<a href="listing.php?etat=9&sku='.$product['sku'].'&product_id='.$product['product_id'].'&action=default&retailprice='.$product['priceretail'].'"><strong>Par défault</strong></a>
				  </td>
				  <td style="vertical-align:  middle;  text-align: center; background-color: ';
				  if($dateverification > $product['date_price_upd_magasin']||$datevermag==""){
					  $err_o=1;
					  $html.='red; color: white;';
					  }else{
						  $html.='green; color: white;';
					}
					$html.='height: 0px; width: 16%;">';
				  if($dateverification > $product['date_price_upd_magasin']||$datevermag==""){
					  $err_o=1;
					  $html.='<strong>Magasin: '."$ ".number_format($product['price_magasin'],2).' </strong>';
				}else{
				  $html.='Magasin: '."$ ".number_format($product['price_magasin'],2).' ';
				  }
				  $html.='</td>		     
				</tr>
			  <tr>
			  <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;">
			  </td>
			  	<td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; ">
				  <a href="pretlister.php?product_id='.$product['product_id'].'&action=pretlister&new=0"><strong>Pr&ecirc;t a Lister</strong></a>
				  </td>
			   <td colspan="1" style="vertical-align:  middle; text-align: center; height: 16px; background-color: #e4bc03; width: 112px;"> 
				  <a  href="modificationitem.php?product_id='.$product['product_id'].'&action=listing"><strong>Modifier</strong></a> 
				  </td>
			</tr>
										<tr>	
				 <td style="vertical-align:  middle; height: 15px; background-color: #030282; color: white; width: 112px;"></td>
<td colspan="1" style="vertical-align:  middle; text-align: center; height: 16px; background-color: #e4bc03; width: 112px;"> 
				  <a href="clonerproduct.php?etat='.$product['condition_id'].','.str_replace("_","",substr((string)$product['sku'] ,12,2)).'&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=modificationitemusa"><strong>Cloner</strong></a>
						</td>
				   <td style="vertical-align:  middle;  text-align: center; background-color: red; color: white;height: 0px;">';
					$html.='<a href="uploadphoto.php?product_id='.$product['product_id'].'&lien_a_cloner='.$product['marketplace_item_id'].'&upc='.$product['upc'].'" >Importer photo eBay</a>';
				  $html.='</td>
				</tr>
				</tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité total:
				  </td>
				  <td colspan="2" style="vertical-align:  middle;text-align:center; width: 174px;';
				  if($product['unallocated_quantity']+$product['quantity'] <> $product['quantity_total']){
					  $html.='background-color:red; color: white;';
				}else{
					$html.='background-color:green; color: white;';
					}
					$html.='">
				  <a href="inventaire.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'">'.$product['quantity_total'].'</a> 
				  </td>
			 </tr>
				
				<tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité
			a PLACER:<br>
				  </td>
				  <td colspan="2"style="vertical-align:  middle; text-align:center;width: 174px;">
				   <a href="inventairemagasin.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=default">'.$product['unallocated_quantity'].'</a>  
				  </td>
				</tr>
					<tr>
				  <td style="vertical-align:  middle; background-color: #030282; color: white;">Location entrep&ocirc;t:<br>
				  </td>
				  <td colspan="2"style="vertical-align:  middle;text-align:center;">'.$product['location_entrepot'].'</td>
				</tr>
				<tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">
				  Quantité entrep&ocirc;t:
				  </td>
					<td colspan="2"style="vertical-align:  middle; text-align:center;">
				  <a href="inventaireentrepot.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=default">'.$product['quantity'].'</a>  
				  </td>
				</tr>
			  </tbody>
			 </table> 
	<table style="text-align: left; width: 1053px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>		 
			<tr>';
		  }else{
			  $html.='
				<td colspan="1" style="vertical-align: middle;height: 100; background-color: white; color: white;  text-align: center;">
						<table style="text-align: left;  margin-left: auto; margin-right: auto;" border="1" cellpadding="1" cellspacing="1">
				  <tbody>
					<tr>
					  <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #030282; color: white; width: 33%;">'.$product['sku'].'
					  </td>
					</tr>
					<tr style="">
					  <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; ';
		if($product['marketplace_item_id']==0&&$product['unallocated_quantity']+$product['quantity']>0){
			$html.='background-color:red; color: white;';
		}elseif($product['marketplace_item_id']>0&&$product['unallocated_quantity']+$product['quantity']>0){
			$html.='background-color:green; color: white;';
		}else{
			$html.='background-color:e4bc03;';	
		}
		$html.='width: 33%;">Ebay ID: <a href="https://www.ebay.com/itm/'.$product['marketplace_item_id'].'" target="ebaynew">'.$product['marketplace_item_id'].'</a></td>
				</tr>
				<tr style="">
					  <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; ';
		if($product['etsy_id']==0&&$product['unallocated_quantity']+$product['quantity']>0){
			$html.='background-color:red; color: white;';
		}elseif($product['etsy_id']>0&&$product['unallocated_quantity']+$product['quantity']>0){
			$html.='background-color:green; color: white;';
		}else{
			$html.='background-color:e4bc03;';	
		}
		$html.='width: 33%;">Ebay ID: <a href="https://www.etsy.com/ca/listing/'.$product['etsy_id'].'" target="etsynew">'.$product['etsy_id'].'</a></td>
				</tr>
				<tr>
				  <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; background-color: #e4bc03; width: 33%;">'.$product['name_en'].'</td>
				</tr>
				<tr>
				  <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%;">';
				  if ($product['image_product']!="")
					  $html.='<img src="https://www.phoenixliquidation.ca/image/'.$product['image_product'].'" width="150">';
				 $html.='<br>'.$info_supplementaire.'</td>
				</tr>
							  <tr>
				<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Condition:</td>
				<td colspan="2" rowspan="1" style="vertical-align:  middle;text-align:center;height: 0px; "><b>'.$product['condition_name'].'<b></td>
				</tr>
				<tr>
				<td style="vertical-align:  middle; background-color: #030282; color: white; width: 112px;">Categorie:<br>
				</td>
				<td colspan="2"style="vertical-align:  middle; text-align:center;"><b><span style="color: #ff0000;">'.$product['category_name'].'</span><b></td>
			  </tr>
				<tr>
				  <td style="vertical-align:  middle;  text-align: center; ';
				  if(isset($retailpriceverif) && $retailpriceverif==1){
					  $html.='background-color: green;';
					}else{
						$html.='background-color: red;';
						}
					$html.='color: white; height: 0px; width: 16%;">
				  Retail: '."$ ".number_format($product['priceretail'],2).'
				  </td>
				  <td style="vertical-align:  middle;  text-align: center; background-color:';
				  if($dateverification > $product['date_price_upd']||$datever==""){
					  $html.='red; color: white;';
				}else{
					$html.='green; color: white;';
					}
				$html.='height: 0px; width: 16%;">';
				if($dateverification > $product['date_price_upd']||$datever==""){
					$html.='<a href="modifierprix.php?clone=clone&product_id='.$product['product_id'].'">
				  <strong>Ebay: '."$ ".number_format($product['price_with_shipping'],2).' us</strong></a>';
				  }else{
					  $html.='Ebay: '."$ ".number_format($product['price_with_shipping'],2).' us';
					  }
					  $html.='</td>		
					</tr>
				  <tr>
				  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
				  </td>
				   <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
				   <a href="createbarcodetotal.php?all=no&type=both&qtemag=0&qtetot=1&product_id='.$_POST['product_id'].'&sku='.(string)$_POST['sku'].'" target="etiquette" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
				   </td>
				  </tr>
				  <tr>
				  <td></td>
				  <td style="vertical-align:  middle;  text-align: center; background-color: ';
				  if($dateverification > $product['date_price_upd_magasin']||$datevermag==""){
					  $err_o=1;
					  $html.='red; color: white;';
					  }else{
						  $html.='green; color: white;';
					}
					$html.='height: 0px; width: 16%;">';
				  if($dateverification > $product['date_price_upd_magasin']||$datevermag==""){
					  $err_o=1;
					  $html.='<a href="modifierprix.php?clone=clone&product_id='.$product['product_id'].'"><strong>Magasin: '."$ ".number_format($product['price_magasin'],2).' </strong></a>';
				}else{
				  $html.='Magasin: '."$ ".number_format($product['price_magasin'],2).' ';
				  }
				  $html.='</td>		     
				</tr>
				<tr>	
				  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
				  <a href="pretlister.php?product_id='.$product['product_id'].'&action=pretlister&new=0"><strong>Pr&ecirc;t a Lister</strong></a>
				  </td>
				 <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
				  <a  href="modificationitem.php?product_id='.$product['product_id'].'&action=listing"><strong>Modifier</strong></a> 
				  </td>
				</tr>
					<tr>
				  <td style="vertical-align:  middle;  text-align: center; background-color: #e4bc03; height: 0px; width: 16%;">
				  <a href="clonerproduct.php?etat='.$product['condition_id'].','.str_replace("_","",substr((string)$product['sku'] ,12,2)).'&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=modificationitemusa"><strong>Cloner</strong></a>
						  <td style="vertical-align:  middle;  text-align: center; background-color: red; color: white;height: 0px;">';
					$html.='<a href="uploadphoto.php?product_id='.$product['product_id'].'&lien_a_cloner='.$product['marketplace_item_id'].'&upc='.$product['upc'].'" >Importer photo eBay</a>';
				  $html.='</td>
				</tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité total:
				  </td>
				  <td style="vertical-align:  middle; text-align:center;width: 174px;';
				  if($product['unallocated_quantity']+$product['quantity'] <> $product['quantity_total']){
					  $html.='background-color:red; color: white;';
				}else{
					$html.='background-color:green; color: white;';
					}
					$html.='">
				  <a href="inventaire.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'">'.$product['quantity_total'].'</a> 
				  </td>
			 </tr>
				<tr>
				  <td style="vertical-align:  middle; background-color: #030282; color: white;">Location magasin:<br>
				  </td>
				  <td style="vertical-align:  middle;text-align:center;">'.$product['location_magasin'].'</td>
				</tr>
				<tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">Quantité
			magasin:<br>
				  </td>
				  <td style="vertical-align:  middle; text-align:center;width: 174px;">
				   <a href="inventairemagasin.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=default">'.$product['unallocated_quantity'].'</a>  
				  </td>
				</tr>
					<tr>
				  <td style="vertical-align:  middle; background-color: #030282; color: white;">Location entrep&ocirc;t:<br>
				  </td>
				  <td style="vertical-align:  middle;text-align:center;">'.$product['location_entrepot'].'</td>
				</tr>
				<tr>
				  <td style="vertical-align:  middle; width: 160px;  background-color: #030282; color: white;">
				  Quantité entrep&ocirc;t:
				  </td>
					<td style="vertical-align:  middle;text-align:center; ">
				  <a href="inventaireentrepot.php?lien=linsting&sku='.(string)$product['sku'].'&product_id='.$product['product_id'].'&action=default">'.$product['quantity'].'</a>  
				  </td>
				</tr>
			  </tbody>
			</table>
					</td>';
				if ($j==2 ||$j==5 || $j==8|| $j==11) 
					$html.='</tr>	<tr>';
			$j++;
		  }
		  $nu++;
	  }
	  $html.='</tr>
    </tbody>
  </table>';
  ?>
   <input type="hidden" name="hid_quantity_total" value="<? echo $product['quantity_total'];?>"/>
  	<input type="hidden" name="hid_product_id" value="<? echo $product['product_id'];?>"/>  
  <?
 }
  ?>
       <tr>
        <td colspan="1" rowspan="25" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;">
			<?if(isset($image_principal))echo '<img src="https://www.phoenixliquidation.ca/image/'.$image_principal.'" width="200">
			<br>
			<a href="uploadphoto.php?product_id='.$product_id_image_principal.'" >Modifier Photos</a>
        <br>
        <br>';?>
		<a href="uploadphoto.php?product_id=<? echo $product['product_id'];?>" >Modifier Photos</a>
        <br>
        </td>
		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 112px;">Sku
        </td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle;  text-align: center; height: 0px; ">
		<input id="sku"  type="text" name="sku"  value="" maxlength="255"  autofocus>
		<br><?echo $_POST['sku'];?>
		</td>
	 </tr>
		  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: #e4bc03; width: 112px;"> 
		 <strong>Afficher les non listé sur eBay (<?echo $_POST['nb_nonlistersurebay'];?>)</strong> 
		 <input type="checkbox" name="nonlister" value="oui" <?if($_POST['nonlister']=='oui')echo 'checked';?>/>	
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<input type="hidden" name="changeupc" value="<?echo $_POST['changeupc'];?>"/> 
		<input type="hidden" name="skuancien" value="<?echo $_POST['sku'];?>"/> 
		</td>
	  </tr>
	  <?
  echo $html;
?>
</form>
  <script type="text/javascript">
$( document ).ready(function() {
	start();
});
var x;
var startstop = 0;
function startStop() { /* Toggle StartStop */
  startstop = startstop + 1;
  if (startstop === 1) {
    start();
    document.getElementById("start").innerHTML = "Stop";
  } else if (startstop === 2) {
    document.getElementById("start").innerHTML = "Start";
    startstop = 0;
    stop();
  }
}
function start() {
  x = setInterval(timer, 10);
} /* Start */
function stop() {
  clearInterval(x);
} /* Stop */
var milisec = 0;
var sec = <?echo $_POST['hsec'];?>; /* holds incrementing value */
var min = <?echo $_POST['hmin'];?>;
var hour = <?echo $_POST['hhour'];?>;
/* Contains and outputs returned value of  function checkTime */
var miliSecOut = 0;
var secOut = 0;
var minOut = 0;
var hourOut = 0;
/* Output variable End */
function timer() {
  /* Main Timer */
  miliSecOut = checkTime(milisec);
  secOut = checkTime(sec);
  minOut = checkTime(min);
  hourOut = checkTime(hour);
  milisec = ++milisec;
  if (milisec === 100) {
    milisec = 0;
    sec = ++sec;
  }
  if (sec == 60) {
    min = ++min;
    sec = 0;
  }
  if (min == 60) {
    min = 0;
    hour = ++hour;
  }
 // document.getElementById("milisec").innerHTML = miliSecOut;
  document.getElementById("sec").innerHTML = secOut;
  document.getElementById("min").innerHTML = minOut;
  document.getElementById("hour").innerHTML = hourOut;
  document.getElementById("hsec").value = secOut;
  document.getElementById("hmin").value = minOut;
  document.getElementById("hhour").value = hourOut;
}
/* Adds 0 when value is <10 */
function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}
function reset() {
  /*Reset*/
  milisec = 0;
  sec = 0;
  min = 0
  hour = 0;
  document.getElementById("milisec").innerHTML = "00";
  document.getElementById("sec").innerHTML = "00";
  document.getElementById("min").innerHTML = "00";
  document.getElementById("hour").innerHTML = "00";
}
</script>
</body></html>
<?
//**/

mysqli_close($db);
// Flush the output buffer
ob_end_flush();
?>
