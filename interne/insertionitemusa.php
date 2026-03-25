<?

include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache"); 
//print_r($_POST['accesoires']);
if (isset($_GET['upc']) && $_GET['upc'] != ""){
	(string)$_POST['upc']=(string)$_GET['upc'];
	$_POST['etape']=0;
	$result_upctemp=json_decode(get_from_upctemp($_GET['upc']),true);
	//echo "upc";
	$_POST['priceusd']=$result_upctemp['items'][0]['highest_recorded_price'];
	if($_POST['title']=="" || !isset($_POST['title']))	$_POST['title']=$result_upctemp['items'][0]['title'];
	}
if ($_GET['condition_insert'] != ""){
	$_POST['condition_insert']=$_GET['condition_insert'];
	$_POST['etape']=0;
	}	

//translate('Hello my name is Jonathan');
$upc=(string)$_POST['upc'];
// on se connecte à MySQL 

// on sélectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
//echo $_POST['marketplace_item_id'];
if(isset($_POST['ebay_id_a_cloner'])){
	//echo $_POST['ebay_id_a_cloner'];
	//$data= array();
	//$data['UPC']="dasfdsfasdf";
	//$data['ConditionID']="1000";
	//echo $_POST['ebay_id_a_cloner'];
	$ebayresult=get_ebay_product($connectionapi,$_POST['ebay_id_a_cloner']);

	$_POST['marketplace_item_id']=add_ebay_item($connectionapi,$ebayresult,$_POST,$db); //get_from_upctemp($_POST['upc']),
	//echo "ALLO".$ebay_id."ALLO";
	//echo '<script>window.open("https://bulksell.ebay.com/ws/eBayISAPI.dll?SingleList&sellingMode=SellLikeItem&lineID='.$_POST['ebay_id_a_cloner'].'","ebaylisting")</script>';
}
if(isset($_POST['marketplace_item_id'])){
	$result=get_ebay_product($connectionapi,$_POST['marketplace_item_id']);
	//$ebay_id=add_ebay_item($connectionapi,$result);
	$json = json_decode($result, true);
			$category_id=$json["Item"]["PrimaryCategory"]["CategoryID"];
			//echo addslashes($json["Item"]["Title"]);
			$weight=$json["Item"]["ShippingPackageDetails"]["WeightMajor"];
			$weight2=$json["Item"]["ShippingPackageDetails"]["WeightMinor"];
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
		$ebay_id_n=0;
		$ebay_id_no=0;
		$ebay_id_r=0;
		$ebay_id_o=0;
		$post= array();
		$post= $_POST;
		$result_upctemp=get_from_upctemp($_POST['upc']);
		$post['price_with_shipping']=0;
		if ($ebay_id_o==0){
		if ($_POST['condition_insert']=="n")
		{
				$ebay_id_n=$_POST['marketplace_item_id'];
				$post=$_POST;
			}
			$product_id_princ= insert_item((string)$_POST['upc'],"",$result,$result_upctemp,$ebay_id_n,0,9,$_POST['title'],$post,$db);
			$post['price_with_shipping']=0;
			if($_POST['condition_insert']=="no")
			{
				$ebay_id_no=$_POST['marketplace_item_id'];
				$post=$_POST;
			}

			insert_item((string)$_POST['upc'],"NO",$result,$result_upctemp,$ebay_id_no,$product_id_princ,99,$_POST['title'],$post,$db);
			$post['price_with_shipping']=0;
			if($_POST['condition_insert']=="r")
			{
				$ebay_id_r=$_POST['marketplace_item_id'];
				$post=$_POST;
			}
			insert_item((string)$_POST['upc'],"R",$result,$result_upctemp,$ebay_id_r,$product_id_princ,22,$_POST['title'],$post,$db); 
			$post['price_with_shipping']=0;
		}elseif ($ebay_id_o>0){		
			insert_item((string)$_POST['upc'],$_POST['condition_insert'],$result,$result_upctemp,$ebay_id_r,$product_id_princ,$condition_id,$_POST['title'],$_POST,$db); 
		}

		header("location: multiupload.php?insert=oui&sku=".substr ((string)$_POST['upc'],0,12)); 
		exit(); 
		//$_POST['etape']=2;

}
?>

