<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db);
if ($_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	//echo 'allo';
}

	//echo  $_POST['piclink'];
if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
header("location: listing.php?sku=".$_GET['sku']); 
exit();		}	
if (isset($_FILES['imageprincipale'])){
	upload_image($_POST['product_id'],1);
}
if(isset($_POST['ebay_id_a_cloner'])&&$_POST['ebay_id_a_cloner']!=""){
	//echo $_POST['ebay_id_a_cloner'];
	link_to_download($connectionapi,$_POST['product_id'],$_POST['ebay_id_a_cloner'],"",$db);
	$_POST['ebay_id_a_cloner']="";
	$_POST['efface_ebayid_cloner']=1;
}
if(isset($_POST['sourcecode'])&&$_POST['sourcecode']!=""){
	//echo $_POST['ebay_id_a_cloner'];
	link_to_download($connectionapi,$_POST['product_id'],$_POST['sourcecode'],"sourcecode",$db);
	$_POST['ebay_id_a_cloner']="";
	$_POST['efface_ebayid_cloner']=1;
}


if(isset($_POST['maj'])){
		foreach($_POST[maj] as $maj)  
			{	
				$sql = 'delete from `oc_product_image` where product_image_id='.$maj;
				$req = mysqli_query($db,$sql);
			}
				//$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);
}	
	
if ((string)$_POST['sku'] =="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")(string)$_POST['sku'] =$_POST['skucheck'];

if (isset($_POST['sku'] ) && (string)$_POST['sku'] !=""){
			
			//echo (string)$_POST['sku'] ;
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.(string)$_POST['sku'] .'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			$_POST['image']=$data['image'];
			
			$new=1;
/* 	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ((string)$_POST['sku'] ,0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$_POST['andescription']=$data9['description'];
			$sql8 = 'SELECT * FROM `oc_product_index` where upc="'.substr ((string)$_POST['sku'] ,0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8); */
			


	 $_POST['andescription']=$data9['description'];
	

}

/* if($_POST['upload']=="upload"){
	//print_r($_POST['piclink_aut']);
	foreach($_POST['piclink_aut'] as $piclink_aut)  
	{	
		if($piclink_aut!="")upload_from_link($_POST['product_id'],$piclink_aut,0,$db);
	}
	if($_POST['piclink']!=""){
		upload_from_link($_POST['product_id'],$_POST['piclink'],1,$db);
	}
	

} */?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<style> 
input[type=text] {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
textarea  {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}

select {
    width: 50%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
select:focus {
    border: 3px solid #555;
}

input[type=text]:focus {
    border: 3px solid #555;
}
textarea:focus {
    border: 3px solid #555;
}
</style>
</head>
<body bgcolor="ffffff">
<h1>Ajout ou Modification Photos Item </h1>

<form id="form_67341" class="appnitro" action="multiupload.php?sku=<? echo (string)$_POST['sku'] ;?>" method="post">
<div class="form_description">


</div>
<h3>SKU <?if($new==1){?><a href="multiupload.php" >Changer d'item</a><?}?> <a href="interne.php" >Retour au MENU</a> <a href="listingusa.php?sku=<?echo (string)$_POST['sku'] ?>" >Menu Listing</a></h3>
<h1><?echo (string)$_POST['sku'] ;?></h1>
<input type="hidden" name="skucheck" value="<?echo (string)$_POST['sku'] ;?>" />
<br>
<?if ($new>=1){?>
<svg class="barcode"
	jsbarcode-value="<?echo (string)$_POST['sku'] ;?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>
<script>
JsBarcode(".barcode").init();
</script>
<?
if($picexterne!="")
{
	echo '<br><a href="'.$picexterne.'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
}
?>

</form>

<h3><label class="description" for="element_1">Titre :</label>
		<h1><?echo strtoupper ($_POST['name_product']);?></h1>
<br>
<form action="multiupload.php?insert=oui&sku=<? echo (string)$_POST['sku'] ;?>" method="post" enctype="multipart/form-data" name="addroom">
<h3>Importer du listing conccurent</h3>
<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" autofocus><br>
Importer a partir du code source
<textarea name="sourcecode" rows="10" cols="50" placeholder="copiez le code source" id="sourcecode" class="form-control"></textarea>
 <h3>Image Principale: </h3>
	<table>
		<tr><td style="text-align: center;" align="center" valign="middle">
			<img src="https://www.phoenixsupplies.ca/image/<? echo $_POST['image'];?>" width="200" 
		</td></tr>
	</table>
	<input type="file" name="imageprincipale" class="ed"><br />
	<?/*  <h3>Ajout d'une photo via site</h3> <input id="piclink" type="text" name="piclink" value="" maxlength="255"> */?>

	<br>
 <h3>Autres Images: </h3>

<table bgcolor="ffffff"> <tbody><tr>
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id='".$_POST['product_id']."'";
			$req2= mysqli_query($db,$sql2); 
			//echo $sql2;
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
				if($i==5 ||$i==10)echo "</tr><tr>";?>
				<td style="text-align: center;" align="center" valign="middle"><img src="https://www.phoenixsupplies.ca/image/
				<?
				echo $data2['image'];
				?>
				" width="200"><br>
				<input type="checkbox" name="maj[]" value="<?echo $data2['product_image_id'];?>"/> Supprimer

				</td>
				<?
				$i++;
				}
			}
			$description.='</tbody></table><br>';
?>
</tr>


</tbody>
</tbody></table><br>
 <input type="file" name="image[]" multiple class="ed"><br /><br>
 <input type="submit" name="Submit" value="Upload" id="button1" />
 <input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
 <input type="hidden" name="sku" value="<?echo (string)$_POST['sku'] ;?>" />
 <input type="hidden" name="upload" value="upload" />
 <?}?>
 	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
 </form>
<br />

<br />
<br />
 <h1><a href="interne.php" >Retour au MENU</a></h1>
 <script>
JsBarcode(".barcode").init();

</script>
</body>
</html>
<? // on ferme la connexion � mysql 

mysqli_close($db); ?>