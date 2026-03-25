<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
if ($_GET['action']=="ajouter"){
	(string)$_POST['sku'] =$_GET['sku'];	
	 $test= ajouter_item ($connectionapi,$_GET['sku'],$db); 
	//echo 'allo'; 
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
if (isset($_POST['sku'] ) && $_POST['new']==1){
		$inventaire=1;
		//if($_POST['quantity']==0)$inventaire=0;
		
		$sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity+'.$_POST['quantity'].' where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'SELECT quantity FROM `oc_product` where `product_id`='.$_POST['product_id'];//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		//echo $sql2.'<br><br>';
		$data2 = mysqli_fetch_assoc($req2);
		if($_POST['quantity_actuel']<($data2['quantity']+$_POST['quantity']+$_POST['unallocated_quantity'])){
			$updquantity=$_POST['quantity']+$_POST['unallocated_quantity'];
		}else{
			$updquantity=$data2['quantity']+$_POST['quantity']+$_POST['unallocated_quantity'];
		}
	
		
		
		if ($_POST['location']!=""){
		$quantitytotal=$_POST['quantitytotal']+$_POST['quantity'];
		$sql2 = 'UPDATE `oc_product`SET quantity="'.$quantitytotal.'",remarque_interne="'.$_POST['remarque_interne'].'",weight_class_id=5,tax_class_id=9, stock_status_id=7,ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
	//	echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);	
	//	$sql2 = 'UPDATE `oc_product_special` SET date_price_upd="'.$Date.'",`price` = '.number_format($_POST['pricemagasin']/1.34, 2, '.', '').' WHERE `oc_product_special`.`product_id` ='.$_POST['product_id'];

			//echo $sql2.'<br><br>';
	    $req2 = mysqli_query($db,$sql2); 
}$_POST['quantity']=0;
		
		if($_POST['processing']=="oui"){
			//echo $_POST['price_with_shipping'].'<br>'.$updquantity.'<br>'.$_POST['marketplace_item_id'].'<br>'.(string)$_POST['upc'].'<br>';
			if($_POST['pourverification']=="oui"){
				$_POST['price_with_shipping']=0;
				$sql2 = 'UPDATE `oc_product` SET status=0,remarque_interne="Prix à vérifier ('.$_POST['remarque_interne'].')",ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				update_to_ebay($connectionapi,0,0,$_POST['marketplace_item_id'],$_POST['product_id']);
				header("location: listing.php?sku=".$_POST['upc']); 
				exit();
				
			}elseif($_POST['marketplace_item_id']>0){
				
				$daterelisted = new DateTime($_POST['ebay_date_relisted']);
				$daterelisted = $daterelisted->format('Y-m-d');
				$daterelisted2 = new DateTime($data2['date_price_upd']);
				$daterelisted2 = $daterelisted2->format('Y-m-d');
				$daterelisted=date_parse ($daterelisted);
				//echo $_POST['ebay_date_relisted']."<br>";
				//print_r($daterelisted);
				
				
				$dateverification = new DateTime('now');
				$dateverification->modify('-15 day'); // or you can use '-90 day' for deduct
				$dateverification = $dateverification->format('Y-m-d');
				$dateverification=date_parse ($dateverification);
				//print_r($dateverification)."<br>".print_r($daterelisted); 
/* 				if($dateverification>$daterelisted && $updquantity>0 && $pourverification==""){
					//echo "ca marche";
					if($updquantity>1){
						$updquantity=($data2['quantity']+$_POST['quantity']+$_POST['unallocated_quantity'])/2;
					}
					update_to_ebay($connectionapi,$_POST['price_with_shipping'],$updquantity,$_POST['marketplace_item_id'],(string)$_POST['upc']); // a changer apres inventaire correct
					end_to_ebay($connectionapi,$_POST['marketplace_item_id']);
					relist_to_ebay($connectionapi,$_POST['product_id'],$_POST['marketplace_item_id'],$db);
				}else{ */
					update_to_ebay($connectionapi,0,$updquantity,$_POST['marketplace_item_id'],$_POST['product_id']); // a changer apres inventaire correct
/* 					echo $updquantity;
				} */
			}
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
		}

}

if ((string)$_POST['sku'] !=""){ 
		$sql = 'SELECT *,P.price AS price_retail, P.product_id,P.ebay_id,P.sku,name,P.quantity_actuel,p.unallocated_quantity,P.image,P.upc,P.price_with_shipping,P.weight,P.length,P.width,P.height,P.date_price_upd,P.condition_id FROM `oc_product` AS P,`oc_product_description` where P.product_id=oc_product_description.product_id  and P.sku like "'.(string)$_POST['sku'] .'"';
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data = mysqli_fetch_assoc($req);
		if(mysqli_num_rows($req)>0){
			if($_POST['product_id']==""){
					$sql2 = 'select * from `oc_product_special` WHERE `product_id` ='.$data['product_id'];

					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
					$data2 = mysqli_fetch_assoc($req2);
					
					if(mysqli_num_rows($req2)==0){
						$sql7 = "INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority` ,`price`,`date_start`,`date_end`) VALUES ('".$data['product_id']."', '1', '1','0', '2018-09-01','2218-09-01')";
						$req7 = mysqli_query($db,$sql7);
					//echo $sql7.'<br><br>';
					}
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
					//	$_POST['shipping']=get_shipping ($connectionapi,$data['weight'],$data['length'],$data['width'],$data['height'],$db,(string)$data['upc']);
/* 					} */
					$_POST['pricemagasin']=$data2['price']*1.34;
					$_POST['price_with_shipping']=$data['price_with_shipping'];
					(string)$_POST['upc']=(string)$data['upc'];
					$_POST['upcorigine']=(string)$data['upc'];
					$_POST['price']=$data['price_retail']*1.34;
					$_POST['quantity']=0;
					$_POST['location']=$data['location'];
					$_POST['condition_id']=$data['condition_id'];
					$_POST['marketplace_item_id']=$data['marketplace_item_id'];
					$_POST['skuold']=$data['sku']; 
					$_POST['remarque_interne']=$data['remarque_interne'];;
					
			}
			$_POST['new']=1;

			$_POST['name']=$data['name'];
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
			$datemagasin2 = new DateTime($data2['date_price_upd']);
			$datemagasin2 = $datemagasin2->format('Y-m-d');
			$datemagasin=date_parse ($datemagasin);
			$dateretail = new DateTime($data['date_price_upd']);
			$dateretail = $dateretail->format('Y-m-d');
			$dateretail2 = new DateTime($data['date_price_upd']);
			$dateretail2= $dateretail2->format('Y-m-d');
			$dateretail=date_parse ($dateretail);
			$dateverification = new DateTime('now');
			$dateverification->modify('-3 month'); // or you can use '-90 day' for deduct
			$dateverification = $dateverification->format('Y-m-d');
			$dateverification=date_parse ($dateverification);
			
			
/* 			if($dateverification > $dateretail)  print_r($dateretail);
			if($dateverification > $datemagasin) print_r($datemagasin); */
			
			//echo $_POST['shipping'].'<br><br>';
		}
				if ($_POST['condition_id']==9){
					$prixconvert=1; 
					$endprix=.95;
				}elseif($_POST['condition_id']==99){
					$prixconvert=.90;
					$endprix=.85;	
				}elseif($_POST['condition_id']==22){
					$prixconvert=.80;
					$endprix=.75;
				}
				
				$_POST['suggest']=(($_POST['price_with_shipping']*.95)-$_POST['shipping']);
				$_POST['suggest']= number_format($_POST['suggest']*1.34, 2,'.', '');
				$price_replace=explode('.',$_POST['suggest']);

				
				
				$_POST['suggest']=$price_replace[0]+$endprix;
				//$_POST['pricemagasin']= number_format($_POST['pricemagasin']*1.34, 2, '.', '');
				if($_POST['pricemagasin']<1){
					$_POST['pricemagasin']=number_format(0, 2,'.', '');
				}else{
					$price_replace=explode('.',$_POST['pricemagasin']);
					$_POST['pricemagasin']=$price_replace[0]+$endprix;
				}
				
				$_POST['suggestebay']=($_POST['findprice']+$_POST['findshipping']-.95)*$prixconvert;
				$price_replace=explode('.',$_POST['suggestebay']);
				$_POST['suggestebay']=$price_replace[0]+$endprix;
				
}?>

<html xmlns="http://www.w3.org/1999/xhtml">
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
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="inventairemagasin.php" method="post">

<?/*<h3>
		<h3><a href="inventairemagasin.php?sku=<?echo substr((string)$_POST['sku'] ,0,12)."NO";?>" target="_self" style="color:#ff0000"><strong>Changer New Other</strong></a> 
	<a href="inventairemagasin.php?sku=<?echo substr((string)$_POST['sku'] ,0,12);?>" target="_self" style="color:#ff0000"><strong>Changer New</strong></a></h3>
	<a href="inventairemagasin.php?sku=<?echo (string)$_POST['sku'] ;?>&action=ajouter" target="_self" style="color:#ff0000"><strong>Ajouter item pour inventaire</strong></a></h3>*/?>
 <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 200px;">
		<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><a href="inventairemagasin.php" >Annuler</a><br> <a href="menuinventaire.php" >Retour au MENU</a>
        </td>
        <td colspan="4" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		Inventaire en MAGASIN<?echo '<br><h3><font color="red">'.$erreurvide.'</font></h3>';?>
        </td>
      </tr>



      </tr>
      <tr>
        <td colspan="1" rowspan="17" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;">
				<?
			if($data['image']!="")echo '<img src="https://www.phoenixliquidation.phoenixdepot.com/image/'.$data['image'].'" width="200">';
			?>

        </td>
		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">Sku
       <?if ($data['name']==""&&$sku!=""){?>
		<a href="insertionitemusa.php?upc=<? echo $sku;?>&action=listingusa"><strong>Ajouter</strong></a> 
		<?}else{?>
		<a href="createsmallbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
		<?}?>
	   </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="sku"  type="text" name="sku"  value="<?echo (string)$_POST['sku'] ;?>" maxlength="255"  autofocus> 
		
		</td>
	 </tr>
	       <tr>
        <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Nu Ebay:
		</td>
        <td colspan="1" rowspan="1" style="<?if($_POST['marketplace_item_id']==0||$_POST['marketplace_item_id']==""){?>background-color:red; color: white;<?}else{?>background-color:green; color: white;<?}?> vertical-align:  middle; text-align:center; height: 0px; ">
		<?echo $_POST['marketplace_item_id'];?>
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
         <td style="vertical-align:  middle;  height: 0px; background-color: #030282; color: white; width: 200px;">
		 Quantité totale: 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['quantitytotal'];?>     
		</td>
      </tr>
		<tr>
        <td style="vertical-align:  middle; height: 25px; background-color: #030282; color: white; width: 200px;">
		Location:
		</td>
        <td style="vertical-align:  middle; text-align:center;height: 25px; ">
		<input id="location" class="element text medium" type="text" name="location" value="<?echo $_POST['location'];?>" maxlength="120" />
		</td>

      </tr>
      <tr>
         <td style="vertical-align:  middle;  height: 0px; background-color: #030282; color: white; width: 200px;">
		 Quantité en magasin: 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align: center; height: 0px; ">
		<?echo $_POST['unallocated_quantity'];?>     
		</td>
      </tr>
      <tr>
         <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		 Quantité ajoutée: 
		 </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" />       
		</td>
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
		
		 <input type="checkbox" name="processing" value="oui" />	Procèder et passer à un nouvel item 
		 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>	 
	  </tr>
    </tbody>
  
  </table>

		<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
		<input type="hidden" name="new" value="<?echo $_POST['new'];?>" />
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="shipping" value="<?echo $_POST['shipping'];?>" />	
		<input type="hidden" name="upcorigine" value="<?echo $_POST['upcorigine'];?>" />
		<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>" />
		<input type="hidden" name="skuold" value="<?echo $_POST['skuold'];?>" />
		<input type="hidden" name="unallocated_quantity" value="<?echo $_POST['unallocated_quantity'];?>" />
		<input type="hidden" name="ebay_date_relisted" value="<?echo $_POST['ebay_date_relisted'];?>"/> 
		<input type="hidden" name="quantitytotal" value="<?echo $_POST['quantitytotal'];?>"/> 
		



</form><?if ($_GET['sku']!=""){?>
<h1><a href="interne.php" class="">Retour au MENU</a></h1>

<?}?>
<p id="footer">
</body>
</html>

<? // on ferme la connexion à 

mysqli_close($db); ?>