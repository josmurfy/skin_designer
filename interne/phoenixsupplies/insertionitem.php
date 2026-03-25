<?
include 'connection.php';
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
//include '/home/n7f9655/public_html/phoenixliquidation/interne/translatenew.php';
//print("<pre>".print_r ($_POST,true )."</pre>");
if(isset($_GET['hsec']) && $_GET['hsec']){
	$_POST['hsec']=$_GET['hsec'];
	$_POST['hmin']=$_GET['hmin'];
	$_POST['hhour']=$_GET['hhour'];
}elseif(!isset($_POST['hsec'])){
	$_POST['hsec']=0;
	$_POST['hmin']=0;
	$_POST['hhour']=0;
}
if(!isset($_POST['sku']) || $_POST['sku']!="" ){
	$_POST['sku']=$_POST['product_id']??0;//getRandomSku($db);
	
}
//echo "allo-->";

		
	if((isset($_POST) && ($_POST['product_id']=="" || $_POST['product_id']==0)) && count($_POST)>4 && $_POST['nameen'] !=""){
	//	echo count($_POST);
	//echo '<br>insert_item_db24:';
		$_POST['product_id']=insert_item_db($connectionapi,$_POST,$db);
}

if(isset($_GET['action']) && $_GET['action']=='exist'){
	$_POST['action']=$_GET['action'];
}else{
	$_POST['sku']=$_POST['product_id']??0;//substr((string)$_POST['sku'] ,0,12);
}
//print_r($_POST['accesoires']);
		if (isset($_FILES['imageprincipale']['size']) && $_FILES['imageprincipale']['size']>0){
		//delete_photo($_POST['product_id'],"principal",$db);
		//print("<pre>".print_r ($_FILES['imageprincipale'],true )."</pre>");
			$_POST['imageprincipale']=upload_image(0,1,$db);
		}
