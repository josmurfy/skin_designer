<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte &agrave; MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['action']=="ajouter"){
	(string)$_POST['sku'] =$_GET['sku'];	
	 $test= ajouter_item ($connectionapi,$_GET['sku'],$db); 
	//echo 'allo'; 
}
if($_POST['changeupc']=="changer"){
		$sql = 'SELECT * FROM oc_product where upc = "'.(string)$_POST['upc'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		while($data = mysqli_fetch_assoc($req)){
			$finsku=substr((string)$data['sku'] ,12,10);
			$sql2 = 'UPDATE `oc_product`SET upc="'.(string)$_POST['newupc'].'",ebay_last_check="2020-09-01",sku="'.substr((string)$_POST['newupc'] ,0,12).$finsku.'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
			echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
		}	
		$_POST['upc']=(string)$_POST['newupc'];
	$_POST['changeupc']="";
}
if($_GET['changeupc']=="oui"){
	$_POST['changeupc']="changer";
}
if($_GET['product_id']>0){
	//echo "allo";
	$_POST['product_id']=$_GET['product_id'];
	$_POST['product_id_no']=$_GET['product_id_no'];
	$_POST['product_id_r']=$_GET['product_id_r'];
}
if (isset ($_GET['sku'])){
	(string)$_POST['sku']=$_GET['sku'];	
//echo 'allo'; 
}
//echo $_POST['new'];
if ((string)$_POST['sku'] !=$_POST['skuold'] && $_POST['skuold']!=""){
	(string)$_POST['sku'] =$_POST['skuold'];
	$_POST['quantity']="";
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
//echo $_POST['new'].(string)$_POST['sku'] .$_POST['location'].$_POST['quantity'];
if (isset($_POST['product_id'] )){
		if($_POST['price']>0){
			$sql3 = 'UPDATE `oc_product` SET `price` = "'.number_format($_POST['price']/1.34, 2, '.', '').'",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ="'.$_POST['product_id'].'"';
			$req3 = mysqli_query($db,$sql3); 	
		}
		if( $_POST['new']==1 && $_POST['price']>0 && $_POST['price_with_shipping']>0 && $_POST['pricemagasin']>0){
			$sql2 = 'UPDATE `oc_product`SET remarque_interne="'.$_POST['remarque_interne'].'",ebay_last_check="2020-09-01",weight_class_id=5,tax_class_id=9,date_price_upd="'.$Date.'", stock_status_id=7,status=1,`price` = "'.number_format($_POST['price']/1.34, 2, '.', '').'", price_with_shipping='.number_format($_POST['price_with_shipping'], 2, '.', '').' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
			$req2 = mysqli_query($db,$sql2);	

			$sql2 = 'select * from oc_product_special WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];
			$req2 = mysqli_query($db,$sql2);
			//echo $sql2;
					if(mysqli_num_rows($req2)==0){
						$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$_POST['product_id']."', '1', '1','0', '2018-09-01','2218-09-01')";
						$req7 = mysqli_query($db,$sql7);
						
					echo $sql7.'<br><br>';
						} 
			$sql3 = 'UPDATE `oc_product_special` SET date_price_upd="'.$Date.'",`price` = "'.number_format($_POST['pricemagasin']/1.34, 2, '.', '').'" WHERE `oc_product_special`.`product_id` ="'.$_POST['product_id'].'"';
			//$sql3 = 'UPDATE `oc_product_special` SET `price` = "'.number_format($_POST['pricemagasin']/1.34, 2, '.', '').'", `date_price_upd` = "'.$Date.'" WHERE `product_id` = 18738';
			//echo $sql3;
			$req3 = mysqli_query($db,$sql3); 

			if($_POST['clone']==""){
				$pricemagasin_no=($_POST['pricemagasin']*.90);
				$price_replace=explode('.',$pricemagasin_no);
				$pricemagasin_no=$price_replace[0]+.85;
				
				$pricemagasin_r=($_POST['pricemagasin']*.80);
				$price_replace=explode('.',$pricemagasin_r);
				$pricemagasin_r=$price_replace[0]+.75;
				
				$price_with_shipping_no=($_POST['price_with_shipping']*.90);
				$price_replace=explode('.',$price_with_shipping_no);
				$price_with_shipping_no=$price_replace[0]+.85;
				
				$price_with_shipping_r=($_POST['price_with_shipping']*.80);
				$price_replace=explode('.',$price_with_shipping_r);
				$price_with_shipping_r=$price_replace[0]+.75;
				
				$sql2 = 'UPDATE `oc_product`SET `status`=0,ebay_last_check="2020-09-01",remarque_interne="'.$_POST['remarque_interne'].'",weight_class_id=5,tax_class_id=9,date_price_upd="'.$Date.'", stock_status_id=7,`price` = '.number_format($_POST['price']/1.34, 2, '.', '').', price_with_shipping='.number_format($price_with_shipping_no, 2, '.', '').' WHERE price_with_shipping=0 and `oc_product`.`product_id` ="'.$_POST['product_id_no'].'"';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = 'UPDATE `oc_product`SET `status`=0,ebay_last_check="2020-09-01",remarque_interne="'.$_POST['remarque_interne'].'",weight_class_id=5,tax_class_id=9,date_price_upd="'.$Date.'", stock_status_id=7,`price` = '.number_format($_POST['price']/1.34, 2, '.', '').', price_with_shipping='.number_format($price_with_shipping_r, 2, '.', '').' WHERE price_with_shipping=0 and `oc_product`.`product_id` ="'.$_POST['product_id_r'].'"';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = 'UPDATE `oc_product_special` SET `date_price_upd`="'.$Date.'",`price` = '.number_format($pricemagasin_no/1.34, 2, '.', '').' WHERE `product_id` ="'.$_POST['product_id_no'].'"';
				$req2 = mysqli_query($db,$sql2); 
					if(mysqli_num_rows($req2)==0){
						$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$_POST['product_id_no']."', '1', '1','0', '2018-09-01','2218-09-01')";
						$req7 = mysqli_query($db,$sql7);
						$req2 = mysqli_query($db,$sql2); 
					}
//echo $sql2;				
				$sql2 = 'UPDATE `oc_product_special` SET `date_price_upd`="'.$Date.'",`price` = '.number_format($pricemagasin_r/1.34, 2, '.', '').' WHERE `product_id` ="'.$_POST['product_id_r'].'"';
				$req2 = mysqli_query($db,$sql2); 
					if(mysqli_num_rows($req2)==0){
						$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$_POST['product_id_r']."', '1', '1','0', '2018-09-01','2218-09-01')";
						$req7 = mysqli_query($db,$sql7);
						$req2 = mysqli_query($db,$sql2); 
					}	
					$updquantity=$_POST['quantity']+$_POST['unallocated_quantity'];
					//echo $updquantity;
				if($_POST['marketplace_item_id']>0)update_to_ebay($connectionapi,$_POST['price_with_shipping'],$updquantity,$_POST['marketplace_item_id'],$_POST['product_id']); 
//echo $sql2;				
			}
			
			if($_POST['pourverification']=="oui"){
				$_POST['price_with_shipping']=0;
				$sql2 = 'UPDATE `oc_product` SET `status`=0,ebay_last_check="2020-09-01",remarque_interne="Prix &agrave; v&eacute;rifier ('.$_POST['remarque_interne'].')" where product_id='.$_POST['product_id'];
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
						 	unset ($_POST['product_id']);
							unset ($_POST['pricemagasin']);
							unset ($_POST['price_with_shipping']);
							unset ($_POST['upc']);
							unset ($_POST['upcorigine']);
							unset ($_POST['price']);
							unset ($_POST['location']);
							unset ($_POST['condition_id']);
							unset ($_POST['marketplace_item_id']);
							unset ($_POST['skuold']);
							unset ($_POST['new']);
							unset ($_POST['sku'] );
							unset ($_POST['unallocated_quantity']); 
							unset ($_POST['quantity']);
							unset ($_POST['shipping']);
							unset ($_POST['remarque_interne']);
				header("location: listing.php?insert=oui&sku=".substr ((string)$_POST['upc'],0,12)); 
				exit(); 

				
			}

		}else{
			$erreurvide="Vous ne pouvez laisser aucun champs &agrave; ZERO!";
		}
		if($_POST['processing']=="oui")header("location: listing.php?insert=oui&sku=".substr ((string)$_POST['upc'],0,12));

}

if ($_POST['product_id'] !=""){ 
		$sql = 'SELECT *,P.price AS price_retail, P.price_with_shipping,PS.price
		 AS price_magasin, P.product_id,P.sku,PD.name,P.image,P.upc,P.unallocated_quantity 
		 AS unallocated_quantity,P.quantity 
		 FROM `oc_product` AS P LEFT JOIN `oc_product_description` AS PD ON P.product_id=PD.product_id 
		 LEFT JOIN oc_product_special AS PS ON  P.product_id=PS.product_id 
		where PD.language_id=1 and P.product_id = "'.$_POST['product_id'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data = mysqli_fetch_assoc($req);
					if(mysqli_num_rows($req)==0){
						$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$data['product_id']."', '1', '1','0', '2018-09-01','2218-09-01')";
						$req7 = mysqli_query($db,$sql7);
						$req = mysqli_query($db,$sql);
						$data = mysqli_fetch_assoc($req);
					//echo $sql7.'<br><br>';
					}
		if(mysqli_num_rows($req)>0){
			if($_POST['sku']==""){
					

/* 					if($data['USPS_com']!=""){
					//
						if($data['USPS']>0 && $data['USPS']< $data['USPS_com'] && ($data['USPS']< $data['UPS'] || $data['USPS']< $data['UPS_com'])){
							$_POST['shipping']=$data['USPS'];
						}elseif($data['USPS_com']>0 && ($data['USPS_com']< $data['UPS_com'] && $data['UPS_com']>0)){
							$_POST['shipping']=$data['USPS_com'];
						}elseif($data['UPS_com']>0 && $data['UPS_com']< $data['USPS_com']){
							$_POST['shipping']=$data['UPS_com'];
						}elseif($data['USPS_com']>0 && $data['USPS_com']< $data['USPS']){
							$_POST['shipping']=$data['USPS_com'];
						}elseif($data['UPS']>0 && $data['UPS']< $data['UPS_com']){
							$_POST['shipping']=$data['UPS'];
						}elseif($data['UPS_com']>0 && $data['UPS_com']< $data['UPS']){
							$_POST['shipping']=$data['UPS_com']; 
						}else{
							$_POST['shipping']=9999;
						}
					}else{ */
						$_POST['shipping']=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc']);
/* 					} */
					$_POST['pricemagasin']=$data['price']*1.34;
					$_POST['price_with_shipping']=$data['price_with_shipping'];
					(string)$_POST['upc']=(string)$data['upc'];
					$_POST['upcorigine']=(string)$data['upc'];
					$_POST['price']=$data['price_retail']*1.34;
					$_POST['quantity']=0;
					$_POST['location']=$data['location'];
					$_POST['condition_id']=$data['condition_id'];
					
					$_POST['skuold']=$data['sku']; 
					$_POST['remarque_interne']=$data['remarque_interne'];
					$datapricesuggest = new DateTime($_POST['date_price_upd_ps']);
					$datapricesuggest = $datapricesuggest->format('Y-m-d');
					$datapricesuggest=date_parse ($datapricesuggest);	
					

					//verification prix neuf

			}
					$sql3 = 'SELECT *,P.price AS price_retail, P.product_id,P.ebay_id,P.sku,name,p.unallocated_quantity,P.quantity,P.image,P.upc,P.price_with_shipping,P.weight,P.length,P.width,P.height,P.date_price_upd,P.condition_id FROM `oc_product` AS P,`oc_product_description` where P.product_id=oc_product_description.product_id   and P.sku like "'.$_POST['upc'].'"';
//echo $sql3.'<br><br>';
		// on envoie la requête
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data3 = mysqli_fetch_assoc($req3);					
					$_POST['pricesuggest']=$data3['price_with_shipping'];
					$_POST['date_price_upd_ps']=$data3['date_price_upd'];
					$_POST['ebay_id_refer']=$data3['marketplace_item_id'];
					
			$dateverification = new DateTime('now');
			$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
			$dateverification = $dateverification->format('Y-m-d');
			$dateverification=date_parse ($dateverification);
			$datapricesuggest = new DateTime($_POST['date_price_upd_ps']);
			$datapricesuggest = $datapricesuggest->format('Y-m-d');
			$datapricesuggest=date_parse ($datapricesuggest);	
				if ($_POST['condition_id']==9){
					$prixconvert=1; 
					$endprix=.95;
				}elseif($_POST['condition_id']==99 || $_POST['condition_id']==8){
					$prixconvert=.90;
					$endprix=.85;	
				}elseif($_POST['condition_id']==22 || $_POST['condition_id']==7){
					$prixconvert=.80;
					$endprix=.75;
				}
					
/* 					if(($_POST['pricesuggest']>0&&($dateverification <= $datapricesuggest))&&($_POST['price_with_shipping']==0 ||$_POST['price_with_shipping']=="")){//
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
			$_POST['name']=$data['name'];
			$_POST['quantity']=$data['quantity'];
			$_POST['unallocated_quantity']=$data['unallocated_quantity'];
			$_POST['quantitytotal']=$data['quantity_actuel'];
			$_POST['image']=$data['image'];
			$_POST['product_id']=$data['product_id'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['date_price_upd_magasin']=$data2['date_price_upd'];
			$_POST['date_price_upd']=$data['date_price_upd'];
			$_POST['ebay_date_relisted']=$data['ebay_date_relisted'];

			$datemagasin = new DateTime($data2['date_price_upd']);
			$datemagasin = $datemagasin->format('Y-m-d');
			$datemagasin=date_parse ($datemagasin);
			
			$datemagasin2 = new DateTime($data2['date_price_upd']);
			$datemagasin2 = $datemagasin2->format('Y-m-d');


			
			$dateretail = new DateTime($data['date_price_upd']);
			$dateretail = $dateretail->format('Y-m-d');
			$dateretail=date_parse ($dateretail);
			$dateretail2 = new DateTime($data['date_price_upd']);
			$dateretail2= $dateretail2->format('Y-m-d');


			
			
/* 			if($dateverification > $dateretail)  print_r($dateretail);
			if($dateverification > $datemagasin) print_r($datemagasin); */
			
			//echo $_POST['shipping'].'<br><br>';
		}

			//print_r($datapricesuggest);
			//echo "<br><br>";
			//print_r($dateverification);
			
				$_POST['suggest']=(($_POST['price_with_shipping']*.95)-$_POST['shipping']);
				$_POST['suggest']= number_format($_POST['suggest']*1.34, 2,'.', '');
				$price_replace=explode('.',$_POST['suggest']);
				$_POST['suggest']=$price_replace[0]+$endprix;
				if($_POST['pricemagasin']<1){
					$_POST['pricemagasin']=number_format(0, 2,'.', '');
				}/* else{
					$price_replace=explode('.',$_POST['pricemagasin']);
					$_POST['pricemagasin']=$price_replace[0]+$endprix;
				} */
				
/* 				if(($_POST['pricesuggest']>0&&($dateverification <= $datapricesuggest))){//
					$_POST['suggestebay']=$_POST['pricesuggest']*$prixconvert;
					$price_replace=explode('.',$_POST['suggestebay']);
					$_POST['suggestebay']=$price_replace[0]+$endprix;
					//echo "allo";
				}elseif(($_POST['findprice']+$_POST['findshipping'])>0){ */
					$_POST['suggestebay']=($_POST['findprice']+$_POST['findshipping']-.95)*$prixconvert;
/* 					$price_replace=explode('.',$_POST['suggestebay']);
					$_POST['suggestebay']=$price_replace[0]+$endprix; */
/* 				} */
				
				
				
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>

		<script>


</script>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
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

<form id="form_67341" class="appnitro" action="modifierprix.php?product_id_r=<? echo $_POST['product_id_r'];?>&product_id_no=<? echo $_POST['product_id_no'];?>&product_id=<? echo $_POST['product_id'];?>" method="post">

 <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 200px;">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><a href="listing.php?sku=<?echo (string)$_POST['upc'];?>" >Retour au MENU</a><br> 
        </td>
        <td colspan="4" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Modification de prix</h1><?echo '<h3><font color="red">'.$erreurvide.'</font></h3>';?>
        </td>
      </tr>



      </tr>
      <tr>
        <td colspan="1" rowspan="17" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;">
				<?
			if($data['image']!="")echo '<img src="https://www.phoenixliquidation.ca/image/'.$data['image'].'" width="200">';
			?>
<br><a href="multiupload.php?sku=<?echo (string)$_POST['sku'] ?>" >Modifier Photos</a>
        </td>
		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">Sku
       <?if ($data['name']==""&&$sku!=""){?>
		<a href="insertionitemusa.php?upc=<? echo $sku;?>&action=listingusa"><strong>Ajouter</strong></a> 
		<?}?>
	   </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<?echo (string)$_POST['sku'] ;?>
		
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
		Titre:
		</td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
		<?echo strtoupper ($_POST['name']);?>
		</td>

		</tr>
	<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix de d&eacute;tail: 
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;
		<?if($_POST['pricemagasin'] > $_POST['price']*.90){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>
		height: 0px; ">
		(Prix en dollar canadien)<br>
		<input id="price"  type="text" name="price" value="<?echo number_format($_POST['price'], 2,'.', '');?>" size="10" />
		</td>
      </tr>
<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix EBAY trouv&eacute;
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align:center;height: 0px; ">
		(Le prix le moins cher)
		<input id="findlivraison"  type="text" name="findprice" value="" size="10" />
		<br>(Livraison)
		<input id="findshipping"  type="text" name="findshipping" value="" size="10" />
		</td>
</tr>
      <tr>	
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">	  
		Suggestion prix EBAY:
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['suggestebay'];?> usd
		</td>
</tr>
<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		NOTRE Prix EBAY
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align:center;
		<?if(($dateverification > $dateretail)||($_POST['suggestebay']>$_POST['price_with_shipping'])){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>
		height: 0px; ">
		(finir par <?echo $endprix;?> en dollar am&eacute;ricain)<br><input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping'], 2,'.', '');?>" size="10" />
		</td>
</tr>
     <tr>
	  <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix v&eacute;rifi&eacute; le: 
		</td>
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align: center;<?if($dateverification > $dateretail){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">
		<?echo $dateretail2;?>
		</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle;  height: 0px; background-color: #030282; color: white; width: 200px;">
		Livraison: 
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['shipping'];?> usd
		</td>
</tr>
      <tr>	
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">	  
		Suggestion prix:
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center;<?if($_POST['suggest']>$_POST['pricemagasin']){?>background-color: red; color: white;
		<?}else{?>background-color: green; color: white;<?}?>height: 0px; ">
		<?echo $_POST['suggest'];?> cad
		</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix MAGASIN:
	</td>
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align:center;<?if(($_POST['suggest']>$_POST['pricemagasin'])||($dateverification > $datemagasin)){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">
		(finir par <?echo $endprix;?> en dollar canadien)<br><input id="pricemagasin"  type="text" name="pricemagasin" value="<?echo $_POST['pricemagasin'];?>" size="10" />
</td>
</tr>
      <tr>
	  <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix v&eacute;rifi&eacute; le:
	</td>
		<td colspan="1" rowspan="1" style="vertical-align: middle; text-align: center;<?if(($dateverification > $datemagasin)){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>  height: 0px; ">

		<?echo $datemagasin2;?> 
		 
		</td>
</tr>

</tr>
      <tr>

	<td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		
		 <a href="createbarcodemagasinun.php?product_id=<?echo $_POST['product_id'];?>&sku=<?echo (string)$_POST['sku'] ?>" target="google" style="color:#ff0000"><strong>LABEL PRIX</strong></a>

		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">

			Demande de validation <input type="checkbox" id="pourverification" name="pourverification" onclick='change_raison()' value="oui"/> 
			Raison: <input type="text" id="remarque_interne" name="remarque_interne" value="<?echo $_POST['remarque_interne'];?>" size="80" />
		</td>
</tr>
 <tr>

    <td colspan="3" style="vertical-align:  middle; text-align:center;height: 16px; background-color: #e4bc03; width: 200px;"> 
		
		 <input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
		 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>	 
	  </tr>
    </tbody>
  
  </table>
<?if ($_POST['product_id'] !=""){?>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&LH_Sold=1&LH_Complete=1&LH_BIN=1&rt=nc&LH_ItemCondition=3","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&_sop=15&LH_BIN=1&rt=nc&LH_ItemCondition=3","ebaynew");
window.open("https://www.upcitemdb.com/upc/<?echo (string)$_POST['upc'];?>","google");
</script>
<?}?>
		<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
		<input type="hidden" name="new" value="<?echo $_POST['new'];?>" />
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="shipping" value="<?echo $_POST['shipping'];?>" />	
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
		<input type="hidden" name="product_id_no" value="<?echo $_POST['product_id_no'];?>"/> 
		<input type="hidden" name="product_id_r" value="<?echo $_POST['product_id_r'];?>"/> 
		<input type="hidden" name="changeupc" value="<?echo $_POST['changeupc'];?>"/> 
		<input type="hidden" name="sku" value="<?echo $_POST['sku'];?>"/>

		



</form>

</body>
</html>

<? // on ferme la connexion &agrave; 

mysqli_close($db); ?>