<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="ffffff">
<form action="insertionitemusa.php?insert=oui&upc=<? echo (string)$_POST['upc'];?>" method="post">

  <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="4" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>image/catalog/cie/entetelow.jpg">
		</td>
      </tr>
      <tr>
	   
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		

        </td>
        <td colspan="4" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
		<h1>Ajouter Produit</h1>
		</td>
     </tr>
	 <tr>
	 		<td rowspan="6" style="vertical-align: center; text-align: center;  height: 24px; width: 200px;">
			</td>
	 		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			 UPC: 
			 <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
        </td>

	  <td>
	  <?echo //browse_ebay($connectionapi,$_POST['title'],(string)$_POST['upc'],"3","3",$db,$_POST['marketplace_item_id']); 
			 browse_ebay($connectionapi,substr((string)$_POST['name'],0,20),(string)$_POST['upc'],"3","3",$db,$_POST['marketplace_item_id'])?>
	  </td>

	</tr>
		<tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		Prix de d&eacute;tail: 
		</td>
		<td colspan="1" rowspan="1" style="vertical-align:  middle;  height: 0px; ">
		(Prix en dollar canadien)
		<input id="price"  type="text" name="price" value="<?echo number_format($_POST['price'], 2,'.', '');?>" size="10" />
		(Prix en dollar usd)
		<input id="price"  type="text" name="priceusd" value="<?echo number_format($_POST['priceusd'], 2,'.', '');?>" size="10" />
		</td>
      </tr>
			 <tr>
		<td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
		NOTRE Prix EBAY
		<td colspan="1" rowspan="1" style="vertical-align: middle; height: 0px; ">
		(finir par <?echo $endprix;?> en dollar am&eacute;ricain)<br><input id="price_with_shipping"  type="text" name="price_with_shipping" value="<?echo number_format($_POST['price_with_shipping'], 2,'.', '');?>" size="10" />
		</td>
</tr>
 <tr>
		<?if (!isset($_POST['ebay_id_a_cloner'])||$_POST['ebay_id_a_cloner']=="")$_POST['ebay_id_a_cloner']= $_POST['marketplace_item_id'];?>
	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			Ebay ID à cloner: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" autofocus>
		</td>
	</tr>

	<tr>

		 <td style="vertical-align:  middle; height: 0px; background-color: #030282; color: white; width: 200px;">
			Titre: 
        </td>
        <td colspan="1" rowspan="1" style="vertical-align:  middle; height: 0px; width: 400px;">
			<input id="title"  type="text" name="title"  value="<?echo $_POST['title'];?>" maxlength="255" >
		</td>
	</tr>



	  <tr>
	    <td colspan="3" style="vertical-align:  middle; height: 16px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 
		<input type="hidden" name="form_id" value="67341" />
		<input type="hidden" name="etape" value="<?echo $_POST['etape'];?>" />
		<input type="hidden" name="categoryarbonum" value="<?echo $categoryarbonum;?>" />
		<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
		<input type="hidden" name="upc" value="<?echo (string)$_POST['upc'];?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="efface_ebayid_cloner" value="<?echo $_POST['efface_ebayid_cloner'];?>" />
		<input type="hidden" name="image" value="<?echo $_POST['image'];?>" />
		<input type="hidden" name="etat" value="9" />
		<input type="hidden" name="condition_insert" value="<?echo $_POST['condition_insert'];?>" />
		<input type="hidden" name="old_token" value="<?echo $_POST['token'];?>" />
		</td>
	  </tr>

    </tbody>
  
  </table>

</form>

  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['upc'];?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo (string)$_POST['title'];?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold2");
</script>

</body>
</html>

<?  



// on ferme la connexion à mysql 
mysqli_close($db); ?>