//
if (isset($_GET['sku']) && $_GET['sku'] != ""){
	(string)$_POST['sku']=(string)$_GET['sku'];
		if(isset($_GET['product_id'])){
			$_POST['product_id']=$_GET['product_id'];
//echo (string)$_POST['sku'] ;
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where language_id=1 and oc_product.product_id=oc_product_description.product_id and sku = "'.(string)$_POST['sku'] .'"';
			$sql = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=1 and P.product_id=PD.product_id and P.product_id = "'.$_POST['product_id'] .'"';
			$req = mysqli_query($db,$sql);
			//echo $sql;
			$data = mysqli_fetch_assoc($req);
			$_POST['condition_id']=$data['condition_id'];
			$_POST['ebay_id_a_cloner']=$result_ebay['ebay_id_a_cloner'];
			//verifie si variant lister
				$sqlexist = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=1 and P.product_id=PD.product_id and P.sku = "'.$data['sku'] .'" and PD.name!="" order by P.price DESC limit 1';
				$reqexist = mysqli_query($db,$sqlexist);
				$dataexist = mysqli_fetch_assoc($reqexist);
				if(count($dataexist)>0){
					$_POST['lien_a_cloner']=$dataexist['marketplace_item_id'];
					$data=$dataexist;
					$data['marketplace_item_id']=0;
					//echo "<br>CONDITION :".$dataexist['condition_id'];
					$_POST['ebay_id_a_cloner']=$dataexist['marketplace_item_id'];
					if($dataexist['condition_id']==9){
						$variantexist=1;
					}elseif($dataexist['condition_id']==99){
						$variantexist=.9;
					}elseif($dataexist['condition_id']==8){
						$variantexist=.9;
					}elseif($dataexist['condition_id']==7){
						$variantexist=.8;
					}elseif($dataexist['condition_id']==6){
						$variantexist=.75;
					}elseif($dataexist['condition_id']==5){
						$variantexist=.65;
					}elseif($dataexist['condition_id']==1){
						$variantexist=.5;
					}elseif($dataexist['condition_id']==2){
						$variantexist=.85;
					}elseif($dataexist['condition_id']==22){
						$variantexist=.85;
					}
					$_POST['price_with_shippingtemp']=$dataexist['price_with_shipping']/$variantexist;
					$data['condition_supp']="";
				}
			//print("<pre>".print_r ($data,true )."</pre>");
			//echo $data['name']
			//$_POST['nameen']=$data['name'];
			$sql3 = 'SELECT *,PD.color AS color_item FROM `oc_product` AS P,`oc_product_description` AS PD where PD.language_id=2 and P.product_id=PD.product_id and P.ebay_id >0 and P.product_id = "'.$_POST['product_id'] .'"';
	//echo $sql3;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			//print("<pre>".print_r ($data3,true )."</pre>");
			//print("<pre>".print_r ($data3,true )."</pre>");
			//$_POST['namefr']=htmlspecialchars($data3['name'],ENT_QUOTES,'ISO-8859-1', true);
			$_POST['namefr']=$data3['name'];
			if(mysqli_num_rows($req3)==0){
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$data['product_id']."', '', '', '2','')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";	
			}
			(string)$_POST['sku'] =$data['product_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			if($data['color_item']!=""){
				$_POST['coloren']=$data['color_anc'];
			}else{
				$_POST['coloren']=$data['color_item'];
			}
			$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			if($_POST['manufacturer_id']=="") $_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
			// $_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			(string)$_POST['upc']=(string)"DoesNotApply";
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
			$_POST['accessoryen']=$data['accessory'];
			$_POST['condition_suppen']=$data['condition_supp'];
			$_POST['testen']=$data['test'];
			$_POST['description_suppen']=$data['description_supp'];
			$_POST['coloren']=$data['color_item'];
			$_POST['accessoryfr']=$data3['accessory'];
			$_POST['condition_suppfr']=$data3['condition_supp'];
			$_POST['testfr']=$data3['test'];
			$_POST['description_suppfr']=$data3['description_supp'];
			$_POST['colorfr']=$data3['color_item'];
			$_POST['image']=$data['image'];
			$_POST['remarque_interne']=$data['remarque_interne'];
			$sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql4;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
			$_POST['categoryname']=$data4['name'];
			$_POST['category_id']=$data4['category_id'];
			$_POST['category_id']=$data4['category_id'];
		}else{
			$_POST['etape']=0;
			$_POST['marketplace_item_id']=0;
			$_POST['nameen']="";
			$_POST['namefr']="";
			$_POST['manufacturer_id']=0;
			$_POST['brand']="";
			$_POST['ebay_id_a_cloner']=$result_ebay['ebay_id_a_cloner'];
			$_POST['price_with_shipping']=0;//$result_ebay['price_with_shipping']-.05;
			$_POST['price']=0;
			$_POST['category_id']="";
			$_POST['categoryname']="";
			$_POST['model']="";
			$_POST['weight']=0;
			$_POST['weight2']=0;
			$_POST['length']=0;
			$_POST['width']=0;
			$_POST['height']=0;
			$_POST['sku']=$_GET['sku'];
			$_POST['condition_id']=9;
			$variant=1;
		}
		if($_POST['condition_id']==9){
			$variant=1;
			$_POST['etat']="9,";
		}elseif($_POST['condition_id']==99){
			$variant=.9;
			$_POST['etat']="99,NO";
		}elseif($_POST['condition_id']==8){
			$variant=.9;
			$_POST['etat']="8,LN";
		}elseif($_POST['condition_id']==7){
			$variant=.8;
			$_POST['etat']="7,VG";
		}elseif($_POST['condition_id']==6){
			$variant=.75;
			$_POST['etat']="6,G";
		}elseif($_POST['condition_id']==5){
			$variant=.65;
			$_POST['etat']="5,P";
		}elseif($_POST['condition_id']==1){
			$variant=.5;
			$_POST['etat']="1,FP";
		}elseif($_POST['condition_id']==2){
			$variant=.85;
			$_POST['etat']="2,SR";
		}elseif($_POST['condition_id']==22){
			$variant=.85;
			$_POST['etat']="22,R";
		}
		if(isset($_POST['price_with_shippingtemp']))
			$_POST['price_with_shipping']=$_POST['price_with_shippingtemp']*$variant;
		if($_POST['nameen']=="" || !isset($_POST['nameen'])){
			$_POST['manufacturer_id']=0;
			$_POST['upctemp']="";
			$result_upctemp=json_decode($_POST['upctemp'],true);
			$_POST['nameen']=addslashes($result_upctemp['items'][0]['title']);
			$_POST['nameen']=str_replace('Bilingual','Canadian Release',$_POST['nameen']);
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
//$_POST['sku']=substr((string)$_GET['upc'] ,0,12);
			//$search = array('lb', 'lbs');
			//$_POST['weight']=str_replace($search,"",$result_upctemp['items'][0]['weight']);
			$result_ebay=find_bestprice_ebay($connectionapi,$_POST['nameen'],"",1,1,'');
					   //find_bestprice_ebay($connectionapi,$q,string $gtin,$sort,$limit,$ebay_id)
			//print("<pre>".print_r ($result_ebay,true )."</pre>");
			if($result_ebay['ebay_id_a_cloner']>0){
				$_POST['ebay_id_a_cloner']=$result_ebay['ebay_id_a_cloner'];
				$_POST['category_id']=$result_ebay['category_id'];
				$productvariants=$result_ebay['pricevariant'];
				//$productvariants=json_decode($product['pricevariant']);
				//print("<pre>".print_r ($productvariants,true )."</pre>");
				$sql22 = 'SELECT conditions FROM `oc_conditions_to_category` CC LEFT JOIN `oc_conditions` AS C ON CC.conditions_id=C.conditions_id where CC.category_id= '.$result_ebay['category_id'];
				$req22 = mysqli_query($db,$sql22);
				$data22 = mysqli_fetch_assoc($req22);
				$conditions = json_decode($data22['conditions'], true);
				//print("<pre>".print_r ($conditions,true )."</pre>");
				$result_ebay['condition']=$conditions[$_POST['condition_id']]['value'];
				$pricelowest=99999;
				$variantlowest;
				$pricecheck=0;
				$varianthtml="";
				//print("<pre>".print_r ($productvariants,true )."</pre>");
				foreach($productvariants as $key=>$value){
					//echo $value['price'];
//echo "VALUEkey:".$key."VALUE cat".$result_ebay['condition']."<br>";
					//print("<pre>".print_r ( $value['price'],true )."</pre>");
					   if($value['price']<99999 ){
						if($result_ebay['condition']==$key){
							if($pricelowest>$value['price']){
								$pricelowest=$value['price'];
								$variantlowest=$key;
								if($variantlowest<>1000){
									$variant=1;
								}
							// $result_ebay['price_with_shipping']=($value['price']-.05)*$variant;
							//	$result_ebay['price']=$result_ebay['price_with_shipping']-3.54;
							}
						}
							if($result_ebay['condition']==$key){
								$pricecheck=$value['price'];
								$varianthtml.= "<strong>";
							}
							$varianthtml.= "[".$key."-$".number_format($value['price'], 2,'.', '')."]";
							if($result_ebay['condition']==$key)
							$varianthtml.= "</strong>";
						}                       
				}
//ECHO "pricelowest:".$pricelowest;
				//$_POST['price_with_shipping']=($result_ebay['price_with_shipping']*$variant)-.05;//$result_ebay['price_with_shipping']-.05;
				$_POST['price_with_shipping']=$pricelowest-.05;
				$_POST['categoryname']=$result_ebay['categoryname'];
				if ($_POST['nameen']==""){
					$_POST['nameen']=addslashes($result_ebay['name']);
					$_POST['nameen']=str_replace('Sealed','',$_POST['nameen']);
					$_POST['nameen']=str_replace('sealed','',$_POST['nameen']);
				}
				/*else{
					$_POST['weight']=$result_ebay['weight'];
					$_POST['weight2']=$result_ebay['weight2'];
					$_POST['length']=$result_ebay['length'];
					$_POST['width']=$result_ebay['width'];
					$_POST['height']=$result_ebay['height'];
				}*/
				if($result_ebay['model']!="")
					$_POST['model']=$result_ebay['model'];
				if($result_ebay['brand']!="")
					$_POST['brand']=$result_ebay['brand'];
				if($result_ebay['color']!="")
					$_POST['coloren']=$result_ebay['color'];
				//echo "browse fait";
				$_POST['browse_ebay']=$result_ebay['html'];
			}else{
				$_POST['nameen']=addslashes($result_upctemp['items'][0]['title']);
				$_POST['price_with_shipping']=9999;
			}
			$_POST['description_suppen']=addslashes($result_upctemp['items'][0]['description']);
		//	$_POST=translate_field($_POST);
			//$_POST['nameen']=stripslashes($_POST['nameen']);
			$_POST['description_suppen']=stripslashes($_POST['description_suppen']);
			$_POST['namefr']=htmlspecialchars($_POST['namefr'],ENT_QUOTES,'ISO-8859-1', true);
			$sql2 = 'SELECT * FROM `oc_manufacturer` where name like "%'.$_POST['brand'].'%" AND name not like ""';
			//echo $sql2;
					// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			if($data2['manufacturer_id']){
				$_POST['manufacturer_id']=$data2['manufacturer_id'];
			}else{
				$_POST['manufacturer_id']=0;
				$_POST['manufacturercheck']="";
			}
			echo "allo";
			if(!isset($_POST['product_id']))
			echo '<br>insert_item_db312:';
				$_POST['product_id']=insert_item_db($connectionapi,$_POST,$db);
			if($_POST['product_id']==0 || $_POST['product_id']==""){
			//	echo $_POST['product_id'];
				header("location: listing.php?action=error&hhour=".$_POST['hhour']."&hmin=".$_POST['hmin']."&hsec=".$_POST['hsec']);  
				exit(); 
			}
		}	
	}
	//$_POST['upctemp']=get_from_upctemp($_GET['upc']);
	//echo $_POST['etat'];
