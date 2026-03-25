<? 
//echo (string)$_POST['sku'] ;

//print("<pre>".print_r ($_POST,true )."</pre>");
// on se connecte &agrave; MySQL 
ob_start();
include 'connection.php';
// Utilisation de isset() pour vérifier les clés de $_POST



$sku=(string)$_POST['sku'] ;


if(($_POST['quantitymagasin_ajouter']+$_POST['quantityentrepot_ajouter'])>0){
	//echo "allo";
	echo '<script type="text/javascript">
	loadOtherPage1();
	</script>'
;
}
//print("<pre>".print_r ($_POST,true )."</pre>");
/*if(isset($_GET['listetsy'])){
	add_etsy_item($db,$_GET['product_id']);
}
if($_GET['sku']!="")$_POST['sku']=$_GET['sku']	;
if($_POST['changeupc']=="changer"){
		$sql = 'SELECT * FROM oc_product where upc = "'.(string)$_POST['upc'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requ�te
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
}
if($_GET['changeupc']=="oui"){
	$_POST['changeupc']="changer";
}*/
if(isset($_POST['ebay_id_a_cloner'])&&$_POST['ebay_id_a_cloner']!="" ){
	//echo $_POST['ebay_id_a_cloner'];
	$ebayresult=get_ebay_product($connectionapi,$_POST['ebay_id_a_cloner']);
	//echo '<script>window.open("https://bulksell.ebay.com/ws/eBayISAPI.dll?SingleList&sellingMode=SellLikeItem&lineID='.$_POST['ebay_id_a_cloner'].'","ebaylisting")</script>';
	//print("<pre>".print_r ($ebayresult,true )."</pre>");
	//$result_upctemp=get_from_upctemp($_POST['upc']);
	$json=add_ebay_item($connectionapi,$ebayresult,$_POST,$db);
	$_POST['marketplace_item_id']=$json['ItemID'];  
	$_POST['new_ebay_id']=$_POST['marketplace_item_id'];
	if($json["Ack"]=="Failure"){
		//print("<pre>".print_r ($json,true )."</pre>");
	}
	$_POST['ebay_id_a_cloner']="";

}

