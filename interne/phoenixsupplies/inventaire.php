<? 
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
if (isset($_GET['sku']))$_POST['sku']=$_GET['sku'];

if($_POST['sku']=="")$_POST['new']=0;
if (isset($_POST['sku']) && $_POST['new']==1){
		$inventaire=1;
		if($_POST['quantity']==0)$inventaire=0;
		$weight=$_POST['weight']+($_POST['weight2']/16);
		$sql2 = 'UPDATE `oc_product` SET inventaire='.$inventaire.',quantity='.$_POST['quantity'].', location ="'.$_POST['location'].'"';
		$sql2 .=', `weight`="'.$weight.'",`height`="'.$_POST['height'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'].'",ebay_last_check="2020-09-01" where product_id='.$_POST['product_id'];
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		
/* 		if($_POST['import_photo']=="oui" && $_POST['marketplace_item_id']>0){
			
			echo "Photos importé de EBAY<br>";
		} */
		
		if($_POST['processing']=="oui"){
			mise_en_page_description($connectionapi,$_POST['product_id'],$db);
			//echo $_POST['processing'];
			if($_POST['marketplace_item_id']>0){
				revise_ebay_product_inventaire($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],$_POST['quantity'],$db);
			
				//delete_photo($_POST['product_id'],"",$db);
				//link_to_download($connectionapi,$_POST['product_id'],$_POST['marketplace_item_id'],"",$db);
			}
			//update_to_ebay($connectionapi,0,$_POST['quantity'],$_POST['marketplace_item_id']);
			$_POST['new']=0;
			$_POST['sku']="";
		}
}
if (isset($_POST['sku']) && $_POST['sku']!=""){
		if($_POST['ancien_sku']!="")$_POST['sku']=$_POST['ancien_sku'];
		$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku like "'.$_POST['sku'].'"';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement 
		$data = mysqli_fetch_assoc($req);
		$_POST['marketplace_item_id']=check_product_on_ebay($connectionapi,$data['product_id'],$db);
			$weighttab=explode('.', $data['weight']);
			$_POST['weight']=$weighttab[0]+0;
			$_POST['weight2']=substr($weighttab[1],0,4)*16/10000;
			$_POST['length']=$data['length'];
			$_POST['width']=$data['width'];
			$_POST['height']=$data['height'];
		$_POST['new']=1;
		
}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
	<link href="stylesheet.css" rel="stylesheet">
<style> 

.fsSubmitButton
{
	border-top:		2px solid #a3ceda;
	border-left:		2px solid #a3ceda;
	border-right:		2px solid #4f6267;
	border-bottom:		2px solid #4f6267;
	height:			200px;
	width:			400px;
	padding:		10px 20px !important;
	font-size:		25px !important;
	background-color:	#ffffff;
	font-weight:		bold;
	color:			#000000;
}
</style>

</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="inventaire.php" method="post">

<h1>Inventaire Item</h1>
<h3><a href="listing.php" class="">Retour au listing</a></h3>


<?if ($_POST['new']==1){?>
<h3>
		<a href="createbarcodetotal.php?qtetot=1&sku=<?echo (string)$_POST['sku'];?>&test3333" target="google" style="color:#ff0000"><strong>Creation LABEL</strong></a> 
</h3>
<?}?>
<table style="width:100%">
<?if ($_POST['new']==1){?>
<tr>
	<td>
<?if($_POST['marketplace_item_id']>0){?><input type="checkbox" name="import_photo" value="oui" />	importer photo de EBAY <br><?}?>
		<img height="200" src="http://www.phoenixsupplies.ca/image/<?echo $data['image'];?>"/>
		
	</td>
	<td>
	<table>
<?}?>
	  <tr>
	  	   <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">SKU :
		   <?if($_POST['new']==1){?>
<input type="checkbox" name="processing" value="oui" />	Proc&eacute;der 
<?}?></td>
		 <td style="vertical-align:  middle; height: 15px; ">
		 <input id="sku" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" autofocus name="sku" value="<?echo $data['sku'];?>" maxlength="255" />
		 </td>
		 </tr>
		 <?if ($_POST['new']==1){?>
		<tr>
	    <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">Titre :</td>
		 <td style="vertical-align:  middle; height: 15px; "><?echo $data['name'];?>
		 </td>
		<tr>
				</tr>
	    <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">Ebay ID :</td>
		 <td style="vertical-align:  middle; height: 15px; "><?echo $_POST['marketplace_item_id'];?>
		 </td>
		<tr>
			  </tr>
	    <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">Location :</td>
		 <td style="vertical-align:  middle; height: 15px; ">
		 <input id="location" class="element text medium" type="text" name="location" value="<?echo $data['location'];?>" maxlength="120" />
		 </td>
		<tr>
			  </tr>
	    <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">Quantité :</td>
		 <td style="vertical-align:  middle; height: 15px; ">
		 <input id="quantity"  class="element text currency" type="text" name="quantity" value="<?echo $data['quantity'];?>" size="10" /> 
		 </td>
		<tr>
		</tr>
        <td style="vertical-align:  middle; height: 15px; background-color: #1a1d5b; color: white; width: 200px;">Dimension:</td>
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
        <td style="vertical-align:  middle; height: 16px; background-color: #1a1d5b; color: white; width: 200px;">
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
		</td>
		
		<?}?>
</table> 

<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="ebay_id" value="<?echo $_POST['marketplace_item_id'];?>" />
<input type="hidden" name="ancien_sku" value="<?echo $_POST['sku'];?>" />
<input type="hidden" name="new" value="<?echo $_POST['new'];?>" />
<input type="hidden" name="status" value="1" />
<input id="saveForm" class="fsSubmitButton" type="submit" name="submit" value="Submit" />

</form>
<p id="footer"> 
</body>
</html>
<?  // on ferme la connexion à 
mysqli_close($db); ?>