if(isset($_POST['priceusd']) && isset($_POST['priceusdtmp']) && $_POST['priceusd']<>$_POST['priceusdtmp']){
	$_POST['priceusdtmp']=$_POST['priceusd'];
	$_POST['pricecadtmp']=$_POST['priceusd']*1.34;
	$_POST['pricecad']=$_POST['priceusd']*1.34;
}
if(isset($_POST['pricecad']) && isset($_POST['pricecadtmp']) && $_POST['pricecad']<>$_POST['pricecadtmp']){
	$_POST['priceusdtmp']=$_POST['pricecad']/1.34;
	$_POST['priceusd']=$_POST['pricecad']/1.34; 
	$_POST['pricecadtmp']=$_POST['pricecad'];
}
if (isset($_GET['condition_insert']) && $_GET['condition_insert'] != ""){
	$_POST['condition_insert']=$_GET['condition_insert'];
	$_POST['etape']=0;
	}	
	if(isset($_POST['etat'])){
		$etat=explode(",",$_POST['etat']);
		$_POST['condition_id']=$etat[0];
		if(isset($_POST['clone'])&&$_POST['clone']!=""&&$_POST['action']!='listing'){
			$_POST['sku']=$_POST['product_id']??0;//substr((string)$_POST['sku'] ,0,12).$etat[1]."_".$_POST['clone'];
		}elseif($_POST['action']!='listing'){
			$_POST['sku']=$_POST['product_id']??0;//substr((string)$_POST['sku'] ,0,12).$etat[1];
		}
	}
//print("<pre>".print_r ($_POST,true )."</pre>");
//translate('Hello my name is Jonathan');
$upc=(string)"DoesNotApply";
//echo $_POST['frenchcheck'];
if(isset($_POST['category_id'])){
	if($_POST['category_id']==139973 || $_POST['category_id']==617)
		{
			$_POST['conditioncheck']="oui";
			$_POST['modelcheck']="oui";
			$_POST['poidscheck']="oui";
			$_POST['colorcheck']="oui";
			$_POST['infosuppcheck']="oui";
			$_POST['dimensioncheck']="oui";
			$_POST['categoriecheck']="oui";
		}
	//	//print("<pre>".print_r ($_POST,true )."</pre>");
	if($_POST['category_id']==280 && $_POST['manufacturer_id']==1270)
		{
			
			$_POST['coloren']="Yellow cover";
			$_POST['coloren']="Couverture Jaune";
			$_POST['length']="7";
			$_POST['width']="5";
			$_POST['height']="1";
			$_POST['weight']="0";
			$_POST['weight2']="12";
			if($_POST['condition_suppen']==""){
				$_POST['condition_suppen']="May or May NOT have Map or Insert in the magazine";
				$_POST['condition_suppfr']="Peut ou NE PEUT PAS avoir de carte ou d'encart dans le magazine";
			}
			if($_POST['manufacturer_id']==1270)
				$_POST['manufacturercheck']="oui";
			$_POST['poidscheck']="oui";
			$_POST['categoriecheck']="oui";
			$_POST['colorcheck']="oui";
			$_POST['infosuppcheck']="oui";
			$_POST['dimensioncheck']="oui";
			$_POST['categoriecheck']="oui";
		}
	}
