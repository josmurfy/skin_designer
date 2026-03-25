<?
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db);
if ($_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	$_POST['insert']=$_GET['insert'];
	//echo 'allo';
}

	//echo  $_POST['piclink'];
if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
	mise_en_page_description($connectionapi,$_POST['product_id'],$db); 
header("location: listing.php?sku=".$_GET['sku']); 
exit();		}	
	//echo $_FILES['imageprincipale']['size'];
if ($_FILES['imageprincipale']['size']>0 && $_POST['ebay_id_a_cloner']=="" &&$_POST['sourcecode']=="" && !isset($_POST['maj'])){
	//delete_photo($_POST['product_id'],"principal",$db);

	//print("<pre>".print_r ($_FILES['imageprincipale'],true )."</pre>");
	upload_image($_POST['product_id'],1,$db);

}
if(isset($_POST['ebay_id_a_cloner'])&&$_POST['ebay_id_a_cloner']!=""){
	//echo $_POST['ebay_id_a_cloner'];
	//echo "link_to_download";
	link_to_download($connectionapi,$_POST['product_id'],$_POST['ebay_id_a_cloner'],"",$db);
	$_POST['ebay_id_a_cloner']="";
	$_POST['efface_ebayid_cloner']=1;
	
}
if(isset($_POST['sourcecode'])&&$_POST['sourcecode']!=""){
	//echo $_POST['ebay_id_a_cloner'];
	//echo "source";
	link_to_download($connectionapi,$_POST['product_id'],$_POST['sourcecode'],"sourcecode",$db);
	$_POST['ebay_id_a_cloner']="";
	$_POST['efface_ebayid_cloner']=1;

}


if(isset($_POST['maj'])){
		foreach($_POST[maj] as $maj)  
			{	
				delete_photo("",$maj,$db);
			}
		mise_en_page_description($connectionapi,$_POST['product_id'],$db);
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
			


	 //$_POST['andescription']=$data9['description'];
	

}

/* if($_POST['upload']=="upload"){
	//print_r($_POST['piclink_aut']);
	foreach($_POST['piclink_aut'] as $piclink_aut)  
	{	
		if($piclink_aut!="")uploadfromlink($_POST['product_id'],$piclink_aut,0,$db);
	}
	if($_POST['piclink']!=""){
		uploadfromlink($_POST['product_id'],$_POST['piclink'],1,$db);
	}
	

} */?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
  <link href="stylesheet.css" rel="stylesheet">
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
</script>
</head>
<body bgcolor="FF0000">
<h1>Ajout ou Modification Photos Item </h1>
<?if ($_POST['insert']=="oui"){?><a href="modificationitemusa.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >SUIVANT</a><?}else{?><a href="listing.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >TERMINER</a><?}?></h3>

<form id="form_67341" class="appnitro" action="multiupload.php?sku=<? echo (string)$_POST['sku'] ;?>" method="post">
<div class="form_description">


</div>
<h3>SKU </h3>
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
<?if ($_POST['insert']=="oui"){?><a href="modificationitemusa.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >SUIVANT</a><?}else{?><a href="listing.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >TERMINER</a><?}?></h3>
<form action="multiupload.php?insert=oui&sku=<? echo (string)$_POST['sku'] ;?>" method="post" enctype="multipart/form-data" name="addroom">
<h3>Importer du listing conccurent</h3>
<input id="ebay_id_a_cloner"  type="text" name="ebay_id_a_cloner"  value="<?echo $_POST['ebay_id_a_cloner'];?>" maxlength="255" autofocus><br>
<h3>Importer a partir du code source</h3>
<textarea name="sourcecode" rows="10" cols="50" placeholder="copiez le code source" id="sourcecode" class="form-control"></textarea>
 <h3>Image Principale: </h3>
	<table>
		<tr><td style="text-align: center;" align="center" valign="middle">
			<img src="https://www.phoenixliquidation.ca/image/<? echo $_POST['image'];?>" width="200" 
		</td></tr>
	</table>
	<input type="file" name="imageprincipale" class="ed"><br />
	<?/*  <h3>Ajout d'une photo via site</h3> <input id="piclink" type="text" name="piclink" value="" maxlength="255"> */?>

	<br>
 <h3>Autres Images: </h3>
	<input type="button" onclick='selectAll()' value="Select All"/><br>
<table bgcolor="ffffff"> <tbody><tr>
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id='".$_POST['product_id']."'";
			$req2= mysqli_query($db,$sql2); 
			//echo $sql2;
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
				if($i==5 ||$i==10)echo "</tr><tr>";?>
				<td style="text-align: center;" align="center" valign="middle"><img src="https://www.phoenixliquidation.ca/image/
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
 <input type="hidden" name="insert" value="<?echo $_POST['insert'];?>" />
 <?}?>
 
 </form>
<?if ($_POST['insert']=="oui"){?><a href="modificationitemusa.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >SUIVANT</a><?}else{?><a href="listing.php?etat=9&sku=<? echo (string)$_POST['sku'] ;?>&product_id=<? echo $_POST['product_id'];?>&action=default" >TERMINER</a><?}?></h3>
 <script>
JsBarcode(".barcode").init();

</script>
</body>
</html>
<? // on ferme la connexion � mysql 

mysqli_close($db); ?>