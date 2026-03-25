<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 


if (isset($_POST['sku']) && $_POST['sku']!=""){
			$sql = 'SELECT * FROM `oc_currency` where currency_id=4';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$currency=$data['value'];
			
			//echo $_POST['sku'];
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['name']=$data['name'];
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
			
			
			$sql5 = 'SELECT * FROM `oc_product_special` where product_id='.$data['product_id'];
	//echo $sql5;
			$req5 = mysqli_query($db,$sql5);
			$data5 = mysqli_fetch_assoc($req5);
			$_POST['price']=$data5['price']*1.14975;
			$_POST['retailprice']=$data['price']*1.14975;
			//echo $_POST['price']/$_POST['retailprice'];
			$retailprice=number_format($_POST['retailprice']*$currency,2);
			if (($_POST['price'])==0){
				$pricerabais="COMMIS";
				$pourcentecono="";
			}else{
				if (($_POST['price']/$_POST['retailprice'])>0.80) {
					$pricerabais=number_format($_POST['retailprice']*$currency*.80,2);
				//echo "allo";
				}else{
					$pricerabais=number_format((($_POST['price'])*$currency),2);
				}
				$pourcentecono=number_format(((1-($pricerabais/$retailprice))*100),0)."%";
			}
			
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
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$categoryname=$data['name'];
			$_POST['category_id']=$data['category_id'];
			$_POST['category_id']=$data['category_id'];
			

			$new=1;
	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$_POST['andescription']=$data9['description'];
			$algopixbrand=$data9['brand'];
			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8);
			
if($_POST['etat']=="9")$algoetat="Neuf";
if($_POST['etat']=="99")$algoetat="Neuf Boite Ouverte";
if($_POST['etat']=="2")$algoetat=="Reusine";
if($_POST['etat']=="22")$algoetat="Reusine";
if($_POST['etat']=="8")$algoetat="Usage";	  

	 $_POST['andescription']=$data9['description'];
	
	 $picexterne=$data9['imageurl'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
	 }

}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>price.php</title>
 

  
  
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
  </script>
  
  <style> 
<link href="stylesheet.css" rel="stylesheet">
  </style>
</head><body>

<form method="post" action="price.php" name="listing">

  
  <table style="text-align: left; width: 1053px; height: 100%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">

    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; width: 112px;">
		<img style="width: 488px; height: 145px;" alt="" src="http://www.phoenixsupplies.ca//image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: middle; background-color: rgb(255, 204, 204); font-weight: bold; text-align: center;"><h3><?if($new==1){?><a href="listingusa.php" class="button--style-red">Changer d'item</a><?}?> <a href="menulisting.php" class="button--style-red">Retour au MENU</a> 		<?if($new==1){?>
		
		<?}?></h3>
        </td>
        <td style="vertical-align: middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: left;">Sku<br>
        </td>
        <td style="vertical-align: middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: left;"><input type="hidden" name="skucheck" value="<?echo $_POST['sku'];?>" />
		<input id="sku"  type="text" name="sku"  value="" maxlength="255"  autofocus>
		
        </td>
        <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white; font-weight: bold; text-align: center;">
        </td>
      </tr>
      <tr>
        <td colspan="1" rowspan="9" style="vertical-align:  middle; height: 24px; width: 342px;">
				<?
			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);	


			
			echo '<img src="https://www.phoenixsupplies.ca/image/'.$data['image'].'" width="250">';
				$imagenew=	'<img src="https://www.phoenixsupplies.ca/image/'.$data['image'].'" width="150">';	
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
					echo '<img src="https://www.phoenixsupplies.ca/image/'.$data2['image'].'" width="250">';
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
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
$brandrecom="";
$data = mysqli_fetch_assoc($req);
echo $data['name'];
	
?>

        </td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
	   <tr>
        <td style="vertical-align:  middle; height: 15px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Prix retail:</span></td>
        <td style="vertical-align:  middle; height: 15px; width: 506px;font-weight: bold;"><font size="20" color="blue"><STRIKE>$ <?echo $retailprice;?></STRIKE></font></td>	
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Prix
	:</span></td>
        <td style="vertical-align:  middle; height: 16px; width: 506px;font-weight: bold;"><font size="20" color="red">$ <?echo $pricerabais?></font></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 16px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Economie
	:</span></td>
        <td style="vertical-align:  middle; height: 16px; width: 506px;font-weight: bold;"><font size="20" color="white">$ </font><font size="20" color="red"><?echo $pourcentecono;?></font></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Categorie:</span><br>
        </td>
        <td style="vertical-align:  middle; width: 506px;font-weight: bold;"><?echo $categoryname;?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 13px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Couleur:</span></td>
        <td style="vertical-align:  middle; height: 13px; width: 506px;font-weight: bold;"><?echo $data['color'];?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>
      <tr>
        <td style="vertical-align:  middle; height: 74px; background-color: rgb(102, 0, 0); color: white; width: 112px;"><span style="font-weight: bold;">Etat:</span></td>
        <td style="vertical-align:  middle; height: 74px; width: 506px;font-weight: bold;"><?echo $algoetat;?></td>
        <td style="vertical-align:  middle; width: 57px; text-align: center; background-color: rgb(102, 0, 0);"></td>
      </tr>

	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: rgb(255, 204, 204); color: white; width: 112px;"> 

		
		</td>
		
	 
	  </tr>
    </tbody>
  
  </table>

</form>


</body></html>

<?
mysqli_close();
?>