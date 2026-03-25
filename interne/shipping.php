<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte &agrave; MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 



//echo $_POST['new'].(string)$_POST['sku'] .$_POST['location_magasin'].$_POST['quantitymagasin_ajouter'];

if (($_POST['upc'] !="" || $_POST['name'] !="") && $_POST['usd']>1 ){ 
			$_POST['weightshipping']=$_POST['weight']+($_POST['weight2']/16);
			
						$info_shipping=get_shipping ($connectionapi,$_POST['weightshipping'],$_POST['length'],$_POST['width'],$_POST['height'],$db,(string)$_POST['upc'],12919);
						$_POST['shipping']=$info_shipping['shipping'];
						$_POST['carrier']=$info_shipping['carrier'];
						$info_shipping=get_shipping ($connectionapi,$_POST['weightshipping'],$_POST['length'],$_POST['width'],$_POST['height'],$db,(string)$_POST['upc'],98001);
						$_POST['other']=$info_shipping['shipping'];
						//print("<pre>".print_r ($info_shipping,true )."</pre>");
					
					
					
					//get_from_upctemp($data['upc']);
					//echo file_get_contents("https://www.upcitemdb.com/norob/alink/?id=v2u2z2v2v253b464s2&tid=1&seq=1617680392&plt=c35880419e05f3f2fa8fc94c333c297b");
					
					$_POST['upctemp']=get_from_upctemp($_POST['upc']);
					

					//verification prix neuf

			
				
				$_POST['suggest']=(($_POST['price_with_shipping']*.85)-$_POST['shipping']);
				$_POST['suggest']= number_format($_POST['suggest']*$_POST['usd'], 2,'.', '');
			//	$price_replace=explode('.',$_POST['suggest']);
				//$_POST['suggest']=$price_replace[0]+$endprix;
				//$_POST['pricemagasin']= number_format($_POST['pricemagasin']*$_POST['usd'], 2, '.', '');
				/* if($_POST['pricemagasin']<1){
					$_POST['pricemagasin']=number_format(0.00, 2,'.', '');
				}else{
					$price_replace=explode('.',$_POST['pricemagasin']);
					$_POST['pricemagasin']=$price_replace[0]+$endprix;
				}
				if(($_POST['pricesuggest']>0&&($dateverification <= $datapricesuggest))){//
					$_POST['suggestebay']=$_POST['pricesuggest']*$prixconvert;
					$price_replace=explode('.',$_POST['suggestebay']);
					$_POST['suggestebay']=$price_replace[0]+$endprix;
					//echo "allo";
				}elseif(($_POST['findprice']+$_POST['findshipping'])>0){
					$_POST['suggestebay']=($_POST['findprice']+$_POST['findshipping']-.95)*$prixconvert;
					$price_replace=explode('.',$_POST['suggestebay']);
					$_POST['suggestebay']=$price_replace[0]+$endprix;
				} */
				
				
				
}?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>

		<script>