//getebayproduct($connectionapi,402354604983);
if ($_GET['action']=="ajouter"){
	(string)$_POST['sku'] =$_GET['sku'];	
	 $test= ajouter_item ($connectionapi,$_GET['sku'],$db); 
	//echo 'allo'; 
}
if (isset ($_GET['product_id'])  ){
	//(string)$_POST['sku']=$_GET['sku'];	
	$_POST['product_id']=$_GET['product_id'];
	//echo $_GET['product_id'];
	//echo $_POST['product_id'];
//echo 'allo'; 
}
//echo $_POST['new'];
/*if ((string)$_POST['sku'] !=$_POST['skuold'] && $_POST['skuold']!=""){
	(string)$_POST['sku'] =$_POST['skuold'];
	$_POST['quantitymagasin_ajouter']="";
}*/
if($_POST['sku_old']==$_POST['sku']){
	if($_POST['new_ebay_id']>0){
			$sql2 = 'UPDATE `oc_product`SET ebay_id="'.$_POST['new_ebay_id'].'",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			$_POST['new_ebay_listing']="yes";
	}
	if($_POST['new_ebay_id']=="null" || $_POST['new_ebay_id']=="NULL"){
		end_to_ebay($connectionapi,$_POST['marketplace_item_id']);
		$sql2 = 'UPDATE `oc_product`SET ebay_id="0",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
			//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
	}
	//echo $_POST['new'];
	if ((string)$_POST['sku'] =="")$_POST['new']=0;
	//echo $_POST['condition_id'];
	//echo substr($_POST['upcorigine'],0,12)."<br>";
	//echo substr((string)$_POST['upc'],0,12);
	if(substr($_POST['upcorigine'],0,12)<>substr((string)$_POST['upc'],0,12)){
		(string)$_POST['upc']=$_POST['upcorigine'];
		//echo "allo";
	}
	$Date = date('Y-m-d');
	//echo $_POST['new'].(string)$_POST['sku'] .$_POST['location_magasin'].$_POST['quantitymagasin_ajouter'];
	if (isset($_POST['product_id'] ) && $_POST['new']==1){
			$inventaire=1;
			$qtetot=0;
			$qtemag=0;
			//if($_POST['quantitymagasin_ajouter']==0)$inventaire=0;
			$quantity_total_ajouter=$_POST['quantitymagasin_ajouter']+$_POST['quantityentrepot_ajouter'];
			
			
		/*	if($_POST['quantitymagasin_ajouter']>0){
				$qtemag=$_POST['quantitymagasin_ajouter'];
				$qtetot=$_POST['quantitymagasin_ajouter'];
			}
			if($_POST['quantityentrepot_ajouter']>0){
				$qtetot=$qtetot+$_POST['quantityentrepot_ajouter'];
				
			}*/
			if($_POST['condition_id']!=9 || ($_POST['condition_id']==9 && strlen($_POST['sku'])>12)){
				if($qtetot>0 && !(strlen( $_POST['sku'])==12 && $_POST['quantitymagasin_ajouter']==0 && $_POST['quantityentrepot_ajouter']>0)){
					echo '<script>window.open("createbarcodetotal.php?all=no&type=both&qtemag=0&qtetot='.$qtetot.'&product_id='.$_POST['product_id'].'&sku='.(string)$_POST['sku'].'","etiquette")</script>';
				}
			}
			$sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity+'.$_POST['quantitymagasin_ajouter'].' ,quantity=quantity+'.$_POST['quantityentrepot_ajouter'].', location ="'.strtoupper($_POST['location_entrepot']).'" where product_id='.$_POST['product_id'];
		//	echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);

			$sql2 = 'UPDATE `oc_etsy_products_list` SET `update_flag` = "1" where id_product='.$_POST['product_id']; 
			$req2 = mysqli_query($db,$sql2); 
	/* 		if($_POST['quantity_actuel']<>($data2['quantity']+$_POST['quantitymagasin_ajouter']+$_POST['unallocated_quantity'])){
				$updquantity=$_POST['quantitymagasin_ajouter']+$_POST['unallocated_quantity'];
			}else{ */
				$updquantity=$_POST['quantityentrepot_ajouter']+$_POST['quantity']+$_POST['quantitymagasin_ajouter']+$_POST['unallocated_quantity'];
	/* 		} */
			$_POST['quantitymagasin_ajouter']=0;
			$_POST['quantityentrepot_ajouter']=0;
			$status=1;
			$pricetmp= explode ('+',$_POST['price_with_shipping']);
			//echo count($pricetmp);
			if(count($pricetmp)>1){
			//	//print("<pre>".print_r ($pricetmp,true )."</pre>");
				$_POST['price_with_shipping']=0;
				$_POST['price']=0;
				foreach ($pricetmp as $pricet){
					$_POST['price_with_shipping']+=$pricet;
				}
			}
			if($_POST['price_with_shipping']>5000 || $updquantity<=0){
				$status=0;
				$updquantity=0;
			}
			//echo $_POST['price_with_shipping'];
			//$_POST['price']=0+number_format($_POST['price']);
			if(isset($_POST['checkpricechange']) && $_POST['checkpricechange']<>$_POST['price_with_shipping'])	{
				$_POST['price']=$_POST['price_with_shipping']-$_POST['shipping'];
				$_POST['suggest']=$_POST['price_with_shipping']-$_POST['shipping'];
			}
			$sql2 = 'UPDATE `oc_product`SET `status`="'.$status.'",ebay_last_check="2020-09-01",remarque_interne="'.$_POST['remarque_interne'].'",weight_class_id=5,tax_class_id=9,date_price_upd="'.$Date.'", stock_status_id=7,status=1,`price` = "0'.number_format($_POST['price'], 2, '.', '').'", price_with_shipping="'.number_format($_POST['price_with_shipping'], 2, '.', '').'" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
		//	echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);	
			/*$sql2 = 'UPDATE `oc_product_special` SET date_price_upd="'.$Date.'",`price` = "0'.number_format($_POST['price'], 2, '.', '').'" WHERE `oc_product_special`.`product_id` ="'.$_POST['product_id'].'"';
			//	echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2); */
				//echo $_POST['price_with_shipping'].'<br>'.$updquantity.'<br>'.$_POST['marketplace_item_id'].'<br>'.(string)$_POST['upc'].'<br>';
		}
	}
