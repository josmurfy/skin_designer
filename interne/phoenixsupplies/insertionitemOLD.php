<?
//print_r($_POST['accesoires']);
if ($_GET['sku'] != ""){
	(string)$_POST['sku']=$_GET['sku'];
	$_POST['etape']=0;
	}
//translate('Hello my name is Jonathan');
$sku=(string)$_POST['sku'];
// on se connecte à MySQL 

include 'connection.php'; include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//echo $_POST['marketplace_item_id'];
if(isset($_POST['ebay_id_a_cloner'])&&(!isset($_POST['marketplace_item_id'])||$_POST['marketplace_item_id']=="")){
	//echo $_POST['ebay_id_a_cloner'];
	echo '<script>window.open("https://bulksell.ebay.com/ws/eBayISAPI.dll?SingleList&sellingMode=SellLikeItem&lineID='.$_POST['ebay_id_a_cloner'].'","ebaylisting")</script>';
}
if(isset($_POST['marketplace_item_id']) && $_POST['marketplace_item_id']!=""){
	$result=get_ebay_product($connectionapi,$_POST['ebay_id_a_cloner']);
	$json = json_decode($result, true);
			$catsearch=$json["Item"]["PrimaryCategory"]["CategoryID"];
			//echo addslashes($json["Item"]["Title"]);
			$weight=$json["Item"]["ShippingDetails"]["CalculatedShippingRate"]["WeightMajor"];
			$weight2=$json["Item"]["ShippingDetails"]["CalculatedShippingRate"]["WeightMinor"];
			$weight=$weight+($weight2/16);
	$_POST['ebay_id_a_cloner']=0;
	$_POST['efface_ebayid_cloner']=1;
	$_POST['etape']=1;

	//apres le upload
	//$_POST['image']=$data['image'];
}

//if ($_POST['manufacturer']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer'];
//echo $_POST['manufacturer_recom'];
//echo $_POST['new'];
if (isset($result) && (isset($_POST['marketplace_item_id']) && $_POST['marketplace_item_id']!="")){
		//echo "allo";
		
		 $product_id_princ= insert_item((string)$_POST['sku'],"",$result,$_POST['marketplace_item_id'],0,$_POST['etat'],$_POST['name'],$db);
		 
		header("location: modificationitem.php?insert=oui&sku=".(string)$_POST['sku']); 
		exit(); 
		$_POST['etape']=2;

}
?>

<html> 
<head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="insertionitem.php?insert=oui&sku=<? echo (string)$_POST['sku'];?>" method="post">

  <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="http://www.phoenixsupplies.ca/image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
      <tr>
	   
        <td style="vertical-align: middle; background-color: #734c4c;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		

        </td>
        <td colspan="4" style="height: 50; background-color: #1a1d5b; color: white;  text-align: center;">
		Ajouter Produit (<?echo $_POST['product_id']?>)
		</td>
     </tr>
	 <tr>

	 		 <td style="vertical-align:  middle; height: 0px; background-color: #1a1d5b; color: white; width: 200px;">
			 UPC: 
			 <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;text-align: center;">
			<svg class="barcode"
				jsbarcode-value="<?echo $_POST['sku'];?>"
				jsbarcode-textmargin="0"
				jsbarcode-height="24"
				jsbarcode-fontoptions="bold"
				jsbarcode-fontsize="12">
			</svg>
			<script>
			JsBarcode(".barcode").init();
			</script>
			
		</td>
	</tr>
		<?if (!isset($_POST['efface_ebayid_cloner'])||$_POST['efface_ebayid_cloner']==""){?>
	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #1a1d5b; color: white; width: 200px;">
			Ebay ID à cloner: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" autofocus>
		</td>
	</tr>
	<?}?>
	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #1a1d5b; color: white; width: 200px;">
			Ebay ID émis: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="ebay_id"  type="text" name="ebay_id"  value="<?echo $_POST['marketplace_item_id'];?>" maxlength="255" >
		</td>
	</tr>
	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #1a1d5b; color: white; width: 200px;">
			Titre de l'item: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="name"  type="text" name="name"  value="<?echo $_POST['name'];?>" maxlength="80" >
		</td>
	</tr>
	<tr>
			 <td style="vertical-align:  middle; height: 0px; background-color: #1a1d5b; color: white; width: 200px;">
			Condition: 
        </td>
	        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<table>
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9" <?if ($_POST['etat']==9){?>checked<?}?>/> 
				<label class="choice" for="etat_1">New</label></td>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99" <?if ($_POST['etat']==99){?>checked<?}?>/> 
				<label class="choice" for="etat_2">New (Other)</label> <br /></td>
		  </tr>
		  <tr>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2" <?if ($_POST['etat']==2){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22" <?if ($_POST['etat']==22){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Seller Refurbished</label> <br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8" <?if ($_POST['etat']==8){?>checked<?}?>/> 
				<label class="choice" for="etat_4">Used - Like New</label> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7" <?if ($_POST['etat']==7){?>checked<?}?>/> 
				<label class="choice" for="etat_5">Used - Very Good</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6" <?if ($_POST['etat']==6){?>checked<?}?>/> 
				<label class="choice" for="etat_6">Used - Good</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5" <?if ($_POST['etat']==5){?>checked<?}?>/> 
				<label class="choice" for="etat_7">Used - Poor</label><br /> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1" <?if ($_POST['etat']==1){?>checked<?}?>/> 
				<label class="choice" for="etat_8">For Parts Or For Repair</label> </td>
			<td></td>
		  </tr>
		</table>
				</td>
	</tr>


	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: #734c4c; width: 200px;text-align:right"> 	
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape'];?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
		<input type="hidden" name="sku" value="<?echo (string)$_POST['sku'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner'];?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		
		</td>
	  </tr>
    </tbody>
  
  </table>

</form>



</body>
</html>

<?  



// on ferme la connexion à mysql 
mysqli_close($db); ?>