if(isset($_POST['ebay_id_a_cloner'])&& $_POST['ebay_id_a_cloner']!="" && ($_POST['processing']=="oui"
	&& $_POST['conditioncheck']=="oui" && $_POST['modelcheck']=="oui"
	&& $_POST['manufacturercheck']=="oui" && $_POST['categoriecheck']=="oui"
	&& $_POST['frenchcheck']=="oui" && $_POST['englishcheck']=="oui" && $_POST['etat']!=""
	&& $_POST['priceebaycheck']=="oui") && $_POST['dimensioncheck']=="oui"
	&& $_POST['poidscheck']=="oui" && $_POST['infosuppcheck']=="oui" && $_POST['manufacturer_id']!=0
	&& $_POST['colorcheck']=="oui"){
	$ebayresult=get_ebay_product($connectionapi,$_POST['ebay_id_a_cloner']);
	$tempsEtat=explode(",",$_POST['etat']);
$_POST['condition_id']=$tempsEtat[0];
	$json=add_ebay_item($connectionapi,$ebayresult,$_POST,$db); 
//	//print("<pre>".print_r ($json,true )."</pre>");
}
if((isset($json['ItemID']) && $json['ItemID']!="")|| (isset($json['processing']) && $_POST['processing']=="oui"  && isset($json['pas_ebay']) && $_POST['pas_ebay']=="oui" )){
		$post= array();
		if(isset($json['ItemID'])){
			$_POST['marketplace_item_id']=$json['ItemID'];
		}else{
			$_POST['marketplace_item_id']=0;
		}
		$post= $_POST;
		//echo $_POST['clone'];
		//$_POST['product_id']=insert_item_db($connectionapi,$_POST,$db);
		//echo $_POST['sourcecode'];
		if($_POST['sourcecode']!=""){
			//echo "oui";
/* 				unlink($GLOBALS['SITE_ROOT'].'interne/test.txt');
				link($GLOBALS['SITE_ROOT'].'interne/test.txt');
				$fp = fopen($GLOBALS['SITE_ROOT'].'interne/test.txt', 'w');
				fwrite($fp, html_entity_decode("test".$_POST['sourcecode']));  */
			link_to_download($connectionapi,$_POST['product_id'],$_POST['sourcecode'],"sourcecodenew",$db);
		}elseif($_POST['lien_a_cloner']){
			link_to_download($connectionapi,$_POST['product_id'],$_POST['lien_a_cloner'],"",$db); 
		}
		//unlink($GLOBALS['SITE_ROOT'].'image/' . $data['image']); 
		$etat=explode(",",$_POST['etat']);
		$_POST['condition_id']=$etat[0];
			if(isset($_POST['clone'])&&$_POST['clone']!=""&&$_POST['action']!='listing'){
				$_POST['sku']=$_POST['product_id']??0;//substr((string)$_POST['sku'] ,0,12).$etat[1]."_".$_POST['clone'];
			}elseif($_POST['action']!='listing'){
				$_POST['sku']=$_POST['product_id']??0;//substr((string)$_POST['sku'] ,0,12).$etat[1];
			}	
			$_POST['price']=0;
			update_item_db($connectionapi,$_POST,$db);		 					
		//$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],"non",$db);
		//$json = json_decode($result, true); 
		$_POST['info_ebay']=$json;
		if($json["Ack"]=="Failure"){
			$resultebay.="ERREUR: ";//print("<pre>".print_r ($json,true )."</pre>");
		} else{
			//echo $_POST['sku'];
			header("location: uploadphoto.php?action=insert&product_id=".$_POST['product_id']."&hhour=".$_POST['hhour']."&hmin=".$_POST['hmin']."&hsec=".$_POST['hsec']);  
			exit();  
		}
}
//echo $_POST['sku'];
						/*	$_POST['condition_suppen'].=$tabnocollector[0];
							$_POST['condition_suppfr'].=$tabnocollector[1]; 
							$tabnocollector=explode("@",$_POST['dvdcase']);
							$_POST['condition_suppen'].=$tabnocollector[0];
							$_POST['condition_suppfr'].=$tabnocollector[1];*/

	if (isset($_POST['manufacturer_id']) && $_POST['manufacturer_id']!="" && isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']=="")
	//	$_POST['manufacturer_id']=$_POST['manufacturer_id'];
 		if (isset($_POST['manufacturersupp']) && $_POST['manufacturersupp']!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id']= mysqli_insert_id($db);
			$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturersupp']="";
			$_POST['brand']=$_POST['manufacturersupp'];
		}
		if (isset($_POST['manufacturer_recom']) && $_POST['manufacturer_recom']!=""){
			$_POST['manufacturer_id']=$_POST['manufacturer_recom'];
		} 
 			if (isset($_POST['category_id']) && $_POST['category_id']!=""){
					$sql4 = 'SELECT * FROM `oc_category_description` where oc_category_description.category_id="'.$_POST['category_id'].'" and language_id=1';
				//	echo $sql;
					$req4 = mysqli_query($db,$sql4);
					$data4 = mysqli_fetch_assoc($req4);
					
				/*	if(($_POST['category_id']==617 || $_POST['category_id']==139973)&& $_POST['condition_suppen']=="" ){
						$_POST['weight']=0;
						$_POST['weight2']=4;
						$_POST['length']=7;
						$_POST['width']=5;
						$_POST['height']=1;
						if($_POST['condition_id']!=9){
						//	$_POST['condition_suppen']='Comes from a former rental store, could have a RFID sticker in the middle of the disk, no Digital Code included';
						//	$_POST['condition_suppfr']='Provient d\'un ancien magasin de location, pourrait avoir un autocollant RFID au milieu du disque, pas de code numérique inclus';
						}
						$_POST['infosuppcheck']='oui';
						$_POST['poidscheck']='oui';
						$_POST['dimensioncheck']='oui';
						$_POST['colorcheck']='oui';
						$_POST['modelcheck']='oui';
						$_POST['model']='none';
					}*/
					if($data4['category_id']==''){
					//	$response = getCategoriesRequest($connectionapi,$category_id);
				//		echo $response;

						echo "<script>window.open('https://phoenixliquidation.ca/interne/phoenixsupplies/ajoutcategorie.php?primary_cat=".$_POST['category_id']."');</script>";  
										exit();
					}
					$_POST['categoryname']=$data4['name'];
					$_POST['category_id']=$data4['category_id'];
			} 
			if(isset($_POST['manufacturer_id']) && $_POST['manufacturer_id']!=""){
					$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'];
					// on envoie la requête
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					$_POST['brand']=$data2['name'];
			} //||($_POST['accessoryfr']==""&& $_POST['accessory']!="")||($_POST['condition_suppfr']==""&& $_POST['condition_suppen']!="")|| || ($_POST['testfr']==""&& $_POST['test']!="")
 if	(($_POST['namefr']==""&& $_POST['nameen']!="")||($_POST['colorfr']==""&& $_POST['coloren']!="") )
			{
			//	$_POST['nameen']=addslashes($_POST['nameen']);
		//		$_POST['description_suppen']=addslashes($_POST['description_suppen']);
			//	$_POST=translate_field($_POST);
		//		$_POST['nameen']=stripslashes($_POST['nameen']);
		//		$_POST['description_suppen']=stripslashes($_POST['description_suppen']);
				//print("<pre>".print_r ($_POST,true )."</pre>");
			}
// mettre le focus sur le prochain champ
if(isset($_POST) && !isset($_GET['randomupc']))
	update_item_db($connectionapi,$_POST,$db);


if($_POST['priceebaycheck']==""){
	$focus="priceebaycheck";
}elseif($_POST['conditioncheck']==""){
	$focus="conditioncheck";
}elseif($_POST['englishcheck']==""){
	$focus="englishcheck";
}elseif($_POST['frenchcheck']==""){
	$focus="frenchcheck";
}elseif($_POST['modelcheck']==""){
	$focus="modelcheck";
}elseif($_POST['manufacturercheck']==""){
	$focus="manufacturercheck";
}elseif($_POST['categoriecheck']==""){
	$focus="categoriecheck";
}elseif($_POST['colorcheck']==""){
	$focus="colorcheck";
}elseif($_POST['dimensioncheck']==""){
	$focus="dimensioncheck";
}elseif($_POST['poidscheck']==""){
	$focus="poidscheck";
}elseif($_POST['infosuppcheck']==""){
	$focus="infosuppcheck";
}elseif($_POST['conditioncheck']=="oui" && $_POST['modelcheck']=="oui"
	&& $_POST['manufacturercheck']=="oui" && $_POST['categoriecheck']=="oui"
	&& $_POST['frenchcheck']=="oui" && $_POST['englishcheck']=="oui" && $_POST['etat']!=""
	&& $_POST['priceebaycheck']=="oui" && $_POST['pricedetailcheck']=="oui" && $_POST['dimensioncheck']=="oui"
	&& $_POST['poidscheck']=="oui" && $_POST['infosuppcheck']=="oui" && $_POST['manufacturer_id']!=""
	&& $_POST['colorcheck']=="oui"){
	$focus="processing";	
}else{
	$focus="initial2";
}
//echo "**".$focus."**";
$keywords = array();
if (isset($_POST['nameen']) && $_POST['nameen'] !== "") {
 //   $keywords[] = 'product name: ' . $_POST['nameen'];
	$keywords[]  = 'product name: ' . iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $_POST['nameen']);
}
if (isset($_POST['condition_name']) && $_POST['condition_name'] !== "") {
    $keywords[] = 'Product Condition: ' . $_POST['condition_name'];
}