</script>
    <title></title>
	<script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script>    tinymce.init({      selector: '#mytextarea'    });  </script>
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
<form id="form_67341" class="appnitro" action="shipping.php" method="post">

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

        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><a href="listing.php" >Retour au MENU</a><br> 
        </td>
        <td colspan="3" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Shipping Cost</h1>
        </td>
      </tr>
      <tr>
 
		<?if($resultebay!=""){?>
			   <tr>
        <td colspan="3" rowspan="1" style="vertical-align:  middle; text-align: center;height: 24px; width: 342px;background-color: red;color:white">
			<?echo $resultebay;?>
        </td>
 
	  </tr>
		<?}?>

	 <tr>
	         <td colspan="1" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
			</td>
	     <td colspan="2" style="vertical-align:  middle; text-align:center;height: 16px; background-color: white; width: 200px;"> 
		 <?echo browse_ebay($connectionapi,$_POST['name'],(string)$_POST['upc'],"3","3",$db,$_POST['marketplace_item_id']); ?>
		</td>	
	 </tr>

	       <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		UPC:
		</td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
	
		<input  name="upc" type="text" value="<?echo (string)$_POST['upc']	;?>"/>	

		</td>

		</tr>
		      <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Titre:
		</td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
	
		<input  name="name" type="text" value="<?echo $_POST['name']	;?>"/>	

		</td>

		</tr>
			      <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		USD rates:
		</td>
        <td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center; height: 0px; ">
	
		<input  name="usd" type="text" value="<?echo $_POST['usd']	;?>"/>	

		</td>

		</tr>
           <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Dimension:</td>
        <td style="vertical-align:  middle; height: 15px; ">
			<table style="text-align: left; width: 100%; height: 50%; margin-left: auto; margin-right: auto;" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td>
			Largeur 
			</td>
				<td>
				<input id="length"  type="text" name="length" value="0<?echo intval($_POST['length']);?>" maxlength="5" />
				</td>
					</tr>
				<tr>
				<td>
			
			Profondeur 
			</td>
				<td>
				<input id="width"  type="text" name="width" value="0<?echo intval($_POST['width']);?>" maxlength="5" />
				</td>
								</tr>
				<tr>
				<td>

			Hauteur 
			</td>
				<td><input id="height"  type="text" name="height" value="0<?echo intval($_POST['height']);?>" maxlength="5" />
				</td>
			</tr>
			</table>
		</td>	
      </tr>
      <tr>
        <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Poids:
		</td>
        <td style="vertical-align:  middle; text-align: center;height: 16px; ">
		<table style="text-align: left; width: 100%; height: 50%; margin-left: auto; margin-right: auto;" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td>Lbs
			</td>	  
			<td>
				<input id="weight"  type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" />
				</td>
											</tr>
				<tr>	
		<td>Oz
		</td>
			
			<td>
				<input id="weight2"  type="text" name="weight2" value="<?echo $_POST['weight2'];?>" maxlength="5" />
		</td>

		</tr>
</table>
</td>
</tr>
	<tr>
		<td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix payé: 
		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align:center;
		<?if($_POST['pricemagasin'] > $_POST['price']*.90){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>
		height: 0px; ">
		(Prix en dollar canadien)<br>
		<input id="price"  type="text" name="price" value="<?echo number_format($_POST['price'], 2,'.', '');?>" size="10" />
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
		<?if ($_POST['price_with_shipping']==0)$_POST['price_with_shipping']=$_POST['suggestebay'];?>
		<?if(($dateverification > $dateretail)||($_POST['suggestebay']>$_POST['price_with_shipping'])){?>background-color: red; color: white;<?}else{?>background-color: green; color: white;<?}?>
		height: 0px; ">
		(finir par <?echo $endprix;?> en dollar am&eacute;ricain)<br><input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping'], 2,'.', '');?>" size="10" />
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
		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;<?if($_POST['suggest']<$_POST['pricemagasin']){?>background-color: red; color: white;
		<?}else{?>background-color: green; color: white;<?}?>height: 0px; ">
		<?echo $_POST['suggest'];?> cad
		</td>
</tr>
   <tr>	
		<td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">	  
		Profit Potentiel:
		</td>
		<td colspan="2" rowspan="1" style="vertical-align:  middle; text-align: center;<?if(($_POST['suggest']-$_POST['price'])<0 || ($_POST['price_with_shipping']-$_POST['price']-$_POST['other'])<0){?>background-color: red; color: white;
		<?}else{?>background-color: green; color: white;<?}?>height: 0px; ">
		de <?echo $_POST['suggest']-$_POST['price'];?> à <?echo number_format(($_POST['price_with_shipping']-$_POST['price']-$_POST['other'])*$_POST['usd'], 2,'.', '');?>
		</td>
</tr>
 
 
 <tr>

    <td colspan="3" style="vertical-align:  middle; text-align:right;height: 16px; background-color: #e4bc03; width: 200px;"> 
		
	
		 
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>	 
	  </tr>
    </tbody>
  
  </table>
 

</form>

</body>
<?
mysqli_close($db); ?>