<?
include '../connection.php';include '../functionload.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
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
//echo count($_POST['maj']);
		if(isset($_POST['maj'])){
			//echo $_POST['importoldwebsite'];
			foreach($_POST[maj] as $maj)  
			{	
				$item=explode(',',$maj);
				$_POST['product_id']=$item[0];
				$_POST['marketplace_item_id']=$item[1];
				//echo "<br>".$_POST['product_id']." ".$_POST['marketplace_item_id'];
				if($_POST['import']=="oui"){

						if((isset($_POST['marketplace_item_id'])&&$_POST['marketplace_item_id']!="")){ 
								delete_photo($_POST['product_id'],"",$db);
								link_to_download($connectionapi,$_POST['product_id'],$_POST['marketplace_item_id'],"",$db);
								
						}
				}
				if($_POST['importoldwebsite']=="oui"){
					echo $_POST['product_id'];
					upload_image_from_old_website($_POST['product_id'],$db);
				}
						
					/* 	mise_en_page_description($connectionapi,$_POST['product_id'],$db);
						revise_ebay_product($connectionapi,$_GET['marketplace_item_id'],$_POST['product_id'],"",$db,"oui"); */
				if($_POST['continuer']=="oui"){
						$sql3 = 'UPDATE `oc_product`SET usa="3" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
						$req3 = mysqli_query($db,$sql3);
						//mise_en_page_description($connectionapi,$_POST['product_id'],$db);
						//$result=revise_ebay_product($connectionapi,$_POST['marketplace_item_id'],$_POST['product_id'],'',$db,"non");
						$_POST['product_id']="";
						//echo "VERIFIER";
				}
				if($_POST['corriger']=="oui"){
						$sql3 = 'UPDATE `oc_product`SET usa="9" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];
						$req3 = mysqli_query($db,$sql3);
						//echo $sql3;
						$_POST['product_id']="";
						//echo "A CORRIGER";
				}
										
			}
}

/* if ((string)$_POST['sku'] =="" && $_POST['skucheck']==""){ 
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")(string)$_POST['sku'] =$_POST['skucheck']; */




			//echo (string)$_POST['sku'] ;
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product_description.language_id=1 AND oc_product.product_id=oc_product_description.product_id and `oc_product`.usa is null and quantity>0 order by oc_product.product_id DESC ';//and ebay_id>0 and quantity>0
	//echo $sql;
			$req = mysqli_query($db,$sql);
			



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
	
				 <input type="checkbox" name="import" value="oui" />	IMPORTER 
				  <input type="checkbox" name="importoldwebsite" value="oui" />	IMPORTER FROM OLD WEBSITE
				  <input type="checkbox" name="continuer" value="oui" />	VERIFIER <input type="checkbox" name="corriger" value="oui" />	A CORRIGER
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" /> 	

<input type="hidden" name="skucheck" value="<?echo (string)$_POST['sku'] ;?>" />

	<br><input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
<table border="1" width="100%">
	
		<th bgcolor="ff6251">
	
Image principal
	</th>
		<th bgcolor="ff6251">
	
Image autre listing
	</th>
	<th bgcolor="ff6251">
	SKU
	</th>
		<th bgcolor="ff6251">
	EBAY ID
	</th>

		<th bgcolor="ff6251"> 
PHOTO Supplementaire
	
	</th>
	</tr>

<?

$itemcount=0;
	while($data = mysqli_fetch_assoc($req)){
		$filename="/home/n7f9655/public_html/phoenixliquidation/image/".$data['image'];
		//echo is_file ($filename);
		//echo $filename;
	//if (!is_file ($filename) && ($_POST['import']!="oui" || $_POST['importoldwebsite']!="oui")){
	?>
						<tr>
						<td >
								<input type="checkbox" name="maj[]" value="<?echo $data['product_id'];?>,<?echo $data['marketplace_item_id'];?>"/>
								<a href="https://www.upcitemdb.com/upc/<?echo (string)$data['upc'];?>"  target='ebay2'>
								<?echo '<img height="50" src="'.$GLOBALS['WEBSITE'].'/image/'.$data['image'].'"/>';?></a>
								<?if (!is_file ($filename))echo '<img height="50" src="https://phoenixliquidation.ca/image/'.$data['image'].'"/>';?>
	
						</td>
						<td>
						<?$sql3 = 'SELECT * FROM `oc_product` where upc like "'.$data['upc'].'" AND sku not like "'.$data['sku'].'"';
					//echo $sql3."<br>";
					$req3 = mysqli_query($db,$sql3);
					while($data3 = mysqli_fetch_assoc($req3)){?>
						
						<a href="https://'.$GLOBALS['WEBSITE'].'/interne/uploadphoto.php?product_id=<?echo $data['product_id'];?>&product_id_cloner=<?echo $data3['product_id'];?>"  target='ebay'><img src="'.$GLOBALS['WEBSITE'].'/image/<?echo $data3['image'];?>" width="50"></a>
						<a href="https://www.ebay.com/itm/<?echo $data3['marketplace_item_id'];?>"  target='ebay'><?echo $data3['marketplace_item_id'];?></a>
					<a href="https://'.$GLOBALS['WEBSITE'].'/interne/uploadphoto.php?product_id=<?echo $data3['product_id'];?>&lien_a_cloner=<?echo $data3['marketplace_item_id'];?>&upc=<?echo $data3['upc'];?>"target='ebay' >Importer photo eBay</a>
						<?$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$data3['product_id'].'"';
					//echo $sql3."<br>";
					$req4 = mysqli_query($db,$sql4);
					while($data4 = mysqli_fetch_assoc($req4)){?>
					<img src="'.$GLOBALS['WEBSITE'].'/image/<?echo $data4['image'];?>" width="50">
					<?}}?>
					
				
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
<a href="'.$GLOBALS['WEBSITE'].'/interne/uploadphoto.php?product_id=<?echo $data['product_id'];?>"  target='ebay'><?echo $data['sku'];?></a>
							
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
<a href="https://www.ebay.com/itm/<?echo $data['marketplace_item_id'];?>"  target='ebay'><?echo $data['marketplace_item_id'];?></a>
							
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
	<table bgcolor="ffffff"> <tbody><tr>
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id='".$data['product_id']."'";
			$req2= mysqli_query($db,$sql2); 
			//echo $sql2;
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
				if($i==5 ||$i==10)echo "</tr><tr>";?>
				<td style="text-align: center;" align="center" valign="middle"><img src="https://phoenixliquidation.phoenixdepot.com/image/<?echo $data2['image'];?>
				" width="50"><br>
				

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
						</td>
						</tr>
				
			<?
			$itemcount++;
	//	}			
		//$j++;
		//echo $j;
		
		
	}
//	}
		?>
</table>
<?
echo "NB a verifier:".$itemcount;

?>
		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
<?if ($_GET['imp']==""){?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interneusa.php" >Retour au MENU</a></h1>
		
<?}?>
 
 </form>
</body>
</html> 

<? // on ferme la connexion � mysql 
/*
	  <tr>
	  		 <td style="vertical-align:  middle; text-align:center;height: 0px; background-color: #030282; color: white; width: 200px;">Importer par eBay ou lien image
	   </td>
        <td colspan="4" rowspan="1" style="vertical-align:  middle; text-align:center;height: 0px; ">
		<input id="ebay_id"  type="text" name="ebay_id"  value="<?echo $_POST['marketplace_item_id'];?>" maxlength="255" autofocus><br>
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
  
  </table>		*/
mysqli_close($db); ?>