if (isset($_POST['brand']) && $_POST['brand'] !== "" && $_POST['manufacturer_id']>0)  {
    $keywords[] = 'brand name: ' . $_POST['brand'];
	
}

if (isset($_POST['categoryname']) && $_POST['categoryname'] !== "") {
    $keywords[] = 'Category: ' . $_POST['categoryname'];
}

if (isset($_POST['condition_suppen']) && $_POST['condition_suppen'] !== "") {
    $keywords[] = 'Additional product info: ' . $_POST['condition_suppen'];
}

if (isset($_POST['description_suppen']) && $_POST['description_suppen'] !== "") {
    $keywords[] = 'Additional product description: ' . $_POST['description_suppen'];
}

// Utilisation de $keywords


// Vous pouvez ensuite utiliser $keywords comme nécessaire


//print("<pre>".print_r ($_POST,true )."</pre>");
//print("<pre>".print_r ($keywords,true )."</pre>");
if(count($keywords)>0){
	$ChatGPT_Title="<br>".generateOptimizedTitle_CHATGPT($keywords);
}else{
	$ChatGPT_Title='';
}
?>
<html> 
<head>
<?php header("Content-Type: text/html; charset=iso-8859-1"); ?>
    <title></title>
			<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/bootstrap/js/bootstrap.min.js"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/common.js" type="text/javascript"></script>
	
	<script src="/interne/js/tinymce/tinymce.min.js"></script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="../stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="<?if(isset($resultebay)){?>red<?}else{?>ffffff<?}?>">
