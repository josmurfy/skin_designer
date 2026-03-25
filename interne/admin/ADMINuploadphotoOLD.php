<?
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db);
/* if ($_GET['product_id']!=""){
	$_POST['product_id'] =$_GET['product_id'];
	$_POST['insert']=$_GET['insert'];
	//echo 'allo';
}
if ($_GET['sku']!=""){
	(string)$_POST['sku'] =$_GET['sku'];
	$_POST['insert']=$_GET['insert']; 
	//echo 'allo';
} */
$export_photo_to_ebay="oui";
	//echo  $_POST['piclink'];
/* if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
	mise_en_page_description($connectionapi,$_POST['product_id'],$db);
header("location: listing.php?sku=".$_GET['sku']); 
exit();		}	 */
	//echo $_FILES['imageprincipale']['size'];
if($_POST['import']=="oui"){
		if ($_FILES['imageprincipale']['size']>0 && $_POST['lien_a_cloner']=="" &&$_POST['sourcecode']=="" && !isset($_POST['maj'])){
			//delete_photo($_POST['product_id'],"principal",$db);

			//print("<pre>".print_r ($_FILES['imageprincipale'],true )."</pre>");
			upload_image($_POST['product_id'],1,$db);
			mise_en_page_description($connectionapi,$_POST['product_id'],$db);
			revise_ebay_product($connectionapi,$_GET['lien_a_cloner'],$_POST['product_id'],"",$db,"oui");

		}
		if((isset($_POST['lien_a_cloner'])&&$_POST['lien_a_cloner']!="") || $_GET['lien_a_cloner']!=""){ 
			
			//echo "link_to_download";
			
			
			if($_GET['lien_a_cloner']!=""){
				link_to_download_with_existing_picture($connectionapi,$_POST['product_id'],$_POST['lien_a_cloner'].$_GET['lien_a_cloner'],$db);
				/* mise_en_page_description($connectionapi,$_POST['product_id'],$db);
				revise_ebay_product($connectionapi,"",$_POST['product_id'],"",$db,"non"); */
				header("location: listing.php?sku=".$_GET['upc']/* ?insert=oui&sku=".(string)$_POST['upc'] */); 
				exit();
			}else{
				link_to_download($connectionapi,$_POST['product_id'],$_POST['lien_a_cloner'].$_GET['lien_a_cloner'],"",$db);
				/* mise_en_page_description($connectionapi,$_POST['product_id'],$db);
				$_POST['lien_a_cloner']="";
				$_POST['efface_ebayid_cloner']=1;
				revise_ebay_product($connectionapi,"",$_POST['product_id'],"",$db,"oui"); */
			}
		}
		if(isset($_POST['sourcecode'])&&$_POST['sourcecode']!=""){
			//echo $_POST['lien_a_cloner'];
			//echo "source";
			echo $_POST['lien_a_cloner'];
			link_to_download($connectionapi,$_POST['product_id'],$_POST['sourcecode'],"sourcecode",$db);
		/* 	mise_en_page_description($connectionapi,$_POST['product_id'],$db);
			revise_ebay_product($connectionapi,'',$_POST['product_id'],"",$db,"oui"); */
			//$_POST['lien_a_cloner']="";
			//$_POST['efface_ebayid_cloner']=1;

		}
				
			/* 	mise_en_page_description($connectionapi,$_POST['product_id'],$db);
				revise_ebay_product($connectionapi,$_GET['lien_a_cloner'],$_POST['product_id'],"",$db,"oui"); */

}
		if(isset($_POST['maj'])){
				foreach($_POST[maj] as $maj)  
					{	
						delete_photo("",$maj,$db);
					}
				//mise_en_page_description($connectionapi,$_POST['product_id'],$db);
			
		}	
/* if ((string)$_POST['sku'] =="" && $_POST['skucheck']==""){ 
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")(string)$_POST['sku'] =$_POST['skucheck']; */

if($_POST['continuer']=="oui"){
		$sql3 = 'UPDATE `oc_product`SET usa="3" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
		$req3 = mysqli_query($db,$sql3);
		mise_en_page_description($connectionapi,$_POST['product_id'],$db);
		$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],'',$db,"non");
		$_POST['product_id']="";
		echo "VERIFIER";
}
if($_POST['corriger']=="oui"){
		$sql3 = 'UPDATE `oc_product`SET usa="9" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
		$req3 = mysqli_query($db,$sql3);
		echo $sql3;
		$_POST['product_id']="";
		echo "A CORRIGER";
}


			//echo (string)$_POST['sku'] ;
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product_description.language_id=1 AND oc_product.product_id=oc_product_description.product_id and `oc_product`.usa=8 and ebay_id>0 and quantity>0 limit 1';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			$_POST['lien_a_cloner']=$data['marketplace_item_id'];
			$_POST['image']=$data['image'];
			$_POST['sku']=$data['sku'];
			$_POST['upc']=$data['upc'];
			$_POST['marketplace_item_id']=$data['marketplace_item_id'];
			$new=1;