//}
//print("<pre>".print_r ($_POST,true )."</pre>");
if ($_POST['product_id'] !=""){ 
	if($_POST['product_id'] !="" && $_POST['sku']==$_POST['sku_old']){
		$data=get_products_by_id($_POST['product_id']);	
	//	//print("<pre>".print_r ($_POST,true )."</pre>");
	}elseif($_POST['sku']!=""){
		$sku_tmp=$_POST['sku'];
		unset ($_POST);
		$_POST['sku']=$sku_tmp;
		$data=get_product($_POST['sku']);
		$data=$data[0];
		
		$_POST['product_id']=$data[0]['product_id'];

	}
//	//print("<pre>".print_r ($data,true )."</pre>");
//echo $sql.'<br><br>';
		// on envoie la requ�te
	//	$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
	//	$data = mysqli_fetch_assoc($req);
	//	
		if(count($data)>0){
			if($_POST['name']==""){
				$sql4 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description`,`oc_category`  where oc_category_description.language_id=1 and oc_product_to_category.category_id=oc_category_description.category_id AND oc_product_to_category.category_id=oc_category.category_id and product_id="'.$_POST['product_id'].'" and leaf=1';
		//	echo $sql4;
					$req4 = mysqli_query($db,$sql4);
					$data4 = mysqli_fetch_assoc($req4);
					$_POST['categoryname']=$data4['name'];
					$_POST['category_id']=$data4['category_id'];
					$_POST['category_id']=$data4['category_id'];
					if ($_POST['openpageprix']!=1){
						$info_shipping=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc'],12919,$_POST['category_id']);
						$_POST['shipping']=$info_shipping['shipping'];
						$_POST['carrier']=$info_shipping['carrier'];
						$_POST['other']=$info_shipping['other'];
					}
/* 					} */
					//$_POST['price']=$data2['price'];
					$_POST['price_with_shipping']=$data['price_with_shipping'];
					(string)$_POST['upc']=(string)$data['upc'];
					//get_from_upctemp($data['upc']);
					//echo file_get_contents("https://www.upcitemdb.com/norob/alink/?id=v2u2z2v2v253b464s2&tid=1&seq=1617680392&plt=c35880419e05f3f2fa8fc94c333c297b");
					$_POST['upcorigine']=(string)$data['upc'];

					$_POST['price']=$data['price'];
					$_POST['quantitymagasin_ajouter']=0;
					//$_POST['location_magasin']=strtoupper($data['location_magasin']);
					$_POST['quantityentrepot_ajouter']=0;
					$_POST['location_entrepot']=strtoupper($data['location_entrepot']);
					$_POST['condition_id']=$data['condition_id'];
					$_POST['product_id']=$data['product_id'];
					$_POST['ebay_id_old']=$data['ebay_id_old'];
					$_POST['sku']=$data['sku'];
					$_POST['upctemp']="";//get_from_upctemp($data['upc']);
					$sqlbrand = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];
					// on envoie la requ�te
					$reqbrand = mysqli_query($db,$sqlbrand);
					$databrand = mysqli_fetch_assoc($reqbrand);
					$_POST['brand']=$databrand['name'];
					$_POST['model']=$data['model'];
					$_POST['color']=$data['color_en'];
					$_POST['weight']=$data['weight'];
					$_POST['length']=$data['length'];
					$_POST['width']=$data['width'];
					$_POST['height']=$data['height'];
					$_POST['skuold']=$data['sku']; 
					$_POST['remarque_interne']=$data['remarque_interne'];
					$_POST['datapricesuggest'] = new DateTime($_POST['date_price_upd_ps']);
					$_POST['datapricesuggest'] = $_POST['datapricesuggest']->format('Y-m-d');
					$_POST['datapricesuggest']=date_parse ($_POST['datapricesuggest']);	
					//verification prix neuf
			}
			
					$sql3 = 'SELECT *,P.price AS price, P.product_id,P.ebay_id,P.sku,name,P.unallocated_quantity,P.image,P.upc,P.price_with_shipping,P.weight,P.length,P.width,P.height,P.date_price_upd,P.condition_id FROM `oc_product` AS P,`oc_product_description` where P.product_id=oc_product_description.product_id  and P.sku = "'.substr ((string)$_POST['upc'],0,12).'"';
//echo $sql3.'<br><br>';
		// on envoie la requ�te
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data3 = mysqli_fetch_assoc($req3);		
	
					$_POST['pricesuggest']=isset($data3['price_with_shipping'])?$data3['price_with_shipping']:0;
					$_POST['date_price_upd_ps']=isset($data3['date_price_upd'])?$data3['date_price_upd']:'';
					$_POST['ebay_id_refer']=isset($data3['marketplace_item_id'])?$data3['marketplace_item_id']:'';
				//	//print("<pre>".print_r ($data3,true )."</pre>");

			$_POST['dateverification'] = new DateTime('now');
			$_POST['dateverification']->modify('-3 month'); // or you can use '-90 day' for deduct
			$_POST['dateverification'] = $_POST['dateverification']->format('Y-m-d');
		//	$_POST['dateverification']=date_parse ($_POST['dateverification']);
			$_POST['datapricesuggest'] = new DateTime($_POST['date_price_upd_ps']);
			$_POST['datapricesuggest'] = $_POST['datapricesuggest']->format('Y-m-d');
		//	$_POST['datapricesuggest']=date_parse ($_POST['datapricesuggest']);	
				if ($_POST['condition_id']==9){
					$prixconvert=1; 
				//	$endprix=.95;
				}elseif($_POST['condition_id']==99 || $_POST['condition_id']==8){
					$prixconvert=.90;
				//	$endprix=.95;	
				}elseif($_POST['condition_id']==22 || $_POST['condition_id']==7){
					$prixconvert=.80;
				//	$endprix=.95;
				}elseif($_POST['condition_id']<7){
					$prixconvert=.75;
				//	$endprix=.95;
				}
/* 					if(($_POST['pricesuggest']>0&&($_POST['dateverification'] <= $_POST['datapricesuggest']))&&($_POST['price_with_shipping']==0 ||$_POST['price_with_shipping']=="")){//
						//echo $_POST['price_with_shipping'];
						//echo "<br>";
						//echo $_POST['pricesuggest'];
						$_POST['suggestebay']=$_POST['pricesuggest']*$prixconvert;
						$price_replace=explode('.',$_POST['suggestebay']);
						$_POST['suggestebay']=$price_replace[0]+$endprix;
						$_POST['price_with_shipping']=$price_replace[0]+$endprix;
						//echo $_POST['price_with_shipping'];
					//echo "allo";
					} */
			$_POST['new']=1;
			$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			$_POST['etsy_id']=$data['etsy_id'];
			$_POST['name']=$data['name_en'];
			$wordsearch=str_replace(" ", "+",$data['name_en']);
			$_POST['quantity']=$data['quantity'];
			$_POST['unallocated_quantity']=$data['unallocated_quantity'];
			$_POST['quantitytotal']=$data['quantity_total'];
			$_POST['image_product']=$data['image_product'];
			$_POST['product_id']=$data['product_id'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['date_price_upd_magasin']=$data['date_price_upd'];
			$_POST['date_price_upd']=$data['date_price_upd'];
			$_POST['ebay_date_relisted']=$data['ebay_date_relisted'];
			$_POST['datemagasintmp'] = new DateTime($data['date_price_upd']);
			$_POST['datemagasin'] = $_POST['datemagasintmp']->format('Y-m-d');
		//	$_POST['datemagasin']=date_parse ($_POST['datemagasintmp']);
			$_POST['datemagasin2tmp'] = new DateTime($data['date_price_upd']);
			$_POST['datemagasin2'] = $_POST['datemagasin2tmp']->format('Y-m-d');
			$_POST['dateretailtmp'] = new DateTime($data['date_price_upd']);
			$_POST['dateretail'] = $_POST['dateretailtmp']->format('Y-m-d');
		//	$_POST['dateretail']=date_parse ($_POST['dateretailtmp']);
			$_POST['dateretail2tmp'] = new DateTime($data['date_price_upd']);
			$_POST['dateretail2']= $_POST['dateretail2tmp']->format('Y-m-d');
/* 			if($_POST['dateverification'] > $_POST['dateretail'])  print_r($_POST['dateretail']);
			if($_POST['dateverification'] > $_POST['datemagasin']) print_r($_POST['datemagasin']); */
			//echo $_POST['shipping'].'<br><br>';
		}
			//print_r($_POST['datapricesuggest']);
			//echo "<br><br>";
			//print_r($_POST['dateverification']);
				$_POST['suggest']=(($_POST['price_with_shipping'])-$_POST['shipping']);
				$_POST['suggest']= number_format($_POST['suggest'], 2,'.', '');
			//	$price_replace=explode('.',$_POST['suggest']);
			//	$_POST['suggest']=$price_replace[0]+$endprix;
				//$_POST['price']= number_format($_POST['price'], 2, '.', '');
				if($_POST['price']<0.50){
					$_POST['price']=number_format(0.00, 2,'.', '');
				}else{
					//$price_replace=explode('.',$_POST['price']);
					//$_POST['price']=$price_replace[0]+$endprix;
				}
				if(($_POST['pricesuggest']>0 && ($_POST['dateverification'] <= $_POST['datapricesuggest']))){//
					$_POST['suggestebay']=$_POST['pricesuggest']*$prixconvert;
					//$price_replace=explode('.',$_POST['suggestebay']);
				//	$_POST['suggestebay']=$price_replace[0]+$endprix;
					//echo "allo";
				}elseif(($_POST['findprice']+$_POST['findshipping'])>0){
				//	$_POST['suggestebay']=($_POST['findprice']+$_POST['findshipping']-.95)*$prixconvert;
					$_POST['suggestebay']=($_POST['findprice']+$_POST['findshipping'])*$prixconvert;

				//	$price_replace=explode('.',$_POST['suggestebay']);
				//	$_POST['suggestebay']=$price_replace[0]+$endprix;
				}
				if($_POST['remarque_interne']!="" || $_POST['pourverification']!=""){
				    if($_POST['remarque_interne']=="")$_POST['remarque_interne']="*** Ne pas afficher sur site ***";
				//	$_POST['price_with_shipping']=0;
					$sql2 = 'UPDATE `oc_product` SET `status`=0,ebay_last_check="2020-09-01",remarque_interne="'.$_POST['remarque_interne'].'" where product_id='.$_POST['product_id'];
					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
						$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],$updquantity,$db,"oui","oui");
					/*	$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],1,$db,"oui");*/	
				}else{
					//echo $updquantity;
					if($_POST['marketplace_item_id']>1){
						$resultebay="";
						$result = json_encode($new); 
						mise_en_page_description($connectionapi,$_POST['product_id'],$db);
						$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],$updquantity,$db,"oui","oui");
						$json = json_decode($result, true); 
							if($_POST['showerror']=="oui")//print("<pre>".print_r ($json,true )."</pre>");
								if($json["Ack"]=="Failure"){
									$resultebay.="ERREUR: ".(isset($json["Errors"]["ShortMessage"])?$json["Errors"]["ShortMessage"]:$json["Errors"][1]["ShortMessage"]);
									//print("<pre>".print_r ($json,true )."</pre>");
								}elseif($json["Ack"]=="Warning"){
									//$resultebay.="WARNING: ".$json["Errors"]["ShortMessage"];
								}
					}
				}
			if($_POST['processing']=="oui"){
					header("location: listing.php"/* ?insert=oui&sku=".(string)$_POST['upc'] */); 
					exit();
			}
			if(isset($_POST['marketplace_item_id']) && $_POST['marketplace_item_id']!=""){
				$_POST['ebay_id_a_cloner']="";
			}
			
}
//print("<pre>".print_r ($_POST,true )."</pre>");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <title></title>
	<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
	<script type="text/javascript">
	$(function() {
		  $(document).ready(function () {
		   var todaysDate = new Date(); // Gets today's date
			// Max date attribute is in "YYYY-MM-DD".  Need to format today's date accordingly
			var year = todaysDate.getFullYear(); 						// YYYY
			var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2);	// MM
			var day = ("0" + todaysDate.getDate()).slice(-2);			// DD
			var minDate = (year +"-"+ month +"-"+ day); // Results in "YYYY-MM-DD" for today's date 
			// Now to set the max date value for the calendar to be today's date
			$('.departDate input').attr('min',minDate);
			  });
	});
    function selectAll() {
        var items = document.getElementsByName('maj[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }
    function UnSelectAll() {
        var items = document.getElementsByName('maj[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }	
	function change_raison(){
	if (document.getElementById('pourverification').value == 'oui'){
		document.getElementById('raison').enabled ;
	}else{
		document.getElementById('raison').disabled ;
	}
};	
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="pretlister.php?product_id=<?echo $_POST['product_id'];?>" method="post">
<?/*<h3>
		<h3><a href="inventairemagasin.php?sku=<?echo substr((string)$_POST['sku'] ,0,12)."NO";?>" target="_self" style="color:#ff0000"><strong>Changer New Other</strong></a> 
	<a href="inventairemagasin.php?sku=<?echo substr((string)$_POST['sku'] ,0,12);?>" target="_self" style="color:#ff0000"><strong>Changer New</strong></a></h3>
	<a href="inventairemagasin.php?sku=<?echo (string)$_POST['sku'] ;?>&action=ajouter" target="_self" style="color:#ff0000"><strong>Ajouter item pour inventaire</strong></a></h3>*/?>
 <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="3" rowspan="1" style="vertical-align:  middle; width: 200px;">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><a href="listing.php?sku=<?echo (string)$_POST['sku'];?>" >Retour au MENU</a><br> 
        </td>
        <td colspan="3" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Pr&ecirc;t &agrave; lister</h1><?echo '<h3><font color="red">'.$erreurvide.'</font></h3>';?>
        </td>
      </tr>
      <tr>
		<?if(isset($resultebay) && $resultebay!=""){?>
			   <tr>
        <td colspan="3" rowspan="1" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;background-color: red;color:white">
			<?echo $resultebay;?>
        </td>
	  </tr>
		<?}?>
		 <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Sku
		<a href="createbarcodetotal.php?type=both&qtemag=0&qtetot=1&product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
	   </td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="sku"  type="text" name="sku"  value="<?echo (string)$_POST['sku'] ;?>" maxlength="255"  > 
		</td>
	 </tr>
	

	  
	        <tr>
					  <td style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		  </td>
         <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		 Qt&eacute; &agrave; ajout&eacute;e: 
		 </td>
        <td style="vertical-align:  middle; text-align:center;height: 25px; background-color: #030282; color: white; width: 200px;">
		Location:
		</td>
      </tr>
      <tr>
         <td colspan="1" style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #e4bc03; color: black; width: 200px;">
		 <strong>A PLACER: (<?echo $_POST['unallocated_quantity'];?>) </strong>
		 </td>
		         <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="quantitymagasin_ajouter"  class="element text currency" type="text" name="quantitymagasin_ajouter" value="0" size="10" />       
		</td>

      </tr>
	
	  	  <tr>
		  <td style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		  </td>
         <td style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		 Qt&eacute; &agrave; ajout&eacute;e:
		 </td>
         <td style="vertical-align:  middle; text-align:center;height: 25px; background-color: #030282; color: white; width: 200px;">
		Location:
		</td>
      </tr>

	        <tr>
         <td colspan="1" style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #e4bc03; color: black; width: 200px;">
		 <strong>En entrepot: (<?echo $_POST['quantity'];?>)  </strong>  
		 </td>
		 <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<input id="quantityentrepot_ajouter"  class="" type="text" name="quantityentrepot_ajouter" value="0" size="10"  autofocus />		
		</td>
        <td style="vertical-align:  middle; text-align:center;height: 25px; ">
		<input id="location_entrepot" class="" type="text" name="location_entrepot" value="<?echo $_POST['location_entrepot'];?>" maxlength="120" />
		</td>
      </tr>
	  <tr>
	     <td colspan="3" style="vertical-align:  middle; text-align:center;height: 16px; background-color: #e4bc03; width: 200px;"> 
		 <input type="checkbox" name="processing" value="oui" />	Proc&eacute;der
		 <input type="checkbox" name="showerror" value="oui" />	Afficher Erreur
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>
	 </tr>
      <tr>	
		<td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">	  
		Suggestion prix EBAY:
		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['suggestebay'];?> usd
		</td>
</tr>
<tr>
		<td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		NOTRE Prix EBAY
		<td colspan="2" rowspan="1" style="vertical-align: middle; text-align:center;
		<?if ($_POST['price_with_shipping']==0)
			$_POST['price_with_shipping']=$_POST['suggestebay'];
		if($_POST['price']==$_POST['price_with_shipping'])
			$_POST['price']=0;
			?>
		<?if(($_POST['dateverification'] > $_POST['dateretail'])){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>
		height: 0px; ">
		(en dollar am&eacute;ricain)<br><input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping'], 2,'.', '');?>" size="10" />
	
		</td>
</tr>
     <tr>
	  <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix v&eacute;rifi&eacute; le: 
		</td>
		<td colspan="2" rowspan="1" style="vertical-align: middle; text-align: center;<?if($_POST['dateverification'] > $_POST['dateretail']){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">
		<?echo $_POST['dateretail2'];?>
		</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Livraison: 
		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['carrier']."->".$_POST['shipping'];?> usd (plus haut: <?echo $_POST['other']?>)
		</td>
</tr>
      <tr>	
		<td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">	  
		Suggestion prix:
		<input type="hidden" name="suggest" value="<?echo $_POST['suggest']	;?>"/>	

		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;<?if($_POST['suggest']<$_POST['price']){?>background-color: red; color: white;
		<?}else{?>background-color: green; color: white;<?}?>height: 0px; ">
		<?echo $_POST['suggest'];?> 
		</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix MAGASIN:<br>
	</td>
		<td colspan="2" rowspan="1" style="vertical-align: middle; text-align:center;<?if(($_POST['suggest']<$_POST['price'])||($_POST['dateverification'] > $_POST['datemagasin'])){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">
		<?if($_POST['price']==0)$_POST['price']=$_POST['suggest'];?>
		(finir par <?echo $endprix;?> en dollar USD)<br><input id="price"  type="text" name="price" value="<?echo number_format($_POST['price'], 2, '.', '');?>" size="10" />
</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix v&eacute;rifi&eacute; le:
	</td>
		<td colspan="2" rowspan="1" style="vertical-align: middle; text-align: center;<?if(($_POST['dateverification'] > $_POST['datemagasin'])){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">
		<?echo $_POST['datemagasin2'];?> 
		</td>
</tr>
      <tr>
		<td colspan="3" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
			Demande de validation <input type="checkbox" id="pourverification" name="pourverification" onclick='change_raison()' value="oui"/> 
			Raison: <input type="text" id="remarque_interne" name="remarque_interne" value="<?echo $_POST['remarque_interne'];?>" size="80" />
		</td>
</tr>
<tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Ebay ID:
		</td>
        <?if($_POST['marketplace_item_id']==0||$_POST['marketplace_item_id']==""){?>
		<td colspan="2" rowspan="1" style="background-color:red; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
		<?}else{?><td colspan="2" rowspan="1" style="background-color:green; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
			<a href="https://www.ebay.com//lstng?mode=ReviseItem&itemId=<?echo $_POST['marketplace_item_id'];?>&sr=wn&ReturnURL=https://www.ebay.com/sh/lst/active?offset=0" target='ebay' ><?echo $_POST['marketplace_item_id'];?></a>
<br>
		 <input id="new_ebay_id"  type="text" name="new_ebay_id"  value="<?echo $_POST['new_ebay_id'];?>" maxlength="255" ><?}?>  
		</td>
</tr>
 <?if($_POST['marketplace_item_id']==0||$_POST['marketplace_item_id']==""){?>
			<tr>
		 <td style="vertical-align:  middle; text-align:center; height: 0px; background-color: #030282; color: white; width: 200px;">
			Ebay ID &agrave; cloner: 
        </td>
        <td colspan="2" rowspan="1" style="background-color:red; color: white;vertical-align:  middle; text-align:center; height: 0px; ">
	<?if(($_POST['ebay_id_old']!="" || $_POST['ebay_id_old']!=0) && $_POST['ebay_id_a_cloner']=="")
			$_POST['ebay_id_a_cloner']=$_POST['ebay_id_old'];?>
			<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" >
			<br><input type="checkbox" name="pas_upc" value="does not apply" <?if($_POST['pas_upc']=="does not apply"){?>checked<?}?> />	UPC does not apply	
		</td>
	</tr>
 <?}?>


	       <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		UPC:
		</td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?echo $_POST['upc']	;?>
		<?if($_POST['changeupc']=="changer"){?><input id="price"  type="text" name="newupc" value="<?echo $_POST['newupc'];?>" size="30" />
		<input type="hidden" name="upc" value="<?echo $_POST['upc']	;?>"/>	<?}else{?>
		<br><a href="modifierprix.php?changeupc=oui&product_id_r=<? echo $_POST['product_id_r'];?>&product_id_no=<? echo $_POST['product_id_no'];?>&product_id=<? echo $_POST['product_id'];?>">Corriger UPC de l'item</a>
		<?}?>
		</td>
		</tr>
      <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Titre:
		</td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<input type="hidden" name="name" value="<?echo $_POST['name'];?>"/>	

		<a href="https://www.ebay.com/sch/i.html?_nkw=<?echo $_POST['name']?>&LH_PrefLoc=1&LH_ItemCondition=3&LH_BIN=1&rt=nc" target='ebay2' ><?echo strtoupper ($_POST['name']);?>	</a>	
		</td>
		</tr>
			  <tr>
         <td style="vertical-align:  middle;  text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		 Inventaire Total: 
		 </td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['quantitytotal'];?>     
		</td>
      </tr>
 <tr>
    <td colspan="3" style="vertical-align:  middle; text-align:right;height: 16px; background-color: #e4bc03; width: 200px;"> 
		 <input type="checkbox" name="processing" value="oui" />	Proc&eacute;der
		 <input type="checkbox" name="showerror" value="oui" />	Afficher Erreur
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>	 
	  </tr>
    </tbody>
  </table>
<?if ($_POST['openpageprix']!=1){
	?>
  <script>
  <?/*
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&LH_Sold=1&LH_Complete=1&LH_BIN=1&rt=nc&LH_ItemCondition=3","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&_sop=15&LH_BIN=1&rt=nc&LH_ItemCondition=3","ebaynew");
window.open("https://www.upcitemdb.com/upc/<?echo (string)$_POST['upc'];?>","google");
window.open("https://www.ebay.com/sh/research?condition=NEW&condition=USED&condition=REFURBISHED&dayRange=365&endDate=1617592239873&format=FIXED_PRICE&format=BEST_OFFER&format=AUCTION&keywords=&marketplace=EBAY-US&offset=0&productId=<?echo (string)$_POST['upc'];?>&queryCondition=AND&startDate=1586142639873&tabName=SOLD","terapeak");
window.open("https://www.ebay.com/sh/research?condition=NEW&condition=USED&condition=REFURBISHED&dayRange=365&endDate=1617592564917&format=FIXED_PRICE&format=BEST_OFFER&format=AUCTION&keywords=<?echo $wordsearch;?>&marketplace=EBAY-US&offset=0&queryCondition=AND&startDate=1586142964917&tabName=SOLD","terapeak2");
*/?>
</script>
<?
$_POST['openpageprix']=1;
}?>
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="new" value="<?echo $_POST['new'];?>" />
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="sku_old" value="<?echo $_POST['sku'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="etat" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="title" value="<?echo $_POST['name'];?>" />
		<input type="hidden" name="upc" value="<?echo $_POST['upc'];?>" /> 
		<input type="hidden" name="shipping" value="<?echo $_POST['shipping'];?>" />	
		<input type="hidden" name="carrier" value="<?echo $_POST['carrier'];?>" />
		<input type="hidden" name="other" value="<?echo $_POST['other'];?>" />
		<input type="hidden" name="upcorigine" value="<?echo $_POST['upcorigine'];?>" />
		<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>" />
		<input type="hidden" name="skuold" value="<?echo $_POST['skuold'];?>" />
		<input type="hidden" name="unallocated_quantity" value="<?echo $_POST['unallocated_quantity'];?>" />
		<input type="hidden" name="quantity" value="<?echo $_POST['quantity'];?>" />
		<input type="hidden" name="ebay_date_relisted" value="<?echo $_POST['ebay_date_relisted'];?>"/> 
		<input type="hidden" name="pricesuggest" value="<?echo $_POST['pricesuggest'];?>"/> 
		<input type="hidden" name="date_price_upd_ps" value="<?echo $_POST['date_price_upd_ps'];?>"/> 
		<input type="hidden" name="quantitytotal" value="<?echo $_POST['quantitytotal'];?>"/> 
		<input type="hidden" name="ebay_id_refer" value="<?echo $_POST['ebay_id_refer'];?>"/> 
		<input type="hidden" name="new_ebay_listing" value="<?echo $_POST['new_ebay_listing'];?>"/> 
		<input type="hidden" name="changeupc" value="<?echo $_POST['changeupc'];?>"/> 
		<input type="hidden" name="openpageprix" value="<?echo $_POST['openpageprix'];?>"/> 
		<input type="hidden" name="brand" value="<?echo $_POST['brand'];?>"/> 
		<input type="hidden" name="model" value="<?echo $_POST['model'];?>"/> 
		<input type="hidden" name="weight" value="<?echo $_POST['weight'];?>"/> 
		<input type="hidden" name="length" value="<?echo $_POST['length'];?>"/> 
		<input type="hidden" name="width" value="<?echo $_POST['width'];?>"/> 
		<input type="hidden" name="height" value="<?echo $_POST['height'];?>"/> 
		<input type="hidden" name="color" value="<?echo $_POST['color'];?>"/> 
		<input type="hidden" name="dateretail" value="<?echo $_POST['dateretail'];?>"/> 
		<input type="hidden" name="dateretail2" value="<?echo $_POST['dateretail2'];?>"/> 
		<input type="hidden" name="datemagasin" value="<?echo $_POST['datemagasin'];?>"/> 
		<input type="hidden" name="datemagasin2" value="<?echo $_POST['datemagasin2'];?>"/> 
		<input type="hidden" name="dateverification" value="<?echo $_POST['dateverification'];?>"/> 
		<input type="hidden" name="checkpricechange" value="<?echo $_POST['price_with_shipping'];?>"/> 
</form>
	<script>
	$("#btn1").click(function(){
		$("#printabel").remove();
		loadOtherPage1();sku
	});
	$("#btn2").click(function(){
		$("#printabel").remove();
		loadOtherPage2();
	});
	function loadOtherPage1() {
		$("<iframe id='printabel'>")    
			.hide()                     
			.attr("src", "createsmallbarcode.php?qte=<?echo $_POST['quantitymagasin_ajouter']+$_POST['quantityentrepot_ajouter'];?>&product_id=<?echo $_POST['product_id'];?>") 
			.appendTo("body");           
		}
	function loadOtherPage2() {
		$("<iframe id='printabel'>")     
			.hide()                      
			.attr("src", "createbarcodemagasinun.php?qte=<?echo $_POST['quantitymagasin_ajouter'];?>&product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'] ?>") 
			.appendTo("body");           
		}
    </script>
</body>
</html>
<? // on ferme la connexion &agrave; 
if($_POST['price']>0){
	/*$sql2 = 'UPDATE `oc_product_special` SET date_price_upd="'.$Date.'",`price` = '.number_format($_POST['price'], 2, '.', '').' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];
				//echo $sql2.'<br><br>';
	$req2 = mysqli_query($db,$sql2); */
	$sql2 = 'UPDATE `oc_product` SET `price` = '.number_format($_POST['price'], 2, '.', '').',`price_with_shipping` = '.number_format($_POST['price_with_shipping'], 2, '.', '').',ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
			//	echo $sql2.'<br><br>';
	$req2 = mysqli_query($db,$sql2); 
		}
mysqli_close($db);
ob_end_flush(); 
?>