<form action="insertionitem.php" method="post">
  <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="3" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>/image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
      <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		
        </td>
        <td colspan="3" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
		<h1>ADD new product (<?echo $_POST['product_id'];?>)</h1>
		</td>
     </tr>
	 	<tr>
		 <td colspan="3" style="height: 50; background-color: green; color: white;  text-align: center; "> 
			<span id="hour" ><?echo $_POST['hhour'];?></span>:<span id="min"><?echo $_POST['hmin'];?></span>:<span id="sec"><?echo $_POST['hsec'];?></span>
			<input type="hidden" name="hhour" id="hhour" value="<?echo $_POST['hhour'];?>" />
			<input type="hidden" name="hmin" id="hmin" value="<?echo $_POST['hmin'];?>" />
			<input type="hidden" name="hsec" id="hsec" value="<?echo $_POST['hsec'];?>" />
			<input type="hidden" name="action" value="<?echo $_POST['action'];?>" />
			<input type="hidden" name="sku" value="<?echo $_POST['sku'];?>" />
		</td>
     </tr>
	 	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<input type="checkbox" name="processing" value="oui"  <?if($focus=="processing")echo "checked autofocus";?>/>	Procede 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</td>
	  </tr>
	 	 		<?/*if($resultebay!=""){?>
			   <tr>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;background-color:red;color:white">
			<?echo $resultebay;?>
        </td>
	  </tr>
		<?}*/?>
		<tr>
		<td>
		</td>
	     <td colspan="2" style="vertical-align:  middle; text-align:center;height: 16px; background-color: white; width: 200px;"> 
		 <?if (isset($json)){?>
	<?
			echo '<br><textarea name="json" rows="25" cols="10" placeholder="output ebay" id="info_ebay" disabled >';
			//print("<pre>".print_r ($json,true )."</pre>");
			echo '</textarea>';
			?>
<?}else{?>	
		 <?//echo $_POST['browse_ebay']; ?>
<?}?>
		</td>	
	 </tr>
	 <tr><td></td>
	         <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
			 <div id="image_import"></div>
			 <input type="file" name="imageprincipale" class="ed"><br>
			 	<input id="lien_a_cloner"  type="text" name="lien_a_cloner"  value="<?echo $_POST['lien_a_cloner'];?>" maxlength="255" <?if($focus=="initial")echo "autofocus";?>><br>
 <textarea name="sourcecode" rows="5" cols="5" placeholder="copiez le code source des images a télécharger" id="sourcecode" class="form-control"><?echo $_POST['sourcecode']?></textarea>
		</td>
		</tr>
	 <tr>
       <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
		<label>Categorie:</label>     
		</td>
        <td style="vertical-align:  middle; text-align:center; <?if($_POST['categoriecheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		<input type="checkbox" id="verif"  name="categoriecheck" value="oui" <?if($_POST['categoriecheck']=="oui")echo "checked";?> <?if($focus=="categoriecheck")echo "autofocus";?>/> 
    <input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> 
		<?if ($_POST['categoryname']!=""){?><br><label>Categorie found: <span style="color: #ffffff;"><?echo $_POST['categoryname'];?></span></label><?}?>
		</td>
      </tr>
	  		<?if (!isset($_POST['ebay_id_a_cloner'])||$_POST['ebay_id_a_cloner']=="")$_POST['ebay_id_a_cloner']= $_POST['marketplace_item_id'];?>
	<tr>
		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			Ebay ID to CLONE: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center;height: 0px; width: 400px;">
			<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" >
			<br><input type="checkbox" name="pas_ebay" value="oui" <?if($_POST['pas_ebay']=="oui"){?>checked<?}?> />	Pas eBay