?>

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
<body bgcolor="FFFFFF">
<?/* <form id="form_67341" class="appnitro" action="uploadphoto.php?sku=<? echo (string)$_POST['sku'] ;?>" method="post"> */?>
<form action="ADMINuploadphoto.php" method="post" enctype="multipart/form-data" name="addroom">
 <table style="text-align: left; width: 1000px; height: 50%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <td colspan="5" rowspan="1" style="vertical-align:  middle; width: 200px;">
		<img style="width: 488px; height: 145px;" alt="" src="http://www.phoenixliquidation.phoenixdepot.com/image/catalog/cie/entetelow.jpg"><br>
        </td>
      </tr>
      <tr>
	        <tr>
        <td style="vertical-align: middle; background-color: #e4bc03;  text-align: center;"><a href="listing.php?sku=<?echo substr((string)$_GET['sku'],0,12);?>" >Retour au MENU</a><br> 
        </td>
        <td colspan="5" style="height: 50; background-color: #030282; color: white;  text-align: center;">
		<h1>Ajout ou Modification de Photos</h1>
        </td>
      </tr>
	  <tr>
	  <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Titre
	   </td>
	    <td colspan="4" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
				<?echo $_POST['name_product'];?>
	  </td>
	  </tr>
	  <tr>
	  		 <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Sku
	   </td>
        <td colspan="4" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
			<? echo (string)$_POST['sku'] ;?>
		</td>
	 </tr>
	 <tr>
	     <td colspan="5" style="vertical-align:  middle; text-align:center;height: 16px; background-color: #e4bc03; width: 200px;"> 
		
				 <input type="checkbox" name="import" value="oui" />	IMPORTER <input type="checkbox" name="continuer" value="oui" />	VERIFIER <input type="checkbox" name="corriger" value="oui" />	A CORRIGER
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	
		</td>
	 </tr>
<tr>
        <td colspan="5" style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Photo Principale:
		</td>
		</tr>
		<tr>
	         <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
	
				<img src="https://www.phoenixliquidation.ca/image/<? echo $_POST['image'];?>" width="200" >
				<br><input type="file" name="imageprincipale" class="ed">
		</td>
		</tr>

<input type="hidden" name="skucheck" value="<?echo (string)$_POST['sku'] ;?>" />

	  <tr>
	  		 <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Importer par eBay ou lien image
	   </td>
        <td colspan="4" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="lien_a_cloner"  type="text" name="lien_a_cloner"  value="<?echo $_POST['lien_a_cloner'];?>" maxlength="255" autofocus><br>
		</td>
	 </tr>
	  <tr>
	  		 <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Importation par  Walmart
	   </td>
        <td colspan="4" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
			<textarea name="sourcecode" rows="10" cols="50" placeholder="copiez le code source" id="sourcecode" class="form-control"></textarea>
		</td>
	 </tr>
<tr>
        <td colspan="5" style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">
		Photos Suppl&eacute;mentaire:
		</td>
		</tr>
		<tr>
	         <td colspan="5" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
	
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
			</tbody></table>
			<br>
			 <input type="file" name="image[]" multiple class="ed"><br /><br>
			 <input type="submit" name="Submit" value="Upload" id="button1" />
			 <input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
			 <input type="hidden" name="sku" value="<?echo (string)$_POST['sku'] ;?>" />
			 <input type="hidden" name="upload" value="upload" />
			 <input type="hidden" name="insert" value="<?echo $_POST['insert'];?>" />
			 <input type="hidden" name="ebay_id" value="<?echo$_POST['marketplace_item_id'];?>" />
		</td>
		</tr>	
		<tr>
		<td>
		<?if ($_POST['insert']=="oui"){?><a href="modificationitem.php?product_id=<? echo $_POST['product_id'];?>" >SUIVANT</a><?}else{?><a href="listing.php?sku=<? echo (string)$_POST['sku'] ;?>" >TERMINER</a><?}?></h3>
		</td>
		</tr>
    </tbody>
  
  </table>		

 
 </form>
</body>
</html> 
  <script>
window.open("https://www.ebay.com/itm/<?echo $_POST['lien_a_cloner'];?>","ebaysold");
window.open("https://www.upcitemdb.com/upc/<?echo (string)$_POST['upc'];?>","upcitemdb");
  </script>
<? // on ferme la connexion � mysql 

mysqli_close($db); ?>