<br><input type="checkbox" name="pas_upc" value="does not apply" <?if($_POST['pas_upc']=="does not apply"){?>checked<?}?> />	UPC does not apply			
		</td>
	</tr>
			 <tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Price EBAY
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align: center;height: 0px; <?if($_POST['priceebaycheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		(finir par <?echo $endprix;?> en dollar am&eacute;ricain)<br>
		
				<input type="checkbox" name="priceebaycheck" value="oui" <?if($_POST['priceebaycheck']=="oui")echo "checked";?> <?if($focus=="priceebaycheck")echo "autofocus";?>/> 
	
				<input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping']+$_POST['price_with_shippingshipping'], 2,'.', '');?>" size="5" />
		<br>Shipping ebay<br><input id="price_with_shippingshipping"  type="text" name="price_with_shippingshipping" value="" size="10" />
		</td>
</tr>
	 <tr>
	  <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;"> 
		SKU:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<input id="sku"  type="text" name="sku" value="<?echo $_POST['sku'];?>" size="30" />
		<input type="hidden" name="upc" value="DoesNotApply"/>	
		
		</td>
	</tr>
<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			Condition: 
        </td>
	        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center;height: 50px; <?if($_POST['conditioncheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
						<input type="checkbox" name="conditioncheck" value="oui" <?if($_POST['conditioncheck']=="oui")echo "checked";?> <?if($focus=="conditioncheck")echo "autofocus";?>/> 
		<table width="100%">
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9," <?if ($_POST['etat']=="9,"){?>checked<?}?>/> 
				<label class="choice" for="etat_1">NEW</label></td>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99,NO" <?if ($_POST['etat']=="99,NO"){?>checked<?}?>/> 
				<label class="choice" for="etat_2">OpenBox (GRADE A)</label> <br /></td>
		  </tr>
		  <tr>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2,SR" <?if ($_POST['etat']=="2,SR"){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22,R" 
			<?if ($_POST['etat']=="22,R"){?>checked<?}?>/> 
				<label class="choice" for="etat_3">OpenBox (GRADE B)</label> <br /> </td>
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
				</tr>
				<tr>
			<td colspan="2" style="text-align: center;"><label>Clone:</label>
			<input type="text" name="clone" class="element radio" value="<?echo $_POST['clone'];?>" maxlength="4" /></td>
		  </tr>
		</table>
		<?php
// Détermine le nom de l'état en fonction de la valeur de `etat`
$condition_name = "";
switch ($_POST['etat']) {
    case "9,":
        $condition_name = "NEW";
        break;
    case "99,NO":
        $condition_name = "OpenBox (GRADE A)";
        break;
    case "2,SR":
        $condition_name = "Manufacturer Refurbished";
        break;
    case "22,R":
        $condition_name = "OpenBox (GRADE B)";
        break;
    case "8,LN":
        $condition_name = "Used - Like New";
        break;
    case "7,VG":
        $condition_name = "Used - Very Good";
        break;
    case "6,G":
        $condition_name = "Used - Good";
        break;
    case "5,P":
        $condition_name = "Used - Poor";
        break;
    case "1,FP":
        $condition_name = "For Parts Or For Repair";
        break;
}

// Affiche le champ caché `condition_name` avec la valeur déterminée
echo '<input type="hidden" name="condition_name" value="' . $condition_name . '">';
?>
				</td>
	</tr>
	<tr>
        <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
			<label>English Title:</label>
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['englishcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
					<input type="checkbox" id="verif"  name="englishcheck" value="oui" <?if($_POST['englishcheck']=="oui")echo "checked";?>  <?if($focus=="englishcheck")echo "autofocus";?>/> 
					<input id="nameen"  type="text" name="nameen" value="<?echo ($_POST['nameen']);?>"  maxlength="80" onchange="getTranslate(document.getElementById('nameen').value,'name','fr')"/>
	<?echo $ChatGPT_Title;?>
				</td>
	</tr>
      <tr>
      <td style="vertical-align:  middle; height: 50px; background-color: #030282; color: white; width: 200px;">
		 <label>French Title: </label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['frenchcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		 <input type="checkbox" id="verif"  name="frenchcheck" value="oui" <?if($_POST['frenchcheck']=="oui")echo "checked";?>   <?if($focus=="frenchcheck")echo "autofocus";?>/> 
	<input id="namefr"  type="text" name="namefr" value="<?echo htmlspecialchars($_POST['namefr'], ENT_QUOTES, 'iso-8859-1');?>" maxlength="255" onchange="getTranslate(document.getElementById('namefr').value,'name','en')"/></h3>   
		</td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 200px;">
		<label>Model:</label>
		</td>
        <td style="vertical-align:  middle; height: 25px;  text-align:center;<?if($_POST['modelcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		 <input type="checkbox" id="verif"  name="modelcheck" value="oui" <?if($_POST['modelcheck']=="oui")echo "checked";?> <?if($focus=="modelcheck")echo "autofocus";?>/> 
		 <input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="20" />
		</td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; background-color: #030282; color: white; width: 200px;">
		<label>Manufacturier:</label>         
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center;  <?if($_POST['manufacturercheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
<div class="label">
<input type="checkbox" id="verif"  name="manufacturercheck" value="oui" <?if($_POST['manufacturercheck']=="oui")echo "checked";?>  <?if($focus=="manufacturercheck")echo "autofocus";?>/> 
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
						$test1=strtolower ($_POST['nameen']);
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
		<label>Add if not in the list:</label> <br><input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" />
		</div>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>Color</label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 50px;text-align:center; <?if($_POST['colorcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
				<input type="checkbox" id="verif"  name="colorcheck" value="oui" <?if($_POST['colorcheck']=="oui")echo "checked";?>   <?if($focus=="colorcheck")echo "autofocus";?>/> <br>
		<label>English</label> <input type="text" id="coloren" name="coloren" value="<?echo htmlspecialchars($_POST['coloren'], ENT_QUOTES, 'UTF-8');?>" maxlength="80" onchange="getTranslate(document.getElementById('coloren').value,'color','fr')"/><br>
		<label>French</label> <input type="text" id="colorfr" name="colorfr" value="<?echo htmlspecialchars($_POST['colorfr'], ENT_QUOTES, 'iso-8859-1');?>" maxlength="80" onchange="getTranslate(document.getElementById('colorfr').value,'color','en')"/>
		</td>
      </tr>
	        <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;"><label>Dimension:</label>
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['dimensioncheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
				<input type="checkbox" id="verif"  name="dimensioncheck" value="oui" <?if($_POST['dimensioncheck']=="oui")echo "checked";?>   <?if($focus=="dimensioncheck")echo "autofocus";?>/> 
	<table width="100%">
		  <tr>
			<td style="text-align:center;">
			<label>Length</label> <input id="length" class="small_text" type="text" name="length" value="<?echo intval($_POST['length']);?>" maxlength="5" />
				</td>
					</tr>
				<tr>
				<td style="text-align:center;">
			<label>Width</label> <input id="width"  class="small_text" type="text" name="width" value="<?echo intval($_POST['width']);?>" maxlength="5" />
				</td>
								</tr>
				<tr>
				<td style="text-align:center;">
			<label>Height</label> <input id="height"  class="small_text" type="text" name="height" value="<?echo intval($_POST['height']);?>" maxlength="5" />
				</td>
			</tr>
			</table>
		</td>	
      </tr>
      <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>Weight:</label>		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['poidscheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
		<input type="checkbox" id="verif"  name="poidscheck" value="oui" <?if($_POST['poidscheck']=="oui")echo "checked";?> <?if($focus=="poidscheck")echo "autofocus";?>/> 
<table width="100%" style="vertical-align:  middle; text-align:center;text-align: center;height: 16px; ">
		  <tr>
			<td style="text-align:center;"><label>Lbs</label> <input id="weight"  class="small_text" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" />
				</td>
											</tr>
				<tr>	
		<td style="text-align:center;"><label>Oz</label> <input id="weight2"  class="small_text" type="text" name="weight2" value="<?echo $_POST['weight2'];?>" maxlength="5" />
		</td>
		</tr>
</table>
      </tr>
	  	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px;text-align:center; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<a href="createbarcodetotal.php?all=no&type=both&qtemag=0&qtetot=1&product_id=<?echo $_POST['product_id'];?>&all=yes&sku=<?echo (string)$_POST['sku']?>" target="etiquette" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<input type="checkbox" id="verif"  name="processing" value="oui" />	<label>Procede </label>
		<input type="checkbox" id="verif"  name="showerror" value="oui" />	<label>Show Error</label>
		<input id="saveForm1" class="button_text" type="submit" name="submit" value="Submit" />
		</td>
	  </tr>
	    <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">	
		<label>Extra Info:</label>		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 50px;text-align:center; <?if($_POST['infosuppcheck']==""){?>background-color: red;<?}else{?> background-color: green;<?}?>">
	<input type="checkbox" id="verif"  name="infosuppcheck" value="oui" <?if($_POST['infosuppcheck']=="oui")echo "checked";?> <?if($focus=="infosuppcheck")echo "autofocus";?>/> 
		<table width="100%">
		  <tr>
		  <td style="text-align:center;"><label>English</label> <input type="text" id="condition_suppen" name="condition_suppen" value="<?echo htmlspecialchars($_POST['condition_suppen'], ENT_QUOTES, 'UTF-8');?>" maxlength="300" onchange="getTranslate(document.getElementById('condition_suppen').value,'condition_supp','fr')"/></td>
		  </tr>
		  <tr style="text-align:center;">
			<td><label>French</label> <input type="text" id="condition_suppfr" name="condition_suppfr" value="<?echo htmlspecialchars($_POST['condition_suppfr'], ENT_QUOTES, 'iso-8859-1');?>" maxlength="300" onchange="getTranslate(document.getElementById('condition_suppfr').value,'condition_supp','en')"/></td>
		  </tr>
		</table>
	</td>
	</tr>
     <tr>
        <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;"><label>Description:</label></td>
        <td colspan="1" rowspan="1" style="vertical-align:  text-top; height: 50px;text-align:center;">
		<label>English</label><br><textarea  name="description_suppen" class="tinymce" rows="10" cols="50" placeholder="Description" id="description_suppen"  onchange="getTranslate(document.getElementById('description_suppen').value,'description_supp','fr')"><?echo htmlspecialchars($_POST['description_suppen'], ENT_QUOTES, 'UTF-8');?></textarea><br>
		<label>French</label><br><textarea name="description_suppfr" class="tinymce" rows="10" cols="50" placeholder="Description in French" id="description_suppfr"  onchange="getTranslate(document.getElementById('description_suppfr').value,'description_supp','en')"><?echo htmlspecialchars($_POST['description_suppfr'], ENT_QUOTES, 'iso-8859-1');?></textarea>
		</td>
      </tr>
	    <tr>
		 <td style="vertical-align:  middle;  background-color: #030282; color: white; width: 200px;">
		<label>COMMENT:</label> 
		</td>
		<td style="vertical-align:  middle; text-align:center; ">
		<input id="remarque_interne"  type="text" name="remarque_interne" value="<?echo $_POST['remarque_interne'];?>" maxlength="255" />
		</td>
		</tr>
	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 50px;text-align:center; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<a href="createsmallbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<input type="checkbox" name="processing" value="oui" />	<label>Procede </label>
		<input type="checkbox" name="showerror" value="oui" />	<label>Show Error</label>
		<input id="saveForm2" class="button_text" type="submit" name="submit" value="Submit" />
		</td>
	  </tr>
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape']??"";?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum??"";?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="upc" value="DoesNotApply" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner']??"";?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		<input type="hidden" name="condition_insert" value="<?echo $_POST['condition_insert']??"";?>" />
		<input type="hidden" name="old_token" value="<?echo $_POST['token']??"";?>" />
		<?/*<input type="hidden" name="upctemp" value="<?echo $_POST['upctemp'];?>" />*/
		?>
		<input type="hidden" name="brand" value="<?echo $_POST['brand'];?>" />
		<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>" />
		<input type="hidden" name="imageprincipale" value="<?echo $_POST['imageprincipale'];?>" />
		<input type="hidden" name="priceusdtmp" value="<?echo $_POST['priceusd'];?>" />
		<input type="hidden" name="pricecadtmp" value="<?echo $_POST['priceusd']*1.34;?>" />
    </tbody>
  </table>
 
  <script type="text/javascript">

$(function(){
    $( "input[type=checkbox]" ).on("change", function(){
        if($(this).is(':checked'))
            $(this).parent().css('background-color', 'green');
        else
            $(this).parent().css('background-color', 'red');
    });
});

function getTranslate(text_field,field,targetLanguage) {
	//alert(order_status_id_mod);
       $.ajax({
			method: "POST",
			url: '../translatenew.php',//'+ text_field +'
			data:{targetLanguage:targetLanguage,text_field:text_field},
            cache: false,
            dataType: "JSON",  
			beforeSend: function() {
						//	$('#button-customer').button('loading');
				/* 	document.getElementById("refresh").classList.remove('fa','fa-refresh');
					//$('#button-customer').button('loading');
					document.getElementById("refresh").classList.add('fa','fa-refresh','fa-spin');
				document.getElementById("alert-message").style.display = "block";	 */
			},
			complete: function() {
			/* 	 $('#button-customer').button('reset');
				 document.getElementById("alert-message").style.display = "none"; */
			}, 
			success:function(json) {
			   // location.reload();//if (text_field!="")
				 //  alert(json['success']);
				  document.getElementById(field+targetLanguage).value=decodeHtml(json['success']);
			   	/* document.getElementById("refresh").classList.remove('fa','fa-refresh','fa-spin');
				document.getElementById("refresh").classList.add('fa','fa-refresh'); */
				//alert("name"+targetLanguage);
			},
			error:function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
      }); 
 }
var x;
var startstop = 0;
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
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
<?if ( isset($_POST['nameen']) &&$_POST['nameen'] !="" ){?>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['nameen'];?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_ItemCondition=3&LH_Complete=1&LH_BIN=1","ebaysold");
//window.open("https://www.upcitemdb.com/upc/<?echo (string)$_POST['upc'];?>","upcitemdb");
window.open("https://www.ebay.com/sch/i.html?_from=R40&_nkw=<?echo (string)$_POST['nameen'];?>&_sacat=0&_sop=15&rt=nc&LH_BIN=1","ebayactive");
window.open("https://www.ebay.com/sh/research?marketplace=EBAY-US&keywords=<?echo (string)$_POST['upc'];?>&dayRange=1095categoryId=0&tabName=SOLD&tz=America%2FToronto","terapeak");

</script>

<?}?>
<script> 
  tinymce.init({
  selector: 'textarea.tinymce',
  plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
  toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
});
 </script>
<input type="hidden" name="start" value="1" />
</form>
</body>
</html>
<?  
// on ferme la connexion à mysql 
mysqli_close($db); ?> ?>